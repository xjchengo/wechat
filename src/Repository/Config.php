<?php namespace Xjchen\Wechat\Repository;

use Xjchen\Wechat\Support\Util;
use InvalidArgumentException;
use UnexpectedValueException;
use Xjchen\Wechat\Entity\AccessToken;

class Config
{
    protected static $appId = '';
    protected static $appSecret = '';
    protected static $token = '';
    protected static $cache = null;
    protected static $accessToken = null;

    public static function fromArray(array $configs)
    {
        foreach ($configs as $key => $value)
        {
            $key = Util::camel($value);
            if (property_exists(__CLASS__, $key)) {
                static::${$key} = $value;
            }
        }
    }

    public static function setAppId($appId)
    {
        if (!$appId) {
            throw new InvalidArgumentException('appId cannot be empty');
        }
        static::$appId = $appId;
    }

    public static function setAppSecret($appSecret)
    {
        if (!$appSecret) {
            throw new InvalidArgumentException('appSecret cannot be empty');
        }
        static::$appSecret = $appSecret;
    }

    public static function setToken($token)
    {
        if (!$token) {
            throw new InvalidArgumentException('token cannot be empty');
        }
        static::$token = $token;
    }

    public static function setCache(CacheInterface $cache)
    {
        static::$cache = $cache;
    }

    public static function setAccessToken(AccessToken $accessToken)
    {
        static::$accessToken = $accessToken;
    }

    public static function getAppId()
    {
        if (!static::$appId) {
            throw new UnexpectedValueException('appId not set');
        }
        return (string)static::$appId;
    }

    public static function getAppSecret()
    {
        if (!static::$appSecret) {
            throw new UnexpectedValueException('appSecret not set');
        }
        return (string)static::$appSecret;
    }

    public static function getToken()
    {
        if (!static::$token) {
            throw new UnexpectedValueException('token not set');
        }
        return (string)static::$token;
    }

    public static function getCache()
    {
        if (!(static::$cache instanceof CacheInterface)) {
            throw new UnexpectedValueException('cache must implement CacheInterface');
        }
        return static::$cache;
    }

    public static function getAccessToken()
    {
        // TODO if access token is not set, try to get from cache.

        if (!(static::$accessToken instanceof AccessToken)) {
            throw new UnexpectedValueException('accessToken must be instance of AccessToken');
        }
        return static::$accessToken;
    }
}