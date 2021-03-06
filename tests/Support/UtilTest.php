<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Support\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{

    public function testIsHttps()
    {
        $_SERVER = [];
        $_SERVER['HTTPS'] = null;
        $this->assertFalse(Util::isHttps());
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue(Util::isHttps());
    }

    public function testGetCurrentUrl()
    {
        $expected = 'http://test.dev/test.php?foo=bar';
        $expectedPort = 'http://test.dev:443/test.php?foo=bar';
        $expectedPort2 = 'https://test.dev:80/test.php?foo=bar';
        $expectedSSL = 'https://test.dev/test.php?foo=bar';

        $_SERVER = [];
        $_SERVER['HTTP_HOST'] = 'test.dev';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/test.php?foo=bar';
        $_SERVER['QUERY_STRING'] = 'foo=bar';
        $_SERVER['PHP_SELF'] = '/test.php';

        // Test regular.
        $this->assertEquals($expected, Util::getCurrentUrl());

        // Test port.
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals($expectedPort, Util::getCurrentUrl());

        // Test SSL.
        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals($expectedSSL, Util::getCurrentUrl());
        $_SERVER['SERVER_PORT'] = 80;
        $this->assertEquals($expectedPort2, Util::getCurrentUrl());
        unset($_SERVER['HTTPS']);

        // Test no $_SERVER['REQUEST_URI'] (e.g., MS IIS).
        unset($_SERVER['REQUEST_URI']);
        $this->assertEquals($expected, Util::getCurrentUrl());
    }

    public function testIsInWechatApp()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'TEST';
        $this->assertFalse(Util::isInWechatApp());
        $_SERVER['HTTP_USER_AGENT'] = 'mozilla/5.0 (iphone; cpu iphone os 5_1_1 like mac os x) applewebkit/534.46 (khtml, like gecko) mobile/9b206 micromessenger/5.0';
        $this->assertTrue(Util::isInWechatApp());
    }

    public function testIsInIOS()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'TEST';
        $this->assertFalse(Util::isInIOS());
        $_SERVER['HTTP_USER_AGENT'] = 'mozilla/5.0 (iphone; cpu iphone os 5_1_1 like mac os x) applewebkit/534.46 (khtml, like gecko) mobile/9b206 micromessenger/5.0';
        $this->assertTrue(Util::isInIOS());
    }

    public function testCamelCase()
    {
        $this->assertEquals('fooBar', Util::camel('FooBar'));
        $this->assertEquals('fooBar', Util::camel('foo_bar'));
        $this->assertEquals('fooBarBaz', Util::camel('Foo-barBaz'));
        $this->assertEquals('fooBarBaz', Util::camel('foo-bar_baz'));
    }
    public function testStudlyCase()
    {
        $this->assertEquals('FooBar', Util::studly('fooBar'));
        $this->assertEquals('FooBar', Util::studly('foo_bar'));
        $this->assertEquals('FooBarBaz', Util::studly('foo-barBaz'));
        $this->assertEquals('FooBarBaz', Util::studly('foo-bar_baz'));
    }

    public function testJsonDecode()
    {
        $json = [
            'a' => [
                'b' => 5
            ]
        ];
        $this->assertEquals($json, Util::jsonDecode(json_encode($json), true));
    }

    public function testArrayToXml()
    {
        $xml = [
            'a' => 5,
            'b' => 'abc'
        ];
        $xmlString = '<xml><a>5</a><b><![CDATA[abc]]></b></xml>';

        $this->assertEquals($xmlString, Util::arrayToXml($xml));
    }

    public function testXmlDecode()
    {
        $xml = [
            'a' => 5,
            'b' => 'abc'
        ];
        $xmlString = '<xml><a>5</a><b><![CDATA[abc]]></b></xml>';

        $this->assertEquals($xml, Util::xmlDecode($xmlString));
    }

    public function testGenerateRandomString()
    {
        $charset = 'abcdedfhijklmn';
        $length = 32;
        $randomString = Util::generateRandomString($length, $charset);

        $this->assertEquals($length, strlen($randomString));

        $diff = array_diff(str_split($randomString), str_split($charset));
        $this->assertTrue(empty($diff));
    }
}
