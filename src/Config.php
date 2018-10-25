<?php

namespace Fenghuohuo\Payment;

class Config
{

    /**
     * 获取配置
     *
     * @param array $params
     *
     * @return array|bool
     * @throws \Exception
     */
    public static function get($params = [])
    {
        $ret = [
            'ALIPAY_PARTNER'     => '2088****9843589',
            'ALIPAY_KEY'         => 'zhav45******m8pdlgyxxicfcq',
            'ALIPAY_SELLER_ID'   => '邮箱',
            'ALIPAY_PUBLIC_KEY'  => '-----BEGIN PUBLIC KEY-----
***
-----END PUBLIC KEY-----',
            'ALIPAY_PRIVATE_KEY' => '-----BEGIN RSA PRIVATE KEY-----
***
-----END RSA PRIVATE KEY-----
',
            'ALIPAY_NOTIFY_URL'  => '回调地址',
            'ALIPAY_RETURN_URL'  => '返回地址'
        ];

        return $ret;
    }
}