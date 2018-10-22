<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

/**
 * Class AlipayPcDirectPay
 * @package Fenghuohuo\Payment\PaymentAdapter
 */
class AlipayPcDirectPay extends Adapter
{
    protected $signType = 'RSA';

    /**
     * @param \Stdclass $order
     * @return mixed|\提交表单HTML文本
     * @throws \Exception
     */
    public function getCredential(\Stdclass $order)
    {
        $this->setConfigure();
        $config = [
            'partner'           => $this->option['partner'],
            'seller_id'         => $this->option['partner'],
            'private_key'       => $this->option['privateKey'],
            'alipay_public_key' => $this->option['publicKey'],
            'notify_url'        => $this->option['notifyUrl'],
            'return_url'        => $this->option['returnUrl'],
            'sign_type'         => 'RSA',
            'input_charset'     => 'utf-8',
            'service'           => 'create_direct_pay_by_user',
            'transport'         => 'http',
            'payment_type'      => 1,
            'anti_phishing_key' => '',
            'exter_invoke_ip'   => ''
        ];
        $parameter = [
            "service"           => $config['service'],
            "partner"           => $config['partner'],
            "seller_id"         => $config['partner'],
            "out_trade_no"      => $order->orderId,
            "subject"           => $order->subject,
            "total_fee"         => $order->amount / 100,
            "payment_type"      => 1,
            "body"              => $order->body,
            'notify_url'        => $config['notify_url'],
            'return_url'        => $config['return_url'],
            'anti_phishing_key' => '',
            'exter_invoke_ip'   => '',
            "_input_charset"    => 'utf-8'
        ];
        $alipaySubmit = new \AlipaySubmit($config);
        return $alipaySubmit->buildRequestForm($parameter, "get", "确认");
    }

    /**
     * 支付结果通用通知
     * @param string $data
     * @param array $payAppParam
     * @return array|mixed
     * @throws \Exception
     */
    final public function checkNotify($data = '', $payAppParam = [])
    {
        $notifyData = json_decode($data, true);
        $this->setConfigure();

        $config = [
            'partner'           => $this->option['partner'],
            'seller_id'         => $this->option['partner'],
            'private_key'       => $this->option['privateKey'],
            'alipay_public_key' => $this->option['publicKey'],
            'cacert'            => '',
            'notify_url'        => $this->option['notifyUrl'],
            'return_url'        => $this->option['returnUrl'],
            'sign_type'         => 'RSA',
            'input_charset'     => 'utf-8',
            'service'           => 'create_direct_pay_by_user',
            'transport'         => 'http',
            'payment_type'      => 1,
            'anti_phishing_key' => '',
            'exter_invoke_ip'   => ''
        ];

        $alipayNotify = new \AlipayNotify($config);
        $_POST = $notifyData;
        $result = $alipayNotify->verifyNotify();
        if ($result) {
            if ($notifyData['trade_status'] == 'TRADE_SUCCESS' || $notifyData['trade_status'] == 'TRADE_FINISHED') {
                $order = [
                    'orderId'       => $notifyData['out_trade_no'],
                    'totalFee'      => $notifyData['total_fee'] * 100,
                    'transactionId' => $notifyData['trade_no'],
                    'openId'        => $notifyData['buyer_id']
                ];
                return ['result' => true, 'order' => $order, 'response' => 'success'];
            } else {
                return ['result' => false, 'response' => 'fail'];
            }
        } else {
            return ['result' => false, 'response' => 'fail'];
        }
    }
}