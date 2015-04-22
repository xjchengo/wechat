<?php namespace Xjchen\Wechat\Test\Repository;

use Xjchen\Wechat\Repository\CacheInterface;
use Illuminate\Cache\Repository;

class RedisCache extends Repository implements CacheInterface
{
}
