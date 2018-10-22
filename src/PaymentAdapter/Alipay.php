<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

use Fenghuohuo\Payment\Payment;

class Alipay extends Adapter
{
    /**
     * @var string
     */
    protected $signType = 'RSA';

    /**
     * @param \Stdclass $order
     * @return string
     * @throws \Exception
     */
    public function getCredential(\Stdclass $order)
    {
        $this->setConfigure();
        $credential = [
            'partner'        => $this->option['partner'], //商户ID，
            'seller_id'      => $this->option['sellerId'],
            'out_trade_no'   => $order->orderId,
            'subject'        => $order->subject,
            'body'           => $order->body,
            'total_fee'      => $order->amount / 100,
            'notify_url'     => $this->option['notifyUrl'],
            'service'        => 'mobile.securitypay.pay',
            'payment_type'   => '1',
            '_input_charset' => 'utf-8',
            'it_b_pay'       => '15m', //设置未付款交易的超时时间
            'return_url'     => ''
        ];
        $para = $this->buildRequestPara($credential);
        return $this->createLinkstringUrlencode($para);
    }

    /**
     * 支付结果通用通知
     * @param string $data
     * @param array $payAppParam 使用的支付账号信息
     * @return array
     * @throws \Exception
     */
    final public function checkNotify($data = '', $payAppParam = [])
    {
        $notifyData = json_decode($data, true);
        $this->setConfigure();

        $receiveData = $this->paraFilter($notifyData);
        $receiveData = $this->argSort($receiveData);
        if ($receiveData) {
            $verifyResult = $this->getVerify(
                'http://notify.alipay.com/trade/notify_query.do?service=notify_verify&partner=' .
                $this->option['partner'] .
                '&notify_id=' .
                $receiveData['notify_id']
            );
            if (preg_match('/true$/i', $verifyResult)) {
                $sign = $this->getSignVeryfy($receiveData, $notifyData['sign']);
                if ($sign === false) {
                    return ['result' => false, 'response' => 'success'];
                } else {
                    if ($receiveData['trade_status'] == 'TRADE_SUCCESS' ||
                        $receiveData['trade_status'] == 'TRADE_FINISHED') {
                        $order = [
                            'orderId'       => $receiveData['out_trade_no'],
                            'totalFee'      => bcmul($receiveData['total_fee'], 100),
                            'transactionId' => $receiveData['trade_no'],
                            'openId'        => $receiveData['buyer_id'],
                        ];
                        return ['result' => true, 'order' => $order, 'response' => 'success'];
                    } else {
                        return [
                            'result'   => false,
                            'response' => 'fail',
                            'debug1'   => $receiveData,
                            'verify'   => $verifyResult
                        ];
                    }
                }
            } else {
                return [
                    'result'   => false,
                    'response' => 'fail',
                    'debug2'   => $receiveData,
                    'verify'   => $verifyResult
                ];
            }
        } else {
            return ['result' => false, 'response' => 'fail', 'debug3' => $receiveData];
        }
    }

    /**
     * @param string $url
     * @param string $time_out
     * @return array|string
     * @throws \Exception
     */
    protected function getVerify($url = '', $time_out = "60")
    {
        $urlarr = parse_url($url);
        $errno = "";
        $errstr = "";
        if ($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp = @fsockopen($transports . $urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
        if (!$fp) {
            throw new \Exception(sprintf('ALIPAY ERROR:%s-%s', $errno, $errstr), 1);
        } else {
            fputs($fp, "POST " . $urlarr["path"] . " HTTP/1.1\r\n");
            fputs($fp, "Host: " . $urlarr["host"] . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($urlarr["query"]) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            $info = [];
            while (!feof($fp)) {
                $info[] = @fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",", $info);
            return $info;
        }
    }

    /**
     * 生成要请求给支付宝的参数数组
     *
     * @param array $paraTemp 请求前的参数数组
     *
     * @return array 要请求的参数数组
     */
    private function buildRequestPara($paraTemp = [])
    {
        //除去待签名参数数组中的空值和签名参数
        $paraFilter = $this->paraFilter($paraTemp);
        //对待签名参数数组排序
        $paraSort = $this->argSort($paraFilter);
        //生成签名结果
        $mysign = $this->buildRequestMysign($paraSort);
        //签名结果与签名方式加入请求提交参数组中
        $paraSort['sign'] = $mysign;
        $paraSort['sign_type'] = strtoupper(trim($this->signType));
        return $paraSort;
    }

    /**
     * 生成签名结果
     *
     * @param array $paraSort 已排序要签名的数组
     *
     * @return string 签名结果字符串
     */
    private function buildRequestMysign($paraSort = [])
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($paraSort);
        switch (strtoupper(trim($this->signType))) {
            case 'RSA':
                $mysign = $this->rsaSign($prestr, trim($this->option['privateKey']));
                break;
            default:
                $mysign = '';
        }
        return $mysign;
    }

    /**
     * 获取返回时的签名验证结果
     *
     * @param array $paraTemp 通知返回来的参数数组
     * @param string $sign 返回的签名结果
     *
     * @return bool 签名验证结果
     */
    private function getSignVeryfy($paraTemp = [], $sign = '')
    {
        //除去待签名参数数组中的空值和签名参数
        $paraFilter = $this->paraFilter($paraTemp);
        //对待签名参数数组排序
        $paraSort = $this->argSort($paraFilter);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($paraSort);
        switch (strtoupper(trim($this->signType))) {
            case 'MD5':
                $is_sgin = $this->md5Verify($prestr, $sign, $this->alipayKey);
                break;
            case 'RSA':
                $is_sgin = $this->rsaVerify($prestr, $this->option['publicKey'], $sign);
                break;
            default:
                $is_sgin = false;
        }
        return $is_sgin;
    }

    /**
     * 除去数组中的空值和签名参数
     *
     * @param array $para 签名参数组
     *
     * @return array 去掉空值与签名参数后的新签名参数组
     */
    private function paraFilter($para = [])
    {
        $para_filter = [];
        foreach ($para as $key => $val) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $para_filter[$key] = $para[$key];
            }
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     *
     * @param array $para 排序前的数组
     *
     * @return array 排序后的数组
     */
    private function argSort($para = [])
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * @param array $data 待签名数据
     * @param string $publicKey 支付宝的公钥文件路径
     * @param string $sign 要校对的的签名结果
     *
     * @return bool 验证结果
     */
    private function rsaVerify($data = [], $publicKey = '', $sign = '')
    {
        $res = openssl_get_publickey($publicKey);
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    /**
     * @param array $data 待签名数据
     * @param string $privateKey 商户私钥文件路径
     *
     * @return string 签名结果
     */
    private function rsaSign($data = [], $privateKey = '')
    {
        $priKey = $privateKey;
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     *
     * @param array $para 需要拼接的数组
     *
     * @return string 拼接完成以后的字符串
     */
    private function createLinkstring($para = [])
    {
        $arg = '';
        foreach ($para as $key => $val) {
            $arg .= $key . '=' . $val . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 2);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     *
     * @param array $para 需要拼接的数组
     *
     * @return string 拼接完成以后的字符串
     */
    private function createLinkstringUrlencode($para = [])
    {
        $arg = '';
        foreach ($para as $key => $val) {
            $arg .= $key . '=' . urlencode($val) . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 2);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }
}