<?php namespace Xjchen\Wechat\HttpClient;

use Xjchen\Wechat\Exception\WechatSDKException;

class CurlHttpClient implements HttpInterface
{

    /**
     * @var array The headers to be sent with the request
     */
    protected $requestHeaders = array();

    /**
     * @var array The headers received from the response
     */
    protected $responseHeaders = array();

    /**
     * @var int The HTTP status code returned from the server
     */
    protected $responseHttpStatusCode = 0;

    /**
     * @var string The client error message
     */
    protected $curlErrorMessage = '';

    /**
     * @var int The curl client error code
     */
    protected $curlErrorCode = 0;

    /**
     * @var string|boolean The raw response from the server
     */
    protected $rawResponse;

    /**
     * @var Curl Procedural curl as object
     */
    protected $curl;

    /**
     * @const Curl Version which is unaffected by the proxy header length error.
     */
    const CURL_PROXY_QUIRK_VER = 0x071E00;

    /**
     * @const "Connection Established" header text
     */
    const CONNECTION_ESTABLISHED = "HTTP/1.0 200 Connection established\r\n\r\n";

    /**
     * @param Curl|null Procedural curl as object
     */
    public function __construct(Curl $curl = null)
    {
        $this->curl = $curl ?: new Curl();
    }

    /**
     * The headers we want to send with the request
     *
     * @param string $key
     * @param string $value
     */
    public function addRequestHeader($key, $value)
    {
        $this->requestHeaders[$key] = $value;
    }

    /**
     * The headers returned in the response
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * The HTTP status response code
     *
     * @return int
     */
    public function getResponseHttpStatusCode()
    {
        return $this->responseHttpStatusCode;
    }

    /**
     * Sends a request to the server
     *
     * @param string $url The endpoint to send the request to
     * @param string $method The request method
     * @param array  $parameters The key value pairs to be sent in the body
     *
     * @return string Raw response from the server
     *
     * @throws \Xjchen\Wechat\Exception\WechatSDKException
     */
    public function send($url, $method = 'GET', $parameters = array())
    {
        $this->openConnection($url, $method, $parameters);
        $this->tryToSendRequest();

        if ($this->curlErrorCode) {
            throw new WechatSDKException($this->curlErrorMessage, $this->curlErrorCode);
        }

        // Separate the raw headers from the raw body
        list($rawHeaders, $rawBody) = $this->extractResponseHeadersAndBody();

        $this->responseHeaders = self::headersToArray($rawHeaders);

        $this->closeConnection();

        return $rawBody;
    }

    /**
     * Opens a new curl connection
     *
     * @param string $url The endpoint to send the request to
     * @param string $method The request method
     * @param array  $parameters The key value pairs to be sent in the body
     */
    public function openConnection($url, $method = 'GET', array $parameters = array())
    {
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true, // Enable header processing
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO         => __DIR__ . '/certs/rootca.pem',
        );

        if ($method !== 'GET') {
            $options[CURLOPT_POSTFIELDS] = !$this->paramsHaveFile($parameters) ? http_build_query($parameters, null, '&') : $parameters;
        }
        if ($method === 'DELETE' || $method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }

        if (count($this->requestHeaders) > 0) {
            $options[CURLOPT_HTTPHEADER] = $this->compileRequestHeaders();
        }

        $this->curl->init();
        $this->curl->setopt_array($options);
    }

    /**
     * Closes an existing curl connection
     */
    public function closeConnection()
    {
        $this->curl->close();
    }

    /**
     * Try to send the request
     */
    public function tryToSendRequest()
    {
        $this->sendRequest();
        $this->curlErrorMessage = $this->curl->error();
        $this->curlErrorCode = $this->curl->errno();
        $this->responseHttpStatusCode = $this->curl->getinfo(CURLINFO_HTTP_CODE);
    }

    /**
     * Send the request and get the raw response from curl
     */
    public function sendRequest()
    {
        $this->rawResponse = $this->curl->exec();
    }

    /**
     * Compiles the request headers into a curl-friendly format
     *
     * @return array
     */
    public function compileRequestHeaders()
    {
        $return = array();

        foreach ($this->requestHeaders as $key => $value) {
            $return[] = $key . ': ' . $value;
        }

        return $return;
    }

    /**
     * Extracts the headers and the body into a two-part array
     *
     * @return array
     */
    public function extractResponseHeadersAndBody()
    {
        $headerSize = self::getHeaderSize();

        $rawHeaders = mb_substr($this->rawResponse, 0, $headerSize);
        $rawBody = mb_substr($this->rawResponse, $headerSize);

        return array(trim($rawHeaders), trim($rawBody));
    }

    /**
     * Converts raw header responses into an array
     *
     * @param string $rawHeaders
     *
     * @return array
     */
    public static function headersToArray($rawHeaders)
    {
        $headers = array();

        // Normalize line breaks
        $rawHeaders = str_replace("\r\n", "\n", $rawHeaders);

        // There will be multiple headers if a 301 was followed
        // or a proxy was followed, etc
        $headerCollection = explode("\n\n", trim($rawHeaders));
        // We just want the last response (at the end)
        $rawHeader = array_pop($headerCollection);

        $headerComponents = explode("\n", $rawHeader);
        foreach ($headerComponents as $line) {
            if (strpos($line, ': ') === false) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * Return proper header size
     *
     * @return integer
     */
    private function getHeaderSize()
    {
        $headerSize = $this->curl->getinfo(CURLINFO_HEADER_SIZE);

        return $headerSize;
    }

    /**
     * Detect if the params have a file to upload.
     *
     * @param array $params
     *
     * @return boolean
     */
    private function paramsHaveFile(array $params)
    {
        foreach ($params as $value) {
            if ($value instanceof \CURLFile) {
                return true;
            }
        }

        return false;
    }
}
