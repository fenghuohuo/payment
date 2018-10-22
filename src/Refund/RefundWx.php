<?php
/**
 * User: Blink
 * Email: caogemail@163.com
 * Date: 2017/12/13
 * Time: 下午3:25
 */

namespace Fenghuohuo\Payment\Refund;

use Fenghuohuo\Payment\PaymentAdapter\Wx as WxData;
use Fenghuohuo\Payment\PaymentAdapter\Wx\WxPayApi;

require_once(dirname(__FILE__) . '/../PaymentAdapter/Wx/WxPayData.php');

/**
 * 微信支付退款
 *
 * Class RefundWx
 * @package Fenghuohuo\Payment\Refund
 */
class RefundWx
{
    public function request($data = [])
    {
        $option = [
            'WX_APPID'            => 'wx4f403985ff0126e7',
            'WX_MCHID'            => '1490271682',
            'WX_KEY'              => '6c99df1d84e20e683596ee0cf6beb484',
            'WX_PUBLIC_KEY_PATH'  => dirname(__FILE__) . '/../PaymentAdapter/Wx/cert/apiclient_cert.pem',
            'WX_PRIVATE_KEY_PATH' => dirname(__FILE__) . '/../PaymentAdapter/Wx/cert/apiclient_key.pem',
        ];
        $wxPayApi = new WxPayApi;
        $wxPayApi->setOption($option);

        $input = new WxData\WxPayRefund();

        $input->SetTransaction_id($data['transactionId']);
        $input->SetOut_trade_no($data['payOrderId']);
        $input->SetOut_refund_no($data['refundOrderId']);
        $input->SetTotal_fee($data['amount']);
        $input->SetRefund_fee($data['amount']);
        $input->SetOp_user_id($option['WX_MCHID']);

        $result = $wxPayApi->refund($input, 3);

        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return $result;
        } else {
            throw new \Exception(json_encode($result, JSON_UNESCAPED_UNICODE), -1);
        }

        // TODO 退款结果查询实现
    }
}