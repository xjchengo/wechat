<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\HttpClient\Request;
use Xjchen\Wechat\HttpClient\CurlHttpClient;
use Xjchen\Wechat\Support\Util;
use Mockery as m;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $url;

    public function setUp()
    {

    }

    public function tearDown()
    {
        m::close();
    }

    public function testSetAndGetHttpClientHandler()
    {
        $curlHttpClient = new CurlHttpClient();
        Request::setHttpClientHandler($curlHttpClient);
        $this->assertEquals($curlHttpClient, Request::getHttpClientHandler());
    }

    public function testGetUrl()
    {
        $expectedUrl = 'http://www.echo58.com/test';
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $url->shouldReceive('build')->andReturn($expectedUrl);
        $request = new Request($url, 'GET');
        $this->assertEquals($expectedUrl, $request->getUrl());
    }

    public function testMethod()
    {
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $request = new Request($url, 'GET');
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testPayload()
    {
        $array = [
            'a' => 5,
            'b' => 6
        ];
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $request = new Request($url, 'GET', [json_encode($array)]);
        $this->assertEquals([json_encode($array)], $request->getPayload());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteWrongType()
    {
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $url->shouldReceive('build')->andReturn('http://www.echo58.com');
        $request = new Request($url, 'GET');
        $request->execute('wrong');
    }

    public function testExecuteGetJson()
    {
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $url->shouldReceive('build')->andReturn('http://www.echo58.com', 'http://www.echo58.com?a=5');
        $url->shouldReceive('appendQuery');
        $curlHttpClient = m::mock('Xjchen\Wechat\HttpClient\CurlHttpClient');
        $curlHttpClient->shouldReceive('send')->once()->with(
            'http://www.echo58.com?a=5',
            'GET',
            []
        )->andReturn(json_encode(['a'=> 5]));
        Request::setHttpClientHandler($curlHttpClient);
        $request = new Request($url, 'GET', ['a'=>5]);
        $response = $request->execute('json');
        $this->assertEquals(['a'=>5], $response->getResponse());
    }

    public function testExecuteGetXml()
    {
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $url->shouldReceive('build')->andReturn('http://www.echo58.com', 'http://www.echo58.com?a=5');
        $url->shouldReceive('appendQuery');
        $curlHttpClient = m::mock('Xjchen\Wechat\HttpClient\CurlHttpClient');
        $curlHttpClient->shouldReceive('send')->once()->with(
            'http://www.echo58.com?a=5',
            'GET',
            []
        )->andReturn(Util::arrayToXml(['a'=> 5]));
        Request::setHttpClientHandler($curlHttpClient);
        $request = new Request($url, 'GET', ['a'=>5]);
        $response = $request->execute('xml');
        $this->assertEquals(['a'=>5], $response->getResponse());
    }

    public function testExecutePostJson()
    {
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $url->shouldReceive('build')->andReturn('http://www.echo58.com?a=5');
        $url->shouldReceive('appendQuery');
        $curlHttpClient = m::mock('Xjchen\Wechat\HttpClient\CurlHttpClient');
        $curlHttpClient->shouldReceive('send')->once()->with(
            'http://www.echo58.com?a=5',
            'POST',
            ['b'=>5]
        )->andReturn(json_encode(['b'=> 5]));
        Request::setHttpClientHandler($curlHttpClient);
        $request = new Request($url, 'POST', ['b'=>5]);
        $response = $request->execute('json');
        $this->assertEquals(['b'=>5], $response->getResponse());
    }

    public function testExecutePostXml()
    {
        $url = m::mock('Xjchen\Wechat\HttpClient\Url');
        $url->shouldReceive('build')->andReturn('http://www.echo58.com?a=5');
        $url->shouldReceive('appendQuery');
        $curlHttpClient = m::mock('Xjchen\Wechat\HttpClient\CurlHttpClient');
        $curlHttpClient->shouldReceive('send')->once()->with(
            'http://www.echo58.com?a=5',
            'POST',
            ['b'=>5]
        )->andReturn(Util::arrayToXml(['b'=> 5]));
        Request::setHttpClientHandler($curlHttpClient);
        $request = new Request($url, 'POST', ['b'=>5]);
        $response = $request->execute('xml');
        $this->assertEquals(['b'=>5], $response->getResponse());
    }
}
