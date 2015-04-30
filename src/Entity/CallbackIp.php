<?php namespace Xjchen\Wechat\Entity;

class CallbackIp extends AbstractEntity
{
    /**
     * ip list
     *
     * @var array
     */
    protected $list;

    /**
     * @param string $raw
     */
    public function __construct($raw)
    {
        parent::__construct($raw);
        $this->list = $this->arrayFromParser['ip_list'];
    }

    public function has($ip)
    {
        return in_array($ip, $this->list);
    }

    public function count()
    {
        return count($this->list);
    }

    public function all()
    {
        return $this->list;
    }
}
