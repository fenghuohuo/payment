# payment
---

[![Latest Stable Version](https://poser.pugx.org/fenghuohuo/payment/v/stable)](https://packagist.org/packages/fenghuohuo/payment)
[![Total Downloads](https://poser.pugx.org/fenghuohuo/payment/downloads)](https://packagist.org/packages/fenghuohuo/payment)
[![Latest Unstable Version](https://poser.pugx.org/fenghuohuo/payment/v/unstable)](https://packagist.org/packages/fenghuohuo/payment)
[![License](https://poser.pugx.org/fenghuohuo/payment/license)](https://packagist.org/packages/fenghuohuo/payment)

## Installation

```shell
composer require "fenghuohuo/payment" -vvv
```

## Usage
### channel 支付平台(支付宝/微信)

| 支付渠道 |   参数     |
| :-----: | :-------: |
| 支付宝APP支付      | alipay  |
| 付宝web支付 | alipay_wap  |
| 支付宝二维码支付     | alipay_qr    |
| 支付宝手机网站支付    | alipay_pc_direct    |
| 微信APP支付     | wx    |
| 微信公众号支付     | wx_pub  |
| 微信二维码支付     | wx_pub_qr |
| 微信H5支付      | wx_h5 |

### 配置文件 
```$xslt
/src/Config.php
```

### 用例
```$xslt
    public function testCreate()
        {
            try {
                $channel = 'alipay';
                $order = (object)[
                    'orderId'  => '111',
                    'amount'   => 100,
                    'subject'  => '测试商品',
                    'body'     => '商品详情',
                    'pid'      => 1, // 商品id
                    'clientIp' => '111', // 客户端设备id
                    'extra'    => '', // 额外参数 json {"openid":"111"}
                ];
                $payment = new Payment($channel);
                $payment->createOrder($order);
    
                var_dump($order);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
```