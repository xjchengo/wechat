<?php namespace Xjchen\Wechat\Entity;

use InvalidArgumentException;

class GeoLocation
{
    protected $latitude;

    protected $longitude;

    protected $accuracy;

    protected $speed;

    public function __construct($latitude, $longitude, $accuracy = null, $speed = null)
    {
        if (abs($latitude) > 90) {
            throw new InvalidArgumentException('纬度错误，必须为浮点数，范围为 90 ~ -90：'. $latitude);
        }
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->accuracy = $accuracy;
        $this->speed = $speed;
    }

    /**
     * @param $address
     * @param null $region
     *
     * @return self
     */
    public static function makeFromAddress($address, $region = null)
    {

    }

    public static function distanceBetween()
    {

    }

    public function distanceTo(GeoLocation $geoLocation)
    {

    }
}
