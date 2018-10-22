<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

use Fenghuohuo\Payment\Config;
use Fenghuohuo\Payment\PaymentAdapter\Wx\WxPayResults;
use Fenghuohuo\Payment\PaymentAdapter\Wx\WxPayApi;
use Fenghuohuo\Payment\PaymentAdapter\Payfubao\PayfubaoApi;
use Pingpp\Pingpp;
use Log;
use Phalcon\Db;

abstract class Adapter implements AdapterInterface
{
    /**
     * 获取到的所有配置信息
     * @var array
     */
    protected $configure = [];

    /**
     * appId
     * @var string
     */
    protected $configAppId = '';

    /**
     * 需要使用的配置信息
     * @var array
     */
    protected $option = [];

    protected $wxPayApi = null;

    /**
     * @throws \Exception
     */
    public function setConfigure()
    {
        $arr = explode('\\', static::class);
        $pay = end($arr);
        $this->configure = Config::get();

        switch ($pay) {
            case 'Alipay':
            case 'AlipayPcDirectPay':
                $this->option = [
                    'publicKey'   => $this->configure['ALIPAY_PUBLIC_KEY'],
                    'privateKey'  => $this->configure['ALIPAY_PRIVATE_KEY'],
                    'partner'     => $this->configure['ALIPAY_PARTNER'],
                    'sellerId'    => $this->configure['ALIPAY_SELLER_ID'],
                    'alipayKey'   => $this->configure['ALIPAY_KEY'],
                    'notifyUrl'   => $this->configure['ALIPAY_NOTIFY_URL'],
                    'returnUrl'   => $this->configure['ALIPAY_RETURN_URL'],
                ];

                $this->configAppId = $this->configure['ALIPAY_PARTNER'];
                break;
            case 'AlipayWapPay':
                $this->option = [
                    'publicKey'   => $this->configure['ALIPAY_WAP_PUBLIC_KEY'],
                    'privateKey'  => $this->configure['ALIPAY_WAP_PRIVATE_KEY'],
                    'partner'     => $this->configure['ALIPAY_WAP_PARTNER'],
                    'sellerId'    => $this->configure['ALIPAY_WAP_SELLER_ID'],
                    'alipayKey'   => $this->configure['ALIPAY_WAP_KEY'],
                    'notifyUrl'   => $this->configure['ALIPAY_WAP_NOTIFY_URL'],
                    'returnUrl'   => $this->configure['ALIPAY_WAP_RETURN_URL'],
                ];

                $this->configAppId = $this->configure['ALIPAY_WAP_PARTNER'];
                break;
            case 'AlipayQrPay':
                //throw new \Exception('没有配置AlipayQrPay', 99);
                //$this->publicKey = $this->configure['ALIPAY_QR_PUBLIC_KEY'];
                $this->option = [
//                    'publicKey'   => $this->configure['ALIPAY_WAP_PUBLIC_KEY'],
                    'privateKey'  => $this->configure['ALIPAY_QR_PRIVATE_KEY'],
                    'partner'     => $this->configure['ALIPAY_QR_PARTNER'],
                    'sellerId'    => $this->configure['ALIPAY_QR_SELLER_ID'],
                    'alipayKey'   => $this->configure['ALIPAY_QR_KEY'],
                    'notifyUrl'   => $this->configure['ALIPAY_QR_NOTIFY_URL'],
                    'returnUrl'   => $this->configure['ALIPAY_QR_RETURN_URL'],
                ];

                $this->configAppId = $this->configure['ALIPAY_QR_PARTNER'];
                break;
            case 'WxAppPay':
                $this->option = [
                    'WX_APPID'      => $this->configure['WX_APPID'],
                    'WX_MCHID'      => $this->configure['WX_MCHID'],
                    'WX_KEY'        => $this->configure['WX_KEY'],
                    'WX_NOTIFY_URL' => $this->configure['WX_NOTIFY_URL'],
                ];
                $this->wxPayApi = new WxPayApi;
                $this->wxPayApi->setOption($this->option);

                $this->configAppId = $this->configure['WX_APPID'];
                break;
            case 'WxAppWebPay':
                // 微信公众号充值配置
                $this->option = [
                    'WX_APPID'      => $this->configure['WX_PUB_APPID'],
                    'WX_MCHID'      => $this->configure['WX_PUB_MCHID'],
                    'WX_KEY'        => $this->configure['WX_PUB_KEY'],
                    'WX_NOTIFY_URL' => $this->configure['WX_PUB_NOTIFY_URL'],
                ];
                $this->wxPayApi = new WxPayApi;
                $this->wxPayApi->setOption($this->option);

                $this->configAppId = $this->configure['WX_PUB_APPID'];
                break;
            case 'WxAppMinaPay':
            case 'WxAppQrPay':
            case 'WxAppH5Pay':
                // 微信小程序充值/微信扫码充值/微信h5充值
                $this->option = [
                    'WX_APPID'         => $this->configure['WX_MINA_APPID'],
                    'WX_MCHID'         => $this->configure['WX_MINA_MCHID'],
                    'WX_KEY'           => $this->configure['WX_MINA_KEY'],
                    'WX_NOTIFY_URL'    => $this->configure['WX_MINA_NOTIFY_URL'],
                    'WX_QR_NOTIFY_URL' => $this->configure['WX_QR_NOTIFY_URL'],
                    'WX_H5_NOTIFY_URL' => $this->configure['WX_H5_NOTIFY_URL'],
                ];
                $this->wxPayApi = new WxPayApi;
                $this->wxPayApi->setOption($this->option);

                $this->configAppId = $this->configure['WX_MINA_APPID'];
                break;
            default:
                throw new \Exception('获取配置错误', 99);
                break;
        }
    }

    /*
     * 返回充值渠道配置的账号appId（payment_config.appId）
     */
    public function getConfigAppId()
    {
        return $this->configAppId;
    }

}