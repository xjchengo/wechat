<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Service\TemplateMessage as TemplateMessageService;

class TemplateMessageTest extends AbstractServiceTest
{
    private $templateMessageService;
    private $message;

    public function setUp()
    {
        parent::setUp();
        $this->templateMessageService = new TemplateMessageService($this->config, $this->httpClient);
        $this->message = [
            'touser' => 'oED6jjiBqSRq5U-B0AVHd2pSAhYg',
            'template_id' => 'EYmxREqWvDvm9YOMn9tXbqfnFzMhc74PwpEH10J4b80',
            'url' => 'http://www.echo58.com',
            'topcolor' => '#FF0000',
            'data' => [
                'time' => [
                    'value' => time(),
                    'color' => '#173177'
                ]
            ]
        ];
    }

    public function testSend()
    {
        $result = $this->templateMessageService->send($this->message);
        $this->assertTrue(is_numeric($result));
    }
}
