<?php
/**
 * User: Blink
 * Email: caogemail@163.com
 * Date: 2017/12/13
 * Time: 下午3:07
 */

namespace Fenghuohuo\Payment\Refund;

use Log;
use Phalcon\Db;

/**
 * 退款
 *
 * Class Refund
 * @package Fenghuohuo\Payment\Refund
 */
class Refund
{
    /**
     * 退款队列
     *
     * @return bool
     */
    public function addQueue()
    {
        if (redis('cache')->set('STR:PAYMENT:REFUND:QUEUE:LOCK', 1, ['nx', 'ex' => 4])) {
            Log::info('更新退款队列');
            $queueKey = 'SET:PAYMENT:REFUND:QUEUE';
            if (redis('cache')->sCard($queueKey) >= 20) {
                Log::error('队列过长');
                return false;
            }
            // 1分钟内处理失败的任务不加进队列
            $recordIds = db('rich')->fetchAll(sprintf("SELECT id FROM `payment_refund` WHERE status = 0 AND times < 50 AND lastModify <= '%s' LIMIT 5",
                date('Y-m-d H:i:s', time() - 60)
            ));
            foreach ($recordIds as $v) {
                redis('cache')->sAdd($queueKey, $v['id']);

                // 更新为开始处理时间
                db('rich')->execute(sprintf(
                    'UPDATE payment_refund SET lastModify = "%s", times = times + 1 WHERE id = %d AND status = 0'
                    , date('Y-m-d H:i:s')
                    , $v['id']
                ));
            }
        }
        return true;
    }

    public function cosumeQueue()
    {
        $queueKey = 'SET:PAYMENT:REFUND:QUEUE';
        $recordId = redis('cache')->sPop($queueKey);
        if (!$recordId) {
            return false;
        }
        Log::info(sprintf('开始处理队列，recordId[%d]', $recordId));
        $record = db('rich')->fetchOne(sprintf("SELECT * FROM `payment_refund` WHERE id = %d AND status = 0",
            $recordId));
        if (!$record) {
            Log::error(sprintf('待处理记录不存在[%d]', $recordId));
            return false;
        }

        $payOrder = db('rich')->fetchOne(
            'SELECT * FROM payment_order WHERE orderId = :orderId AND paid = 1',
            Db::FETCH_ASSOC,
            [
                'orderId' => $record['payOrderId']
            ]
        );
        if (!$payOrder) {
            Log::error(sprintf('退款订单不存在[%s]', $record['payOrderId']));
        }
        $uid = $payOrder['uid'];
        // 检查订单、uid、金额
        if ($uid != $record['uid'] || $payOrder['amount'] != $record['amount']) {
            Log::error('订单金额/用户与退款记录信息不匹配');
        }

        if ($record['refundOrderId']) {
            $refundOrderId = $record['refundOrderId'];
        } else {
            $refundOrderId = date('YmdHis') . sprintf('%05d', crc32(uniqid($uid))) . rand(10, 99);
            db('rich')->updateAsDict(
                'payment_refund',
                [
                    'refundOrderId' => $refundOrderId
                ],
                sprintf('id = %d', $recordId)
            );
        }

        $refundData = [
            'refundOrderId' => $refundOrderId, // 退款单号
            'payOrderId'    => $payOrder['orderId'], // 退款商户订单号
            'transactionId' => $payOrder['transactionId'], // 退款支付渠道订单号
            'amount'        => $payOrder['amount'], // 退款金额，单位分
            'reason'        => $record['reason'] ? $record['reason'] : sprintf('退款-%s', $payOrder['subject']) // 退款原因
        ];

        try {
            $adapter = $this->getAdapter($payOrder['channel']);
            $result = $adapter->request($refundData);

            db('rich')->updateAsDict(
                'payment_refund',
                [
                    'status'   => 2,
                    'response' => json_encode($result, JSON_UNESCAPED_UNICODE)
                ],
                sprintf('id = %d AND status = 0', $recordId)
            );
        } catch (\Exception $e) {
            db('rich')->updateAsDict(
                'payment_refund',
                [
                    'response' => $e->getMessage()
                ],
                sprintf('id = %d AND status = 0', $recordId)
            );

            Log::error(sprintf('退款失败[%s]', $e->getMessage()));
        }
    }

    public function getAdapter($payChannel = '')
    {
        if (in_array($payChannel, ['alipay'])) {
            return new RefundAlipay();
        } elseif (in_array($payChannel, ['wx'])) {
            return new RefundWx();
        } else {
            throw new \Exception('channel错误');
        }
    }
}