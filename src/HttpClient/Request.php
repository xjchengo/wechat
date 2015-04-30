<?php namespace Xjchen\Wechat\HttpClient;

use UnexpectedValueException;
use InvalidArgumentException;
use Xjchen\Wechat\Exception\WechatSDKException;
use Xjchen\Wechat\Exception\WechatInterfaceException;
use Xjchen\Wechat\Support\Util;

class Request
{
    protected $url;

    protected $method;

    protected $payload;

    protected static $httpClientHandler;

    public static $requestCount = 0;


    /**
     * setHttpClientHandler - Returns an instance of the HTTP client
     * handler
     *
     * @param HttpInterface
     */
    public static function setHttpClientHandler(HttpInterface $handler)
    {
        static::$httpClientHandler = $handler;
    }

    /**
     * getHttpClientHandler - Returns an instance of the HTTP client
     * data handler
     *
     * @return HttpInterface
     */
    public static function getHttpClientHandler()
    {
        if (static::$httpClientHandler) {
            return static::$httpClientHandler;
        }
        if (function_exists('curl_init')) {
            return new CurlHttpClient();
        } else {
            throw new UnexpectedValueException('http client handler is not set');
        }
    }

    public function __construct(Url $url, $method = 'GET', array $payload = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->payload = $payload;
    }

    /**
     * execute - Makes the request to Wechat and returns the result.
     *
     * @param string $dataType
     *
     * @return Response
     *
     * @throws WechatSDKException
     * @throws WechatInterfaceException
     */
    public function execute($dataType = 'json')
    {
        $url = $this->getUrl();
        $payload = $this->getPayload();

        if ($this->method === 'GET' and $payload) {
            $this->url->appendQuery($payload);
            $url = $this->getUrl();
            $payload = [];
        }

        $connection = self::getHttpClientHandler();

        // Should throw `WechatSDKException` exception on HTTP client error.
        // Don't catch to allow it to bubble up.
        $result = $connection->send($url, $this->method, $payload);

        static::$requestCount++;

        $dataType = strtolower($dataType);
        if ($dataType == 'json') {
            $decodedResult = Util::jsonDecode($result, true);
        } elseif ($dataType == 'xml') {
            $decodedResult = Util::xmlDecode($result);
        } else {
            throw new InvalidArgumentException('不支持的数据类型');
        }
        return new Response($this, $decodedResult, $result);
    }

    public function getUrl()
    {
        return $this->url->build();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPayload()
    {
        $payload = $this->payload;
        if (is_string($this->payload)) {
            return [$payload];
        }
        return $payload;
    }
}