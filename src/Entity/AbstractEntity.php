<?php namespace Xjchen\Wechat\Entity;

use InvalidArgumentException;

abstract class AbstractEntity
{
    /**
     * raw response body from wechat server
     *
     * @var string
     */
    protected $raw;

    /**
     * array got from parsed response body
     *
     * @var array
     */
    protected $arrayFromParser;

    protected function handleRaw($raw)
    {
        $this->setRaw($raw);
        $this->arrayFromParser = static::parse($raw);
    }

    protected function setRaw($raw)
    {
        $this->raw = $raw;
    }

    /**
     * get raw response body from wechat server
     *
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

}
