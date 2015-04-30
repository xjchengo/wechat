<?php namespace Xjchen\Wechat\Support;

use InvalidArgumentException;

class Util
{
    public static function isHttps()
    {
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
    }

    public static function getCurrentUrl()
    {
        $url = '';

        // Check to see if it's over https
        $is_https = self::isHttps();
        if ($is_https) {
            $url .= 'https://';
        } else {
            $url .= 'http://';
        }


        $url .= $_SERVER['HTTP_HOST'];
        $port = $_SERVER['SERVER_PORT'];
        // Is it on a non standard port?
        if ($is_https && ($port != 443)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        } elseif (!$is_https && ($port != 80)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }

        // Get the rest of the URL
        if (!isset($_SERVER['REQUEST_URI'])) {
            // Microsoft IIS doesn't set REQUEST_URI by default
            $url .= $_SERVER['PHP_SELF'];
            if (isset($_SERVER['QUERY_STRING'])) {
                $url .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            $url .= $_SERVER['REQUEST_URI'];
        }

        return $url;
    }

    public static function isInWechatApp($userAgent = null)
    {
        $userAgent = $userAgent ?: $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MicroMessenger/i', $userAgent)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isInIOS($userAgent = null)
    {
        $userAgent = $userAgent ?: $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/iPhone/i', $userAgent)) {
            return true;
        } else {
            return false;
        }
    }

    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }

    public static function studly($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return str_replace(' ', '', $value);
    }

    /**
     * decode json string
     *
     * get from GuzzleHttp
     *
     * @param string $raw
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     */
    public static function jsonDecode($raw, $assoc = true, $depth = 512, $options = 0)
    {
        static $jsonErrors = [
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        ];

        $data = json_decode($raw, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $last = json_last_error();
            throw new InvalidArgumentException(
                'Unable to parse JSON data: '
                . (isset($jsonErrors[$last])
                    ? $jsonErrors[$last]
                    : 'Unknown error')
            );
        }

        return $data;
    }

    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    public static function xmlDecode($xml)
    {
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    public static function generateRandomString($length = 32, $charset='1234567890abcdefghijklmnopqrstuvwxyz')
    {
        //支持拆分中文
        $charset = preg_split('/(?<!^)(?!$)/u', $charset);
        $phrase = '';

        for ($i = 0; $i < $length; $i++) {
            $phrase .= $charset[array_rand($charset)];
        }

        return $phrase;
    }
}
