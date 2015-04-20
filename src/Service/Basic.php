<?php namespace Xjchen\Wechat\Service;

class Basic extends AbstractService
{
    const GET_CALLBACK_IP_URL = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token={$accessToken}';

    public function getCallbackIp()
    {
        $accessToken = static::getAccessToken();
        $params = [
            'accessToken' => $accessToken
        ];
        $url = static::parseTemplate(static::GET_CALLBACK_IP_URL, $params);
        $result = $this->basicGetUrl($url);
        return $result;
    }
}
