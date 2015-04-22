<?php namespace Xjchen\Wechat\Service;

use InvalidArgumentException;

class JsApi extends AbstractService
{
    const JS_API_TICKET_URL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$accessToken}&type={$type}';

    public function getJsApiTicket()
    {
        $accessToken = static::getAccessToken();
        $params = [
            'type' => 'jsapi',
            'accessToken' => $accessToken
        ];
        $cacheKey = $params['accessToken'].'-JsApiTicket';
        if (static::$cache) {
            if (static::$cache->has($cacheKey)) {
                return static::$cache->get($cacheKey);
            }
        }
        $url = static::parseTemplate(static::JS_API_TICKET_URL, $params);
        $result = static::basicGetUrl($url);
        if (static::$cache) {
            static::$cache->put($cacheKey, $result['ticket'], $result['expires_in']/60);
        }
        return $result['ticket'];
    }

    public function getSignature($url, $nonceStr, $timestamp)
    {
        if (!preg_match('/^http(s)?:\/\/[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-z]{2,6}(:\d+)?\/([-a-zA-Z0-9@:%_\+.~?&\/=]*)$/', $url)) {
            throw new InvalidArgumentException('调用getSignature时url参数不合法，请检查url');
        }
        $jsApiTicket = $this->getJsApiTicket();
        $params = [
            'jsapi_ticket' => $jsApiTicket,
            'noncestr' => $nonceStr,
            'timestamp' => $timestamp,
            'url' => $url,
        ];
        return static::generateSignature($params);
    }
}
