<?php namespace Xjchen\Wechat\Test\Repository;

use Illuminate\Cache\RedisStore;
use Illuminate\Redis\Database;

class RedisCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $store;
    protected $redisCache;

    public function setUp()
    {
        $this->store = new RedisStore(new Database(
            [
                'cluster' => false,
                'default' => [
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0,
                ],
            ]
        ));
        $this->redisCache = new RedisCache($this->store);
    }

    public function testSet()
    {
        $this->redisCache->put('xjchen.wechat.test', 2, 1);
        $this->assertEquals(2, $this->redisCache->get('xjchen.wechat.test'));
    }
}
