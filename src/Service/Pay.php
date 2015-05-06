<?php namespace Xjchen\Wechat\Service;

use InvalidArgumentException;
use Xjchen\Wechat\Exception\WechatPayException;
use Xjchen\Wechat\HttpClient\Url;
use Xjchen\Wechat\Support\Util;

class Pay extends AbstractService
{
    const UNIFIED_ORDER_URL =
        'https://api.mch.weixin.qq.com/pay/unifiedorder';

    protected static $params;

    protected static $lastParamString;

    public function createOrder($outTradeNo, $totalFee, $body, $nonceStr, $clientIp, $notifyUrl, $tradeType, $openid)
    {
        $params = [
            'appid' => static::$config['appId'],
            'mch_id' => static::$config['mchId'],
            'nonce_str' => $nonceStr,
            'body' => $body,
            'out_trade_no' => $outTradeNo,
            'total_fee' => $totalFee,
            'spbill_create_ip' => $clientIp,
            'notify_url' => $notifyUrl,
            'trade_type' => $tradeType,
            'openid' => $openid
        ];

        $params['sign'] = self::generatePaySignature($params);

        static::$params = $params;

        $encodedParam = Util::arrayToXml($params);

        $response = static::$httpClient->post(Pay::UNIFIED_ORDER_URL, ['body' => $encodedParam]);

        $responseBody = Util::xmlDecode((string)$response->getBody());

        if ($responseBody['return_code'] == 'SUCCESS' and $responseBody['result_code'] == 'SUCCESS') {
            $responseBody['pay_sign'] = static::generatePaySignature($params);
            return $responseBody;
        } else {
            throw new WechatPayException($responseBody['return_msg'], $responseBody);
        }
    }

    public static function generateNotifyReply($code, $message = null)
    {
        $return = [
            'return_code' => $code
        ];
        if ($message) {
            $return['return_msg'] = $message;
        }
        return Util::arrayToXml($return);
    }

    public static function generatePaySignature($params)
    {
        $params = array_filter($params, function ($param) {
            return empty($param)?false:true;
        });

        ksort($params);

        $paramString = Url::buildQuery($params, false);
        $paramString .= '&key='.static::$config['key'];
        self::$lastParamString = $paramString;

        return strtoupper(md5($paramString));
    }

    public function getLastParamString()
    {
        return static::$lastParamString;
    }
}
