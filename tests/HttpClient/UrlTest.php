<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\HttpClient\Url;
use Mockery as m;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    protected $url;

    public function setUp()
    {

    }

    public function tearDown()
    {
        m::close();
    }

    public function setAccessToken()
    {
        $accessToken = m::mock('Xjchen\Wechat\Entity\AccessToken');
        $accessToken->shouldReceive('get')->andReturn('test_access_token');
        Url::setAccessToken($accessToken);
    }

    public function testParseStringQuery()
    {
        $expectedArray = [
            'a' => 1,
            'b' => 2
        ];

        $queryString = 'a=1&b=2';
        $queryArray = Url::parseQuery($queryString);
        $this->assertEquals($expectedArray, $queryArray);

        $queryString = '?a=1&b=2';
        $queryArray = Url::parseQuery($queryString);
        $this->assertEquals($expectedArray, $queryArray);

        $queryString = '?a=1&b=2&';
        $queryArray = Url::parseQuery($queryString);
        $this->assertEquals($expectedArray, $queryArray);
    }

    public function testParseArrayQuery()
    {
        $expectedArray = [
            'a' => 1,
            'b' => 2
        ];
        $queryArray = [
            'a' => 1,
            'b' => 2
        ];

        $queryArray = Url::parseQuery($queryArray);

        $this->assertEquals($expectedArray, $queryArray);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseQueryWrong()
    {
        $query = 123;
        Url::parseQuery($query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildQueryWrongType()
    {
        $queryArray = [
            'a' => 1,
            'b' => 2
        ];
        Url::buildQuery($queryArray, 'wrong_type');
    }

    public function testBuildQueryDefault()
    {
        $queryArray = [
            'foo' => 'bar',
            'baz' => 'boom',
            'cow' => 'milk',
            'php' => 'hypertext processor'
        ];
        $this->assertEquals(http_build_query($queryArray, null, '&', PHP_QUERY_RFC1738), Url::buildQuery($queryArray));
    }

    public function testBuildQueryRFC1738()
    {
        $queryArray = [
            'foo' => 'bar',
            'baz' => 'boom',
            'cow' => 'milk',
            'php' => 'hypertext processor'
        ];
        $this->assertEquals(http_build_query($queryArray, null, '&', PHP_QUERY_RFC1738), Url::buildQuery($queryArray, PHP_QUERY_RFC1738));
    }

    public function testBuildQueryRFC3986()
    {
        $queryArray = [
            'foo' => 'bar',
            'baz' => 'boom',
            'cow' => 'milk',
            'php' => 'hypertext processor'
        ];
        $this->assertEquals(http_build_query($queryArray, null, '&', PHP_QUERY_RFC3986), Url::buildQuery($queryArray, PHP_QUERY_RFC3986));
    }

    public function testBuildQueryRaw()
    {
        $queryArray = [
            'cow' => 'milk',
            'php' => 'hypertext processor'
        ];
        $this->assertEquals('cow=milk&php=hypertext processor', Url::buildQuery($queryArray, false));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetAccessTokenWrong()
    {
        Url::getAccessToken();
    }

    public function testGetAccessToken()
    {
        $this->setAccessToken();
        $accessToken = Url::getAccessToken();
        $this->assertEquals('test_access_token', $accessToken->get());
    }

    public function testConstructNotEmptyPathWithoutAccessToken()
    {
        $url = new Url('/test', 'a=1');
        $this->assertEquals('https://api.weixin.qq.com/test?a=1', $url->build(false));

        $url = new Url('/test');
        $this->assertEquals('https://api.weixin.qq.com/test', $url->build(false));
    }

    public function testConstructNotEmptyPathWithAccessToken()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1');
        $this->assertEquals('https://api.weixin.qq.com/test?a=1&access_token=test_access_token', $url->build());

        $url = new Url('/test');
        $this->assertEquals('https://api.weixin.qq.com/test?access_token=test_access_token', $url->build());
    }

    public function testConstructWithAccessTokenSet()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1', false);
        $this->assertEquals('https://api.weixin.qq.com/test?a=1', $url->build());

        $url = new Url('/test', null, true);
        $this->assertEquals('https://api.weixin.qq.com/test?access_token=test_access_token', $url->build());
    }

    public function testConstructFullParams()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1', false, 'www.echo58.com', 'http');
        $this->assertEquals('http://www.echo58.com/test?a=1', $url->build());

        $url = new Url('/test', 'a=1', true, 'www.echo58.com', 'http');
        $this->assertEquals('http://www.echo58.com/test?a=1&access_token=test_access_token', $url->build());
    }

    public function testToString()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1', false, 'www.echo58.com', 'http');
        $this->assertEquals('http://www.echo58.com/test?a=1', (string)$url);

        $url = new Url('/test', 'a=1', true, 'www.echo58.com', 'http');
        $this->assertEquals('http://www.echo58.com/test?a=1&access_token=test_access_token', (string)$url);
    }

    public function testSet()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1');
        $url->setScheme('http');
        $url->setHost('www.echo58.com');
        $url->setPath('/test-set');
        $url->setQuery('b=2');
        $this->assertEquals('http://www.echo58.com/test-set?b=2&access_token=test_access_token', (string)$url);
    }

    public function testAppendQueryString()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1');
        $url->appendQuery('b=2');

        $this->assertEquals('https://api.weixin.qq.com/test?a=1&b=2&access_token=test_access_token', (string)$url);

        $url = new Url('/test', 'a=1');
        $url->appendQuery('a=2');

        $this->assertEquals('https://api.weixin.qq.com/test?a=2&access_token=test_access_token', (string)$url);
    }

    public function testAppendQueryArray()
    {
        $this->setAccessToken();

        $url = new Url('/test', 'a=1');
        $url->appendQuery([
            'b' => 2
        ]);

        $this->assertEquals('https://api.weixin.qq.com/test?a=1&b=2&access_token=test_access_token', (string)$url);

        $url = new Url('/test', 'a=1');
        $url->appendQuery([
            'a' => 2
        ]);

        $this->assertEquals('https://api.weixin.qq.com/test?a=2&access_token=test_access_token', (string)$url);
    }
}
