<?php

namespace Fenghuohuo\Payment;

use Fenghuohuo\Payment\PaymentAdapter\Alipay;
use Fenghuohuo\Payment\PaymentAdapter\AlipayPcDirectPay;
use Fenghuohuo\Payment\PaymentAdapter\AlipayQrPay;
use Fenghuohuo\Payment\PaymentAdapter\AlipayWapPay;
use Fenghuohuo\Payment\PaymentAdapter\WxAppH5Pay;
use Fenghuohuo\Payment\PaymentAdapter\WxAppPay;
use Fenghuohuo\Payment\PaymentAdapter\WxAppQrPay;
use Fenghuohuo\Payment\PaymentAdapter\WxAppWebPay;
use Fenghuohuo\Payment\PaymentAdapter\WxAppMinaPay;

class Payment
{
    //无效的支付通知
    const ERROR_INVALID_NOTIFY = 1;
    //订单不存在
    const ERROR_ORDER_NOT_EXIST = 3;
    //实际支付金额与订单金额匹配不上
    const ERROR_AMOUNT_NOT_MATCH = 4;
    //交易记录失败
    const ERROR_TRADE_RECORD_FAIL = 5;
    //金币发放失败
    const ERROR_COIN_SEND_FAIL = 6;
    //通知处理失败
    const ERROR_HANDLE_NOTIFY_FAIL = 7;

    //收到通知
    const ACTION_RECIEVE_NOTIFY = 2;
    //验证通知
    const ACTION_VALIDATE_NOTIFY = 3;
    //充值完成，系统处理异常
    const ACTION_NOTIFIED_ERROR = 4;
    //充值成功
    const ACTION_NOTIFIED_SUCCESS = 5;

    protected $adapter = null;

    /**
     * Payment constructor.
     * @param string $channel
     * @throws \Exception
     */
    public function __construct($channel = '')
    {
        switch ($channel) {
            case 'alipay':
                $this->adapter = new Alipay;
                break;
            case 'alipay_wap':
                $this->adapter = new AlipayWapPay;
                break;
            case 'alipay_pc_direct':
                $this->adapter = new AlipayPcDirectPay;
                break;
            case 'alipay_qr':
                $this->adapter = new AlipayQrPay;
                break;
            case 'wx':
                $this->adapter = new WxAppPay;
                break;
            case 'wx_pub':
                $this->adapter = new WxAppMinaPay;
                break;
            case 'wx_pub_qr':
                $this->adapter = new WxAppQrPay;
                break;
            case 'wx_pub_zy':
                $this->adapter = new WxAppWebPay;
                break;
            case 'wx_h5':
                $this->adapter = new WxAppH5Pay;
                break;
            default:
                throw new \Exception("not allow channel", 1);
        }
        return true;
    }

    /**
     * @param \Stdclass $order
     * @throws \Exception
     */
    final public function createOrder(\Stdclass &$order)
    {
        $order->credential = $this->adapter->getCredential($order);
    }

    /**
     * @param $data
     * @param array $payAppParam
     * @return array
     * @throws \Exception
     */
    final public function orderProcess($data, $payAppParam = [])
    {
        // 检测合法性
        $checkResult = $this->adapter->checkNotify($data, $payAppParam);

        return $checkResult;
    }
}
