<?php namespace Xjchen\Wechat\HttpClient;

use Xjchen\Wechat\Entity\AccessToken;
use InvalidArgumentException;
use UnexpectedValueException;

class Url
{
    protected $scheme = 'https';

    protected $host = 'api.weixin.qq.com';

    protected $path = '';

    /**
     * @var array
     */
    protected $query = [];

    protected $withAccessToken = true;

    /**
     * @var \Xjchen\Wechat\Entity\AccessToken
     */
    protected static $accessToken = null;

    public static function setAccessToken(AccessToken $accessToken)
    {
        static::$accessToken = $accessToken;
    }

    public static function getAccessToken()
    {
        if (static::$accessToken) {
            return static::$accessToken;
        }
        throw new UnexpectedValueException('access token is not set');
    }

    public function __construct($path, $query = null, $withAccessToken = true, $host = null, $scheme = null)
    {
        $this->setPath($path);
        if ($query) {
            $this->setQuery($query);
        }

        $this->withAccessToken = $withAccessToken;

        if ($host) {
            $this->setHost($host);
        }

        if ($scheme) {
            $this->setScheme($scheme);
        }
    }

    public function build($withAccessToken = null)
    {
        $url = $this->scheme.'://'.$this->host.$this->path;
        $query = $this->query;
        if (($withAccessToken == true) or ($withAccessToken === null and $this->withAccessToken == true)) {
            $query['access_token'] = static::getAccessToken()->get();
        }
        if ($query) {
            $url .= '?' . static::buildQuery($query);
        }
        return $url;
    }

    public function setScheme($scheme)
    {
        if (!$scheme) {
            throw new InvalidArgumentException('scheme cannot be empty');
        }
        $this->scheme = $scheme;
    }

    public function setHost($host)
    {
        if (!$host) {
            throw new InvalidArgumentException('host cannot be empty');
        }
        $this->host = $host;
    }

    public function setPath($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('path must be string');
        } elseif (!($path == '' or $path[0] == '/')) {
            throw new InvalidArgumentException('path must start with slash');
        }
        $this->path = $path;
    }

    public function setQuery($query)
    {
        $query = static::parseQuery($query);

        $this->query = $query;
    }

    public function appendQuery($query)
    {
        $appendQuery = static::parseQuery($query);
        $originalQuery = $this->query;

        //Favor new query over original query
        $newQuery = array_merge($originalQuery, $appendQuery);
        $this->query = $newQuery;
    }

    public static function parseQuery($query)
    {
        if (is_string($query)) {
            $query = trim($query, '?&');
            parse_str($query, $queryArray);
        } elseif (is_array($query)) {
            $queryArray = $query;
        } else {
            throw new InvalidArgumentException('query must be string or array');
        }
        return $queryArray;
    }

    public static function buildQuery(array $query, $encType = PHP_QUERY_RFC1738)
    {
        switch ($encType) {
            case PHP_QUERY_RFC1738:
                $encoder = 'urlencode';
                break;
            case PHP_QUERY_RFC3986:
                $encoder = 'rawurlencode';
                break;
            case false:
                $encoder = function ($v) {
                    return $v;
                };
                break;
            default:
                throw new InvalidArgumentException('Invalid encoding type');
        }

        $result = '';

        foreach ($query as $key => $value) {
            if ($result) {
                $result .= '&';
            }
            $result .= $encoder($key);
            if ($value !== null) {
                $result .= '=' . $encoder($value);
            }
        }

        return $result;
    }

    public function __toString()
    {
        return $this->build();
    }
}
