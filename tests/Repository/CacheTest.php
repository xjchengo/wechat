<?php namespace Xjchen\Wechat\Test\Repository;

use Xjchen\Wechat\Repository\Cache;
use Illuminate\Cache\ArrayStore;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    private $cacheStore;
    private $cache;

    public function setUp()
    {
        $this->cacheStore = new ArrayStore();
        $this->cache = new Cache($this->cacheStore);
    }

    public function testSetCache()
    {
        $this->cache->put('token', '123', 1);
        $this->assertEquals('123', $this->cache->get('token'));
    }
}
