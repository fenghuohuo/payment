<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

use Fenghuohuo\Payment\PaymentAdapter\Wx\WxPayApi;
use Fenghuohuo\Payment\PaymentAdapter\Wx as WxData;
use Log;

require_once(dirname(__FILE__) . '/Wx/WxPayData.php');

class WxAppPay extends Adapter
{
    protected $option;
    /**
     * @var WxPayApi $wxPayApi
     */
    protected $wxPayApi;

    /**
     * @param \Stdclass $order
     * @return mixed|string
     * @throws \Exception
     */
    public function getCredential(\Stdclass $order)
    {
        $this->setConfigure();
        $_SERVER['REMOTE_ADDR'] = $order->clientIp;
        $input = new WxData\WxPayUnifiedOrder();
        $input->SetBody($order->subject);
        $input->SetAttach($order->body);
        $input->SetTotal_fee(intval($order->amount));
        $time = time();
        $input->SetTime_start(date("YmdHis", $time));
        $input->SetTime_expire(date("YmdHis", $time + 900));
        $input->SetProduct_id($order->pid ?: -1);
        $input->SetOut_trade_no($order->orderId);
        $input->SetNotify_url($this->option['WX_NOTIFY_URL']);
        $input->SetTrade_type("APP");

        $result = $this->wxPayApi->unifiedOrder($input);
        if (isset($result['return_code']) && $result['return_code'] != 'SUCCESS') {
            throw new \Exception($result['return_msg'], 1);
        }
        $signResult = new WxData\WxPayResults();
        $signArray = [
            'appid'     => $result['appid'],
            'partnerid' => $result['mch_id'],
            'prepayid'  => $result['prepay_id'],
            'noncestr'  => WxPayApi::getNonceStr(),
            'timestamp' => $time,
            'package'   => 'Sign=WXPay'
        ];
        $signResult->FromArray($signArray);
        // appId， partnerId，prepayId，nonceStr，timeStamp，package:Sign=WXPay
        if (isset($result['return_code']) && $result['return_code'] == 'SUCCESS') {
            return json_encode([
                'appid'     => $result['appid'],
                'mchId'     => $result['mch_id'],
                'nonceStr'  => $signArray['noncestr'],
                'prepayId'  => $result['prepay_id'],
                'timestamp' => $time,
                'package'   => 'Sign=WXPay',
                'sign'      => $signResult->MakeSign($this->option['WX_KEY']),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            throw new \Exception('签名失败', 1);
        }
    }

    /**
     * 支付结果通用通知
     *
     * @param $notifyData
     * @param $payAppParam
     *
     * @return array
     * @throws \Exception
     */
    public function checkNotify($notifyData, $payAppParam = [])
    {
        $this->setConfigure();
        $needSign = true;

        $notifyArr = WxData\WxPayResults::Init($notifyData['notify'], $this->option['WX_KEY']);

        if (!array_key_exists("transaction_id", $notifyArr)) {
            throw new \Exception("输入参数不正确", 1);
        }
        $input = new WxData\WxPayOrderQuery();
        $input->SetTransaction_id($notifyArr["transaction_id"]);
        $orderQuery = $this->wxPayApi->orderQuery($input);
        // Log::DEBUG("query:" . json_encode($result));
        $checked = false;
        if (array_key_exists("return_code", $orderQuery)
            && array_key_exists("result_code", $orderQuery)
            && $orderQuery["return_code"] == "SUCCESS"
            && $orderQuery["result_code"] == "SUCCESS"
        ) {
            $checked = true;
        }

        $notify = new WxData\WxPayNotifyReply();
        if ($needSign == true) {
            $notify->SetSign($this->option['WX_KEY']);
        }

        if ($checked == false) {
            $notify->SetReturn_code("FAIL");
            $notify->SetReturn_msg("OK");
        } else {
            $notify->SetReturn_code("SUCCESS");
            $notify->SetReturn_msg("OK");
        }
        $order = [
            'orderId'       => $notifyArr['out_trade_no'],
            'totalFee'      => $notifyArr['total_fee'],
            'transactionId' => $notifyArr['transaction_id'],
            'openId'        => $notifyArr['openid'],
        ];

        return ['result' => $checked, 'order' => $order, 'response' => $notify->ToXml()];
    }
}