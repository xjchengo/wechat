<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Service\JsApi as JsApiService;

class JsApiTest extends AbstractServiceTest
{
    private $jsApiService;

    public function setUp()
    {
        parent::setUp();
        $this->jsApiService = new JsApiService($this->config, $this->httpClient);
    }

    public function testGetJsApiTicket()
    {
        $result = $this->jsApiService->getJsApiTicket();
        $this->assertTrue(is_string($result));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetSignatureUrlWrong()
    {
        $params = [
            'url' => 'http://www.echo58.com',
            'noncestr' => rand(100000, 999999),
            'timestamp' => time(),
        ];
        $this->jsApiService->getSignature($params['url'], $params['noncestr'], $params['timestamp']);
    }

    public function testGetSignature()
    {
        $params = [
            'url' => 'http://www.echo58.com:8000/',
            'noncestr' => rand(100000, 999999),
            'timestamp' => time(),
        ];
        $signature = $this->jsApiService->getSignature($params['url'], $params['noncestr'], $params['timestamp']);
        $this->assertTrue(is_string($signature));
    }
}
