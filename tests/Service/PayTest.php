<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Support\Util;
use Xjchen\Wechat\Service\Pay as PayService;

class PayTest extends AbstractServiceTest
{
    private $payService;

    public function setUp()
    {
        parent::setUp();
        $config = require __DIR__.'/../.config.php';
        $this->payService = new PayService($config, $this->httpClient);
    }

    public function testGeneratePaySignature()
    {
        $params = [
            'appid' => 'wxd930ea5d5a258f4f',
            'body' => 'test',
            'device_info' => '1000',
            'mch_id' => '10000100',
            'nonce_str' => 'ibuaiVcKdpRxkhJA',
        ];
        $key = '192006250b4c09247ec02edce69f6a2d';

        $config = $this->payService->getConfigRepository();
        $config['key'] = $key;
        $this->payService->setConfigRepository($config);

        $payService = $this->payService;
        $signature = $payService::generatePaySignature($params);
        $this->assertEquals('9A0A8659F005D6984697E2CA0A9CF3B7', $signature);
    }

    public function testCreateOrder()
    {
        $outTradeNo = date('mdHis').sprintf('%03d', 1).rand(1000,9999);
        $totalFee = 1;
        $body = 'test';
        $nonceStr = Util::generateRandomString();
        $clientIp = '127.0.0.1';
        $notifyUrl = 'http://www.echo58.com';
        $tradeType = 'JSAPI';
        $openid = 'oB-GHjj0Fru3VK32r0xFJ_kIO_B8';
        $order = $this->payService->createOrder($outTradeNo, $totalFee, $body, $nonceStr, $clientIp, $notifyUrl, $tradeType, $openid);
        $this->assertArrayHasKey('prepay_id', $order);
    }

    public function testGenerateNotifyReplyWithoutMessage()
    {
        $code = 'SUCCESS';
        $payService = $this->payService;
        $reply = $payService::generateNotifyReply($code);
        $this->assertEquals('<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>', $reply);
    }

    public function testGenerateNotifyReplyWithMessage()
    {
        $code = 'SUCCESS';
        $message = 'invalid signature';
        $payService = $this->payService;
        $reply = $payService::generateNotifyReply($code, $message);
        $this->assertEquals('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[invalid signature]]></return_msg></xml>', $reply);
    }
}
