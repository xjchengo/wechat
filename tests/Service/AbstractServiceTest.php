<?php namespace Xjchen\Wechat\Test\Service;

use GuzzleHttp\Client;

abstract class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $httpClient;
    protected $config;

    public function setUp()
    {
        $this->config = [
            'appId' => 'wx0167c9df11af7c0c',
            'appSecret' => '31879411de89a4cc2f6cef7b6eb02ae5'
        ];
        $this->httpClient = new Client();
    }
}
