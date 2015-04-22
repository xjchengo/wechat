<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Service\Media as MediaService;
use SplFileInfo;

class MediaTest extends AbstractServiceTest
{
    private $mediaService;
    private $uploadFilename;
    private $downloadFilename;
    private $directory;

    public function setUp()
    {
        parent::setUp();
        $this->mediaService = new MediaService($this->config, $this->httpClient);
        $this->directory = __DIR__.'/../media/';
        $this->uploadFilename = 'upload.jpg';
        $this->downloadFilename = 'download.jpg';
    }

    public function testUpload()
    {
        $result = $this->mediaService->upload('image', $this->directory.$this->uploadFilename);
        $this->assertArrayHasKey('media_id', $result);
        return $result['media_id'];
    }

    /**
     * @param $mediaId
     *
     * @depends testUpload
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDownloadWrongDir($mediaId)
    {
        $result = $this->mediaService->download($mediaId, __DIR__.'../media', $this->downloadFilename);
    }

    /**
     * @expectedException \Xjchen\Wechat\Exception\WechatInterfaceException
     */
    public function testDownloadWrongMediaId()
    {
        $result = $this->mediaService->download('xxxxxxx', $this->directory, $this->downloadFilename);
    }

    /**
     * @param $mediaId
     *
     * @depends testUpload
     */
    public function testDownload($mediaId)
    {
        if (file_exists(__DIR__.'/../media/download.jpg')) {
            unlink(__DIR__.'/../media/download.jpg');
        }
        $result = $this->mediaService->download($mediaId, $this->directory, $this->downloadFilename);
        $this->assertTrue($result instanceof SplFileInfo);
    }

    /**
     * @param $mediaId
     *
     * @depends testUpload
     * @depends testDownload
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDownloadFileExists($mediaId)
    {
        $result = $this->mediaService->download($mediaId, __DIR__.'/../media', $this->downloadFilename);
    }
}
