<?php namespace Xjchen\Wechat\Service;

use Xjchen\Wechat\Repository\Cache;
use Xjchen\Wechat\Repository\Config;

abstract class AbstractService
{
    protected static $cache;
    protected static $config;

    public function __construct()
    {

    }

    public function setCacheRepository(Cache $cache)
    {
        static::$cache = $cache;
    }

    public function getCacheRepository()
    {
        return static::$cache;
    }

    public function setConfigRepository(Config $config)
    {
        static::$config = $config;
    }

    public function getConfigRepository()
    {
        return static::$config;
    }
}