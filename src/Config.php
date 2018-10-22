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
            'ALIPAY_PARTNER'     => '2088821389843589',
            'ALIPAY_KEY'         => 'zhav450ysk74cpt20pm8pdlgyxxicfcq',
            'ALIPAY_SELLER_ID'   => 'zhangyuzhuawawa@163.com',
            'ALIPAY_PUBLIC_KEY'  => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRA
FljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQE
B/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5Ksi
NG9zpgmLCUYuLkxpLQIDAQAB
-----END PUBLIC KEY-----',
            'ALIPAY_PRIVATE_KEY' => '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC9+UUn8BDzRWRF/Aqdwo07/ZJvkq/ypZDnAGjeI/LPub+RK/eH
mQwmhFoB9nV8/PKWNyl1yB+tR/KnK/tvo1y244k2UHpd2jBEKH7X3UVoVouzHjtJ
JmgZFxEGwMpf+I54GOFNxGuxptv8QtHb64GJ8gQi9+6mw6nsY0BwUQKOUwIDAQAB
AoGBAJw0HS/0jgtpkESXNCd5s+WS31hMVc3/YwD97jxRdLJmueRlMXfWWQ5Gnzej
7gDif5kSLE4DSkCRuyzH1kt6GJ1/Uh0f6yUvbbNnOL/UHuySw0MlBof4BrqciNUE
entdaHhLliQ4ULTPB38AgFTWT1OAwBcmiLrE+nvz4+ImWSHRAkEA+ZktgAfWvA9D
VQLVHGoIDaxsGwnmze1sOwbQUXgKxuxLpnikoi3I6LgbgDr82TfTnbeR28h25k1/
qAJTxqyOawJBAMLYm1DMzfUGzJyak487hGG/XhSWMZPE5s/VUheDK6jAvHV06eIl
XwzbhSq/GAKTn80bKjshAd1/YqTZQ4mhqbkCQG4VwjyquGn/bVoMQsQie+TT/GY8
isCei9LI4Y5dHJu50m/c2/fvq6IAuZhn6+c+OSZhtYIzO0W+PqRySlLg/nsCQQCj
XgQTojSJXhMOtxhDvs5HOrHCJxAYar5vwddbuXJQwpEBFm7HWzgvypsD9UdHWclh
qoYlH461zYnC2BuleTOZAkBTZwVVVSBSmKupjYRnnBtAY4veEX5V31M1pTjLBp1E
NaQf2t/OGQwC4rY2rZrh+EOZaEgHtQqv6545udYujmAo
-----END RSA PRIVATE KEY-----
',
            'ALIPAY_NOTIFY_URL'  => 'http://api.diaoyu-3.com/payment/notify/cid_296/appId_2088821389843589/platform_/channel_alipay',
            'ALIPAY_RETURN_URL'  => 'http://www.baidu.com'
        ];

        return $ret;
    }
}