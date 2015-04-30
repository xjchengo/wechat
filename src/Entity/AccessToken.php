<?php namespace Xjchen\Wechat\Entity;

use DateTime;
use InvalidArgumentException;

class AccessToken extends AbstractEntity
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var DateTime|null
     */
    protected $expiresAt;

    public function __construct($accessToken, DateTime $expiresAt = null)
    {
        $this->token = $accessToken;
        $this->expiresAt = $expiresAt;
    }

    public function get($refresh = false)
    {
        if ($this->isExpired()) {
            if (!$refresh) {
                return false;
            }
            $this->refresh();
        }
        return $this->token;
    }

    public function refresh()
    {

    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function isExpired()
    {
        return $this->expiresAt->getTimestamp() < time();
    }

    public function isValid()
    {

    }

    public function __toString()
    {
        return $this->token;
    }
}
