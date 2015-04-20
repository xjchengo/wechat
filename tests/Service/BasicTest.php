<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Service\Basic as BasicService;

class BasicTest extends AbstractServiceTest
{
    private $basicService;

    public function setUp()
    {
        parent::setUp();
        $this->basicService = new BasicService($this->config, $this->httpClient);
    }

    public function testGetAccessToken()
    {
        $result = $this->basicService->getAccessToken();
        $this->assertTrue(is_string($result));
    }

    public function testGetCallbackIp()
    {
        $result = $this->basicService->getCallbackIp();
        $this->assertTrue(is_array($result));
        $this->assertNotEmpty($result);
    }
}
