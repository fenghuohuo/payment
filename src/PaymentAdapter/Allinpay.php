<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

class Allinpay extends Adapter
{
    public $money_unit = 100;

    /**
     * 脉淼商户号
     *
     * @var string
     */
    private $merchant_id = '109060211708001';
    private $key = '727qmtv1734com';

    public function getCredential(\Stdclass $order)
    {
        $this->tlcert_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Allinpay/certificate/allien_tlcert.cer';

        $config = [
            'input_charset' => 1,//默认填 1； 1 代表 UTF-82 代表GBK,3 代表GB2312；
            'sign_type'     => 1,//默认填 1， 固定选择值： 0、 1；0 表示订单上送和交易结果通知都使用 MD5 进行签名 1表示商户用使用 MD5 算法验签上送订单
            'merchant_id'   => $this->merchant_id,//商户编号
            'key'           => $this->key,//加密密钥
            'tlcert_path'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Allinpay/certificate/allien_tlcert.cer'
        ];
        $this->tlcert_path = $config['tlcert_path'];
        $this->key = $config['key'];

        $url = 'https://service.allinpay.com/gateway/index.do';//正式支付请求地址'
        $postArray['inputCharset'] = $config['input_charset'];
        $postArray['pickupUrl'] = 'http://www.quanmin.tv'; // 跳转地址
        $postArray['receiveUrl'] = PAYMENT_HOST . '/notify/allinpay';
        $postArray['version'] = 'v1.0';//网关接收支付请求接口版本
        $postArray['language'] = '1';//默认填 1，固定选择值： 1； 1 代表简体中文、 2 代表繁体中文、 3 代表英文
        $postArray['signType'] = $config['sign_type'];
        $postArray['merchantId'] = $config['merchant_id'];
        $postArray['orderNo'] = $order->orderId;
        $postArray['orderAmount'] = intval($order->amount);//商户订单金额
        $postArray['orderDatetime'] = date("YmdHis");//订单提交时间 yyyyMMDDhhmmss
        $postArray['productName'] = $order->subject;
        $postArray['productPrice'] = intval($order->amount) / 100;
        $postArray['payType'] = "0";
        $postArray['signMsg'] = $this->getSignMsg($postArray);
        //待请求参数数组
        $sHtml = "<form id='allinpaysubmit' name='allinpaysubmit' action='" . $url . "' method='POST'>";
        while (list ($key, $val) = each($postArray)) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml = $sHtml . "<script>document.forms['allinpaysubmit'].submit();</script>";
        return $sHtml;
    }

    final function checkNotify($data = [])
    {
        $this->tlcert_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Allinpay/certificate/allien_tlcert.cer';

        $notifyData = json_decode($data['notify'], true);
        $params['merchantId'] = isset($notifyData['merchantId']) ? trim($notifyData['merchantId']) : "";
        $params['version'] = isset($notifyData['version']) ? trim($notifyData['version']) : "";
        $params['language'] = isset($notifyData['language']) ? trim($notifyData['language']) : "";
        $params['signType'] = isset($notifyData['signType']) ? trim($notifyData['signType']) : "";
        $params['payType'] = isset($notifyData['payType']) ? trim($notifyData['payType']) : "";
        $params['issuerId'] = isset($notifyData['issuerId']) ? trim($notifyData['issuerId']) : "";
        $params['paymentOrderId'] = isset($notifyData['paymentOrderId']) ? trim($notifyData['paymentOrderId']) : "";//通联订单号
        $params['orderNo'] = isset($notifyData['payType']) ? trim($notifyData['orderNo']) : "";//商户支付订单ID
        $params['orderDatetime'] = isset($notifyData['payType']) ? trim($notifyData['orderDatetime']) : "";//商户订单提交时间
        $params['orderAmount'] = isset($notifyData['payType']) ? trim($notifyData['orderAmount']) : "";//商户订单金额
        $params['payDatetime'] = isset($notifyData['payType']) ? trim($notifyData['payDatetime']) : "";//支付完成时间
        $params['payAmount'] = isset($notifyData['payAmount']) ? htmlentities($notifyData['payAmount']) : "";//订单实际交付金额
        $params['ext1'] = isset($notifyData['ext1']) ? trim($notifyData['ext1']) : "";
        $params['ext2'] = isset($notifyData['ext2']) ? trim($notifyData['ext2']) : "";
        $params['payResult'] = isset($notifyData['payResult']) ? trim($notifyData['payResult']) : "";
        $params['errorCode'] = isset($notifyData['errorCode']) ? trim($notifyData['errorCode']) : "";
        $params['returnDatetime'] = isset($notifyData['returnDatetime']) ? trim($notifyData['returnDatetime']) : "";
        $params['signMsg'] = isset($notifyData['signMsg']) ? trim($notifyData['signMsg']) : "";

        $result = $this->verifyNotify($params);

        if ($result) {
            list($orderId, $totalFee, $tradeNo) = $result;
            $order = [
                'orderId'       => $orderId,
                'totalFee'      => $totalFee * 100,
                'transactionId' => $tradeNo
            ];
            return ['result' => true, 'order' => $order, 'response' => 'success'];
        } else {
            return ['result' => false, 'response' => 'fail'];
        }
    }

    /**
     * 支付回调通知
     *
     * @param $params
     *
     * @return array|bool
     */
    public function verifyNotify($params)
    {
        //判断商户信息是否正确
        if ($this->merchant_id != $params['merchantId']) {
            //商户信息不正确
            return false;
        }
        //消息校验
        $result = $this->getSignMsgBySignType($params);
        if (!$result) {
            //消息校验失败
            return false;
        }
        //验证支付结果是否成功
        if ($params['payResult'] != 1) {
            return false;
        }
        $out_trade_no = $params['orderNo'];        //获取订单号
        $trade_no = $params['paymentOrderId'];            //获取支付宝交易号
        $total_fee = $params['payAmount'] / $this->money_unit;       //订单实际交付
        return [$out_trade_no, $total_fee, $trade_no];
    }

    /**
     * 通联支付回调信息返回
     *
     * @param $params
     *
     * @return array|bool
     */
    public function verifyReturn($params)
    {
        //判断商户信息是否正确
        if ($this->merchant_id != $params['merchantId']) {
            //商户信息不正确
            return false;
        }
        //消息校验
        $result = $this->getSignMsgBySignType($params);
        if (!$result) {
            //消息校验失败
            return false;
        }
        //验证支付结果是否成功
        if ($params['payResult'] != 1) {
            return false;
        }
        $out_trade_no = $params['orderNo'];        //获取订单号
        $trade_no = $params['paymentOrderId'];            //获取支付宝交易号
        $total_fee = $params['payAmount'] / $this->money_unit;       //订单实际交付
        return [$out_trade_no, $total_fee, $trade_no];
    }

    /**
     * 通联统一下单设置加密字符串
     *
     * @param $inputArray
     *
     * @return string
     */
    private function getSignMsg($inputArray)
    {
        $signString = "";
        if (is_array($inputArray)) {
            foreach ($inputArray as $key => $input) {
                if ($input != "") {
                    $signString .= "{$key}={$input}&";
                }
            }
        } else {
            $signString = $inputArray . "&";
        }
        if (!empty($signString)) {
            $signString .= "key=" . $this->key;
        }
        return strtoupper(md5($signString));
    }

    /**
     * 通联回调加密，如果signtype =1 是用该方法加密
     *
     * @param $inputArray
     *
     * @return string
     */
    private function getSignMsgBySignType($inputArray)
    {
        $signMsg = $inputArray['signMsg'];
        unset($inputArray['signMsg']);
        $signString = "";
        if (is_array($inputArray)) {
            foreach ($inputArray as $key => $input) {
                if ($input != "") {
                    $signString .= "{$key}={$input}&";
                }
            }
        } else {
            $signString = $inputArray . "&";
        }
        $signString = trim($signString, "&");
        //解析证书方式
        $certfile = file_get_contents($this->tlcert_path);
        $x509 = new \File_X509();
        $x509->loadX509($certfile);
        $pubkey = $x509->getPublicKey();
        $rsa = new \Crypt_RSA();
        $rsa->loadKey($pubkey); // public key
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        return $rsa->verify($signString, base64_decode(trim($signMsg)));
    }
}