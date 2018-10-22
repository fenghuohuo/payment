<?php
/**
 * Created by PhpStorm.
 * User: fenghuohuo
 * Date: 2018/10/19
 * Time: 上午10:32
 */
namespace Fenghuohu\Test;

use PHPUnit\Framework\TestCase;
use Fenghuohuo\Payment\Payment;

require_once __DIR__ . '/../vendor/autoload.php';

class Test extends TestCase
{
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

//    public function testNotify()
//    {
//        try {
//            $channel = 'alipay';
//            $data = json_encode([
//                'out_trade_no' => '20171019162629312603540591',
//                'partner'      => '2088821389843589',
//                'payment_type' => 1,
//                'seller_id'    => 'zhangyuzhuawawa@163.com',
//                'service'      => 'mobile.securitypay.pay',
//                'subject'      => '120金币',
//                'total_fee'    => 12,
//                'sign'         => 'qbSeU8vYSl6Oj/KvA6qc22x1DIc9EsubNEZrJxrPy1Dqor1rUEzZMiEFfYI0LPp70mpVunBmxQTBsah6PSnxFoBMfRJ/y7ZGe9bJFg5ttGCv968SuOf5olLpp1kuU9cihu/lZ1V+WIZxV4nZ6IIVLij5otyipunfYQ6IOoP8HHk=',
//                'sign_type'    => 'RSA'
//            ]);
//
//            $payment = new Payment($channel);
//            $processResult = $payment->orderProcess($data);
//        } catch (\Exception $exception) {
//            throw $exception;
//        }
//    }
}
