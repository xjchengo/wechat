<?php namespace Xjchen\Wechat\Support;

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
}