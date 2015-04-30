<?php namespace Xjchen\Wechat\Service;

class TemplateMessage extends AbstractService
{
    const CUSTOM_MESSAGE_SEND_URL =
        'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$accessToken}';

    public function send($message)
    {
        $accessToken = $this->getAccessToken();
        $params = [
            'accessToken' => $accessToken
        ];
        $url = static::parseTemplate(static::CUSTOM_MESSAGE_SEND_URL, $params);
        $result = $this->basicPostUrl($url, $message);
        return $result['msgid'];
    }
}
