<?php namespace Xjchen\Wechat\Exception;

use Exception;

class WechatPayException extends Exception
{
    protected $return;

    public function __construct($message, $return)
    {
        $this->return = $return;

        parent::__construct($message);
    }

    public function getReturn()
    {
        return $this->return;
    }
}
