<?php
/**
 * User: Blink
 * Email: caogemail@163.com
 * Date: 2017/12/13
 * Time: 下午3:25
 */

namespace Fenghuohuo\Payment\Refund;

/**
 * 支付宝退款
 *
 * Class RefundAlipay
 * @package Fenghuohuo\Payment\Refund
 */
class RefundAlipay
{
    public function request($data = [])
    {
        $aop = new \AopClient();

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2017100809188344';
        $aop->rsaPrivateKey = 'MIIEowIBAAKCAQEAsdrtCEE1qN14Qx2hamf0jTmZlYe21KxuqLA9mikUGRlp0TyciUsviNspEb/iMMhOZb1cAkEPNmcKIPLLUfAhFc+9Jh5jmEbrQc4GwXQKPx75X3PHnxFJtI1XbP3g84r21b2XuxtWYyRySmvYUjL73sVEPoo9dD0Hdg26XWzZr7Bf4Q+LxQbhhcdRUmu7FSQFlB4HZl7lKfIwa9Lmz/yt+LAOjMm7Ewp5fGHNqz57rtO4kYoZIpPP0nLGXNRn3OZHXmGBu6vNar/Rhx7cOw+EViqaAd+leQs13N3ckdTv5p7jIvLkAd5hQqmiz/KycpqhIUeh3CnoUCdZBBpUeOPxKwIDAQABAoIBAQCfLcBdVfePlf/PmdJg6H9wci/qK5fPyjAUHABWXBOXzy3szaGQI1F83CFquGDZpjy8Q/j4L4BNAXWIPaUJRbBm1BGG70XWAQsHTzyuoYuWKMOp0XbE//UGekXXz3Uoo6P9cn09Qd9URkdgIhtuniDcpZNJofIUeZaW6vx0JzrBSMr4oTDkV7vwSWy4LAfUBDcZhjwDhqVOxtnErJSF5cnkcYe+zXefFKIFH9NDZmoeuxXmvKfroYFoF5gmxidR567az+EVEv3nie1AeN6ltZZz7jBbfb9fSrWvDZIo852VBAwvJGuN2uJx17M+IZiZbYMqyuyxeRvbISqGNAapMRkpAoGBAOvcBdXqtYyaXcP2F4N8757+kGeMtwr6SoN+TzLXlCGy3EizWmaZif9LYFo1blDHf7R9f/JErirU4BinMSGcGDxDB0ao3XQhdMYC59SUpSamoZe/djyGGkoKquL/QsbAmXquloQIrzvjG01B9GwAPH9DIKiJjpFtYH446WCADDKVAoGBAMEK6FC1G4m4pI9tRMnkfMAnV/pahyL+MzlJt6uY3jdExyVAaPpPb4eNX1IyhK1GbOvgvKGIx1QXgcB/QlRjt211czjtI9qXVei+IsGY5KraK1LP5llOcmqHLlALT8LeM1E/ZRrwVdy5ESWRUvcXWq1oi8CoFiM9WDG086DB6GS/AoGAG6ZwnLJmVDRol6eK/CQyZz5KqYkKkGPlRmMRX9F8ETNJtdAo4exQDDjG8+xdoWSx6PPpgvpgPK82Ek3PabRF9xKYkMUNKSce7HO8v+QpNE3GleahMtk5zcJQZEouhuRfWc2L4bCu8mNeeQEpaVvhu72f34I4z322GTYMzf5U770CgYASBJeceNsCJsbNUFCcW4Q9Vy7CAlMctAdJ8JSBIrqTBAtmcbWar3FkgJCGFosFtU4TUh2pIiAgvDTzJ62kPM5xHAZOKOwwyfFN0PljgobNX6GtkBQa/9R0Fvv+op+mO6Ekkzs3oXkSKf/stry8rWRcTSbGK6otTdt+gDpBJfgv9wKBgHpnfpIW/irfRbVLK7/rRJGiBbe3Bvy76n5P9Sqe3d2OyXfgfGI4YucLLZT3kUEgVUvae4kexewDTj6owNb4pgxMfsJuOYRbjVSHosY5LbtZX2GDGNDAl7wRLtNqd4wvTOGOq6lpX5GwDOhwphhYsKIWngU4rwZibAs09vnFEFb5';
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoy3Y841Gxly7uuM/l4YldvmZvNCzByKdDnsMO/EZZ+Ca57IQFCZTOVhHmP3z43GP/4ksX7YgFO3E1LPQNBr3vjxXzD5Pg5GVvKV419Aq4ImesDfSjIZNdjFNNL8k0OuPZEsrEYnOdN1vj3PE4ibqewxy53yo/VzQkTvwhJ3n6ya/WfHof+BUOAaZo381scpYPyShAwCljkUjG8kSs3fi1T+WBqi+s4i4itqsH6wFn068EJHnuMG4jDEVzaXD+ecMK7vSWbU/vr57Q2piy8MAXThirUeKrBubUhpQ864R0t6Co9qCFsXz+Xza8Ti3qfmeRB+o+leu3NL7D0RFvArxdQIDAQAB';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'UTF-8';
        $aop->format = 'json';
        $request = new \AlipayTradeRefundRequest();
        $request->setBizContent(json_encode([
            'out_trade_no'  => $data['payOrderId'],
            'trade_no'      => $data['transactionId'],
            'refund_amount' => (float)bcdiv($data['amount'], 100, 2),
            'refund_reason' => $data['reason']
        ]));
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return $result;
        } else {
            throw new \Exception(json_encode($result, JSON_UNESCAPED_UNICODE), -1);
        }
    }
}
//参数示例
//"{" .
//"\"out_trade_no\":\"20150320010101001\"," .
//"\"trade_no\":\"2014112611001004680073956707\"," .
//"\"refund_amount\":200.12," .
//"\"refund_reason\":\"正常退款\"," .
//"\"out_request_no\":\"HZ01RF001\"," .
//"\"operator_id\":\"OP001\"," .
//"\"store_id\":\"NJ_S_001\"," .
//"\"terminal_id\":\"NJ_T_001\"" .
//"  }"

//响应示例
//{
//    "alipay_trade_refund_response": {
//    "code": "10000",
//    "msg": "Success",
//    "trade_no": "支付宝交易号",
//    "out_trade_no": "6823789339978248",
//    "buyer_logon_id": "159****5620",
//    "fund_change": "Y",
//    "refund_fee": 88.88,
//    "gmt_refund_pay": "2014-11-27 15:45:57",
//    "refund_detail_item_list": [
//        {
//            "fund_channel": "ALIPAYACCOUNT",
//            "amount": 10,
//            "real_amount": 11.21,
//            "fund_type": "DEBIT_CARD"
//        }
//    ],
//    "store_name": "望湘园联洋店",
//    "buyer_user_id": "2088101117955611"
//},
//"sign": "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
//}

//异常示例
//{
//    "alipay_trade_refund_response": {
//        "code": "20000",
//        "msg": "Service Currently Unavailable",
//        "sub_code": "isp.unknow-error",
//        "sub_msg": "系统繁忙"
//    },
//    "sign": "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
//}