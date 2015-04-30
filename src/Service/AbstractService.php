<?php namespace Xjchen\Wechat\Service;

use Xjchen\Wechat\Repository\CacheInterface;
use GuzzleHttp\ClientInterface;
use OutOfRangeException;
use Xjchen\Wechat\Exception\WechatInterfaceException;

abstract class AbstractService
{
    protected static $cache = null;
    protected static $config = [];
    protected static $httpClient;

    private $lastResult;

    const ACCESS_TOKEN_URL =
        'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appId}&secret={$appSecret}';

    public function __construct(array $config, ClientInterface $httpClient, CacheInterface $cache = null)
    {
        static::$config = $config;
        static::$httpClient = $httpClient;
        static::$cache = $cache;
    }

    public function setCacheRepository(CacheInterface $cache)
    {
        static::$cache = $cache;
    }

    public function getCacheRepository()
    {
        return static::$cache;
    }

    public function setConfigRepository($config)
    {
        static::$config = $config;
    }

    public function getConfigRepository()
    {
        return static::$config;
    }

    public function __get($key)
    {
        if (!isset(static::$config[$key])) {
            throw new OutOfRangeException(sprintf(
                'config does not contain a key by the name of "%s"',
                $key
            ));
        }

        return static::$config[$key];
    }

    public static function parseTemplate($url, $params)
    {
        extract($params);
        eval('$url="'.$url.'";');
        return $url;
    }

    public static function errorChecker($result)
    {
        if (isset($result['errcode']) and (!empty($result['errcode']))) {
            throw new WechatInterfaceException($result['errmsg'], $result['errcode']);
        }
    }

    public function getAccessToken($appId = null, $appSecret = null)
    {
        if ($appId != null) {
            static::$config['appId'] = $appId;
        }
        if ($appSecret != null) {
            static::$config['appSecret'] = $appSecret;
        }
        $params = [
            'appId' => static::$config['appId'],
            'appSecret' => static::$config['appSecret']
        ];
        $cacheKey = $params['appId'].'-AccessToken';
        if (static::$cache) {
            if (static::$cache->has($cacheKey)) {
                return static::$cache->get($cacheKey);
            }
        }
        $url = static::parseTemplate(static::ACCESS_TOKEN_URL, $params);
        $result = $this->basicGetUrl($url);
        if (static::$cache) {
            static::$cache->put($cacheKey, $result['access_token'], $result['expires_in']/60);
        }
        return $result['access_token'];
    }

    public function basicGetUrl($url)
    {
        $response = static::$httpClient->get($url);
        $result = $response->json();
        static::errorChecker($result);
        $this->lastResult = $result;
        return $result;
    }

    public function basicPostUrl($url, $payload = null)
    {
        $response = static::$httpClient->post($url, ['body' => json_encode($payload)]);
        $result = $response->json();
        static::errorChecker($result);
        $this->lastResult = $result;
        return $result;
    }

    public function getLastResult()
    {
        return $this->lastResult;
    }

    public static function generateSignature($params, $method = 'sha1')
    {
        ksort($params);
        $paramsString = '';
        foreach ($params as $key => $value) {
            if ($paramsString == '') {
                $paramsString = $key . "=" . $value;
            } else {
                $paramsString .= "&" . $key . "=" . $value;
            }
        }
        return $method($paramsString);
    }
}
