<?php namespace Xjchen\Wechat\Test\HttpClient;

use Mockery as m;
use Xjchen\Wechat\HttpClient\Curl;
use Xjchen\Wechat\HttpClient\CurlHttpClient;

class CurlHttpClientTest extends \PHPUnit_Framework_TestCase
{

    protected $curlMock;
    protected $curlClient;

    protected $fakeRawHeader = "HTTP/1.1 200 OK
Etag: \"9d86b21aa74d74e574bbb35ba13524a52deb96e3\"
Content-Type: text/javascript; charset=UTF-8
X-FB-Rev: 9244768
Pragma: no-cache
Expires: Sat, 01 Jan 2000 00:00:00 GMT
Connection: close
Date: Mon, 19 May 2014 18:37:17 GMT
X-FB-Debug: 02QQiffE7JG2rV6i/Agzd0gI2/OOQ2lk5UW0=
Content-Length: 29
Cache-Control: private, no-cache, no-store, must-revalidate
Access-Control-Allow-Origin: *\r\n\r\n";

    protected $fakeRawBody = "{\"id\":\"123\",\"name\":\"Foo Bar\"}";

    protected $fakeHeadersAsArray = array(
        'http_code' => 'HTTP/1.1 200 OK',
        'Etag' => '"9d86b21aa74d74e574bbb35ba13524a52deb96e3"',
        'Content-Type' => 'text/javascript; charset=UTF-8',
        'X-FB-Rev' => '9244768',
        'Pragma' => 'no-cache',
        'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
        'Connection' => 'close',
        'Date' => 'Mon, 19 May 2014 18:37:17 GMT',
        'X-FB-Debug' => '02QQiffE7JG2rV6i/Agzd0gI2/OOQ2lk5UW0=',
        'Content-Length' => '29',
        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
        'Access-Control-Allow-Origin' => '*',
    );

    public function setUp()
    {
        $this->curlMock = m::mock('Xjchen\Wechat\HttpClient\Curl');
        $this->curlClient = new CurlHttpClient($this->curlMock);
    }

    public function tearDown()
    {
        m::close();
        (new CurlHttpClient()); // Resets the static dependency injection
    }

    public function testCanOpenGetCurlConnection()
    {
        $this->curlMock
            ->shouldReceive('init')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('setopt_array')
            ->with(m::on(function($arg) {
                $caInfo = array_diff($arg, [
                    CURLOPT_CUSTOMREQUEST  => 'GET',
                    CURLOPT_URL            => 'http://foo.com',
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT        => 60,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER         => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_SSL_VERIFYPEER => true,
                ]);

                if (count($caInfo) !== 1) {
                    return false;
                }

                if (1 !== preg_match('/.+\/certs\/rootca\.pem$/', $caInfo[CURLOPT_CAINFO])) {
                    return false;
                }

                return true;
            }))
            ->once()
            ->andReturn(null);

        $this->curlClient->openConnection('http://foo.com', 'GET', array());
    }

    public function testCanOpenGetCurlConnectionWithHeaders()
    {
        $this->curlMock
            ->shouldReceive('init')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('setopt_array')
            ->with(m::on(function($arg) {

                // array_diff() will sometimes trigger error on multidimensional arrays
                if (['X-foo: bar'] !== $arg[CURLOPT_HTTPHEADER]) {
                    return false;
                }
                unset($arg[CURLOPT_HTTPHEADER]);

                $caInfo = array_diff($arg, [
                    CURLOPT_CUSTOMREQUEST  => 'GET',
                    CURLOPT_URL            => 'http://foo.com',
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT        => 60,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER         => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_SSL_VERIFYPEER => true,
                ]);

                if (count($caInfo) !== 1) {
                    return false;
                }

                if (1 !== preg_match('/.+\/certs\/rootca\.pem$/', $caInfo[CURLOPT_CAINFO])) {
                    return false;
                }

                return true;
            }))
            ->once()
            ->andReturn(null);

        $this->curlClient->addRequestHeader('X-foo', 'bar');
        $this->curlClient->openConnection('http://foo.com', 'GET', array());
    }

    public function testCanOpenPostCurlConnection()
    {
        $this->curlMock
            ->shouldReceive('init')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('setopt_array')
            ->with(m::on(function($arg) {
                $caInfo = array_diff($arg, [
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_URL            => 'http://bar.com',
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT        => 60,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER         => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_POSTFIELDS     => 'baz=bar&foo%5B0%5D=1&foo%5B1%5D=2&foo%5B2%5D=3',
                ]);

                if (count($caInfo) !== 1) {
                    return false;
                }

                if (1 !== preg_match('/.+\/certs\/rootca\.pem$/', $caInfo[CURLOPT_CAINFO])) {
                    return false;
                }

                return true;
            }))
            ->once()
            ->andReturn(null);

        // Prove can support multidimensional params
        $params = array(
            'baz' => 'bar',
            'foo' => array(1, 2, 3),
        );
        $this->curlClient->openConnection('http://bar.com', 'POST', $params);
    }

    public function testCanCloseConnection()
    {
        $this->curlMock
            ->shouldReceive('close')
            ->once()
            ->andReturn(null);

        $this->curlClient->closeConnection();
    }

    public function testTrySendRequest()
    {
        $this->curlMock
            ->shouldReceive('exec')
            ->once()
            ->andReturn('foo response');
        $this->curlMock
            ->shouldReceive('errno')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('error')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('getinfo')
            ->with(CURLINFO_HTTP_CODE)
            ->once()
            ->andReturn(200);

        $this->curlClient->tryToSendRequest();
    }

    public function testProperlyCompilesRequestHeaders()
    {
        $headers = $this->curlClient->compileRequestHeaders();
        $expectedHeaders = array();
        $this->assertEquals($expectedHeaders, $headers);

        $this->curlClient->addRequestHeader('X-foo', 'bar');
        $headers = $this->curlClient->compileRequestHeaders();
        $expectedHeaders = array(
            'X-foo: bar',
        );
        $this->assertEquals($expectedHeaders, $headers);

        $this->curlClient->addRequestHeader('X-bar', 'baz');
        $headers = $this->curlClient->compileRequestHeaders();
        $expectedHeaders = array(
            'X-foo: bar',
            'X-bar: baz',
        );
        $this->assertEquals($expectedHeaders, $headers);
    }

    public function testIsolatesTheHeaderAndBody()
    {
        $this->curlMock
            ->shouldReceive('getinfo')
            ->with(CURLINFO_HEADER_SIZE)
            ->once()
            ->andReturn(strlen($this->fakeRawHeader));
        $this->curlMock
            ->shouldReceive('exec')
            ->once()
            ->andReturn($this->fakeRawHeader . $this->fakeRawBody);

        $this->curlClient->sendRequest();
        list($rawHeader, $rawBody) = $this->curlClient->extractResponseHeadersAndBody();

        $this->assertEquals($rawHeader, trim($this->fakeRawHeader));
        $this->assertEquals($rawBody, $this->fakeRawBody);
    }

    public function testConvertsRawHeadersToArray()
    {
        $headers = CurlHttpClient::headersToArray($this->fakeRawHeader);

        $this->assertEquals($headers, $this->fakeHeadersAsArray);
    }

    public function testCanSendNormalRequest()
    {
        $this->curlMock
            ->shouldReceive('init')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('setopt_array')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('exec')
            ->once()
            ->andReturn($this->fakeRawHeader . $this->fakeRawBody);
        $this->curlMock
            ->shouldReceive('errno')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('error')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('getinfo')
            ->with(CURLINFO_HTTP_CODE)
            ->once()
            ->andReturn(200);
        $this->curlMock
            ->shouldReceive('getinfo')
            ->with(CURLINFO_HEADER_SIZE)
            ->once()
            ->andReturn(mb_strlen($this->fakeRawHeader));
        $this->curlMock
            ->shouldReceive('close')
            ->once()
            ->andReturn(null);

        $responseBody = $this->curlClient->send('http://foo.com/');

        $this->assertEquals($responseBody, $this->fakeRawBody);
        $this->assertEquals($this->curlClient->getResponseHeaders(), $this->fakeHeadersAsArray);
        $this->assertEquals(200, $this->curlClient->getResponseHttpStatusCode());
    }

    /**
     * @expectedException \Xjchen\Wechat\Exception\WechatSDKException
     */
    public function testThrowsExceptionOnClientError()
    {
        $this->curlMock
            ->shouldReceive('init')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('setopt_array')
            ->once()
            ->andReturn(null);
        $this->curlMock
            ->shouldReceive('exec')
            ->once()
            ->andReturn(false);
        $this->curlMock
            ->shouldReceive('errno')
            ->once()
            ->andReturn(123);
        $this->curlMock
            ->shouldReceive('error')
            ->once()
            ->andReturn('Foo error');
        $this->curlMock
            ->shouldReceive('getinfo')
            ->with(CURLINFO_HTTP_CODE)
            ->once()
            ->andReturn(null);

        $this->curlClient->send('http://foo.com/');
    }
}
