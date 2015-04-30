<?php namespace Xjchen\Wechat\HttpClient;

interface HttpInterface
{

    /**
     * The headers we want to send with the request
     *
     * @param string $key
     * @param string $value
     */
    public function addRequestHeader($key, $value);

    /**
     * The headers returned in the response
     *
     * @return array
     */
    public function getResponseHeaders();

    /**
     * The HTTP status response code
     *
     * @return int
     */
    public function getResponseHttpStatusCode();

    /**
     * Sends a request to the server
     *
     * @param string $url The endpoint to send the request to
     * @param string $method The request method
     * @param mixed $payload
     *
     * @return string Raw response from the server
     *
     * @throws \Xjchen\Wechat\Exception\WechatSDKException
     */
    public function send($url, $method = 'GET', $payload = []);

}
