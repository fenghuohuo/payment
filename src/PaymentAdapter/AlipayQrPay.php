<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

/**
 * Class AlipayQrPay
 * @package Fenghuohuo\Payment\PaymentAdapter
 */
class AlipayQrPay extends Adapter
{
    /**
     * @param \Stdclass $order
     * @return mixed|\SimpleXMLElement
     * @throws \Exception
     */
    public function getCredential(\Stdclass $order)
    {
        $this->setConfigure();

        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->option['partner'];
        $aop->rsaPrivateKey = $this->option['privateKey'];
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = 'RSA2';
        $aop->alipayrsaPublicKey = $this->option['alipayKey'];

        $request = new \AlipayTradePrecreateRequest();
        $request->setNotifyUrl($this->option['notifyUrl']);
        $request->setBizContent(json_encode([
            'out_trade_no'    => $order->orderId,
            'total_amount'    => $order->amount / 100, //单位为元
            'subject'         => $order->subject, //订单标题
            'timeout_express' => '90m'
        ], JSON_UNESCAPED_UNICODE));
        $response = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $response->$responseNode->code;

        //var_dump($response->$responseNode);
        if (!empty($resultCode) && $resultCode == 10000) {
            $qrCode = $response->$responseNode->qr_code;

            return $qrCode;
        } else {
            throw new \Exception('二维码生成失败:' . $resultCode, 1);
        }
    }

    /**
     * @param $data
     * @param array $payAppParam
     * @return array|mixed
     * @throws \Exception
     */
    final public function checkNotify($data, $payAppParam = [])
    {
        $notifyData = json_decode($data, true);
        $this->setConfigure();

        print_r($notifyData);
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = $this->option['alipayKey'];
        if (!$aop->rsaCheckV2($notifyData, '')) {
            return ['result' => false, 'response' => 'success'];
        } else {
            $order = [
                'orderId'       => $notifyData['out_trade_no'],
                'totalFee'      => $notifyData['total_amount'] * 100,
                'transactionId' => $notifyData['trade_no'],
                'openId'        => $notifyData['buyer_id'],
            ];
            return ['result' => true, 'order' => $order, 'response' => 'success'];
        }
    }
}