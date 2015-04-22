<?php namespace Xjchen\Wechat\Service;

use InvalidArgumentException;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Message\Request;
use SplFileInfo;
use Exception;

class Media extends AbstractService
{
    const MEDIA_UPLOAD_URL = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$accessToken}&type={$type}';
    const MEDIA_GET_URL = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$mediaId}';

    public function upload($type, $path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException('文件不存在:'.$path);
        }
        $accessToken = $this->getAccessToken();
        $params = [
            'accessToken' => $accessToken,
            'type' => $type
        ];
        $url = static::parseTemplate(static::MEDIA_UPLOAD_URL, $params);
        $request = static::$httpClient->createRequest('POST', $url);
        $postBody = $request->getBody();
        $postBody->addFile(new PostFile('media', fopen($path, 'r')));
        $response = static::$httpClient->send($request);
        $result = $response->json();
        static::errorChecker($result);
        return $result;
    }

    public function download($mediaId, $directory, $filename = null)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException('文件夹不存在:'.$directory);
        } elseif (!is_writable($directory)) {
            throw new InvalidArgumentException('文件夹没有写权限');
        }
        $accessToken = $this->getAccessToken();
        $params = [
            'accessToken' => $accessToken,
            'mediaId' => $mediaId
        ];
        $url = static::parseTemplate(static::MEDIA_GET_URL, $params);
        $response = static::$httpClient->get($url);
        $parsed = Request::parseHeader($response, 'Content-disposition');
        if (!isset($parsed[0]['filename'])) {
            $result = $response->json();
            static::errorChecker($result);
        }
        if ($filename) {
            $path = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.ltrim($filename, DIRECTORY_SEPARATOR);
        } else {
            $filename = date("YmdHis").$parsed[0]['filename'];
            $path = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
        }
        if (file_exists($path)) {
            throw new InvalidArgumentException('文件已存在:'.$path);
        }
        if (file_put_contents($path, $response->getBody()) === false) {
            throw new Exception('文件保存失败');
        }
        return new SplFileInfo($path);
    }

    public function uploadImage($path)
    {

    }

    public function uploadVoice($path)
    {

    }

    public function uploadVideo($path)
    {

    }

    public function uploadThumb($path)
    {

    }
}
