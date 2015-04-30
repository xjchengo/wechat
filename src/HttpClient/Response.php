<?php namespace Xjchen\Wechat\HttpClient;

class Response
{
    /**
     * @var Request The request which produced this response
     */
    protected $request;

    /**
     * @var array The decoded response from the Wechat API
     */
    protected $responseData;

    /**
     * @var string The raw response from the Wechat API
     */
    protected $rawResponse;


    public function __construct($request, $responseData, $rawResponse)
    {
        $this->request = $request;
        $this->responseData = $responseData;
        $this->rawResponse = $rawResponse;
    }

    /**
     * Returns the request which produced this response.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the decoded response data.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->responseData;
    }

    /**
     * Returns the raw response
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public function getResponseHeaders()
    {
        return $this->getRequest()->getResponseHeaders();
    }
}