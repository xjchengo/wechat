<?php namespace Xjchen\Wechat\Test\Repository;

use Xjchen\Wechat\Repository\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $configArray;
    private $config;

    public function setUp()
    {
        $this->configArray = [
            'cache' => [
                'default' => 'array',
                'prefix' => 'wechat'
            ]
        ];
        $this->config = new Config($this->configArray);
    }

    public function testGetAll()
    {
        $this->assertEquals($this->configArray, $this->config->all());
    }

    public function testGetSpecifiedOne()
    {
        $this->assertEquals($this->configArray['cache']['default'], $this->config->get('cache.default'));
    }

    public function testGetNonExistedOne()
    {
        $this->assertNull($this->config->get('cache.default.non'));
    }

    public function testSetConfig()
    {
        $this->config->set('cache.default', 'file');
        $this->assertEquals('file', $this->config->get('cache.default'));
    }
}
