<?php namespace Xjchen\Wechat\Exception;

use Exception;

class WechatPayException extends Exception
{
    protected $return;

    public function __construct($message, $code, $return)
    {
        $this->return = $return;

        parent::__construct($message, $code);
    }

    public function getReturn()
    {
        return $this->return;
    }
}
