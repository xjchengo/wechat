<?php namespace Xjchen\Wechat\Service;

class Menu extends AbstractService
{
    const MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/{$action}?access_token={$accessToken}';
    const MENU_INFO_URL = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token={$accessToken}';

    public function create(array $menu)
    {
        $accessToken = $this->getAccessToken();
        $params = [
            'action' => 'create',
            'accessToken' => $accessToken
        ];
        $url = static::parseTemplate(static::MENU_URL, $params);
        if (!isset($menu['button'])) {
            $menu = [
                'button' => $menu
            ];
        }
        $this->basicPostUrl($url, $menu);
        return true;
    }

    public function get()
    {
        $accessToken = $this->getAccessToken();
        $params = [
            'action' => 'get',
            'accessToken' => $accessToken
        ];
        $url = static::parseTemplate(static::MENU_URL, $params);
        $result = $this->basicGetUrl($url);
        return $result;
    }

    public function delete()
    {
        $accessToken = $this->getAccessToken();
        $params = [
            'action' => 'get',
            'accessToken' => $accessToken
        ];
        $url = static::parseTemplate(static::MENU_URL, $params);
        $this->basicGetUrl($url);
        return true;
    }

    public function getCurrentInfo()
    {
        $accessToken = $this->getAccessToken();
        $params = [
            'accessToken' => $accessToken
        ];
        $url = static::parseTemplate(static::MENU_INFO_URL, $params);
        $result = $this->basicGetUrl($url);
        return $result;
    }
}
