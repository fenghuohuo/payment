# payment
---
## 支付sdk集合

* 支持composer
* 支持支付宝
* 支持微信

## Installation

```shell
composer require "fenghuohuo/payment" -vvv
```

## Usage
###channel 支付平台(支付宝/微信)
|支付渠道 |参数 |
|---|---|
|支付宝APP支付|alipay|
|支付宝web支付|alipay_wap|
|支付宝二维码支付|alipay_qr|
|支付宝手机网站支付|alipay_pc_direct|
|微信APP支付|wx|
|微信公众号支付|wx_pub|
|微信二维码支付|wx_pub_qr|
|微信H5支付|wx_h5|

```$xslt
    try {
            $payment = new Payment($channel, $platform);
            $processResult = $payment->orderProcess($data, [
                'appId'    => $appId,
                'cid'      => $cid,
                'platform' => $platform,
                'channel'  => $channel
            ]);
            // code: 0返回成功，1验证失败，2,订单不存在，3金额不批对，4记录添加失败，5钻石发放失败
            $this->response->setData(['response' => $processResult['response']]);
            if ($processResult['code'] == 0) {
                $this->send();
                if (isset($processResult['order'])) {
                    $order = $processResult['order'];
                    $setData = [
                        'channel'     => $order['channel'],
                        'uid'         => $order['uid'],
                        'pid'         => $order['pid'],
                        'orderId'     => $order['orderId'],
                        'amount'      => $order['amount'],
                        'clientIp'    => $order['clientIp'],
                        'cid'         => $order['cid'],
                        'client'      => $order['client'],
                        'platform'    => $order['platform'],
                        'paySource'   => $order['paySource'],
                        'sendDiamond' => $order['sendDiamond'],
                        'depDiamond'  => $order['depDiamond'],
                        'extra'       => json_decode($order['extra'], true),
                        'paid'        => 1, //已支付
                        'agent'       => 0
                    ];
                    //发送事件到nsq进行异步任务处理
                    Nsq::send($setData);
                }
            } else {
                $this->send($processResult['code']);
            }
        } catch (\Exception $e) {
            Log::error('验证订单发生失败' . $e->getMessage() . 'data:' . json_encode($data));
            $this->send(static::ERROR_HANDLE_NOTIFY_FAIL);
        }
```