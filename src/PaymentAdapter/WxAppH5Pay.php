<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

use Fenghuohuo\Payment\PaymentAdapter\Wx\WxPayApi;
use Fenghuohuo\Payment\PaymentAdapter\Wx as WxData;
use Log;

require_once(dirname(__FILE__) . '/Wx/WxPayData.php');

class WxAppH5Pay extends Adapter
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
        $input->SetSpbill_create_ip($order->clientIp);
        $input->SetTotal_fee(intval($order->amount));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetProduct_id($order->pid ?: -1);
        $input->SetOut_trade_no($order->orderId);
        $input->SetNotify_url($this->option['WX_H5_NOTIFY_URL']);
        $input->SetTrade_type("MWEB");

        $result = $this->wxPayApi->unifiedOrder($input);
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return json_encode([
                'mweb_url' => $result['mweb_url']
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            throw new \Exception($result['return_msg'], 1);
        }
    }

    /**
     * 支付结果通用通知
     *
     * @param $notifyData
     * @param array $payAppParam
     *
     * @return array
     * @throws \Exception
     */
    final public function checkNotify($notifyData, $payAppParam = [])
    {
        $this->setConfigure();
        $needSign = true;
        $notifyArr = WxData\WxPayResults::Init($notifyData, $this->option['WX_KEY']);

        if (!array_key_exists("transaction_id", $notifyArr)) {
            throw new \Exception("输入参数不正确", 1);
        }
        $input = new WxData\WxPayOrderQuery();
        $input->SetTransaction_id($notifyArr["transaction_id"]);
        $orderQuery = $this->wxPayApi->orderQuery($input);
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
            'openId'        => $notifyArr['openid']
        ];

        return ['result' => $checked, 'order' => $order, 'response' => $notify->ToXml()];
    }
}