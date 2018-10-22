<?php
/**
 * User: Blink
 * Email: caogemail@163.com
 * Date: 2017/10/2
 * Time: 下午12:16
 */

namespace Fenghuohuo\Payment;


class ConstDefined
{
    const OPTION_PAY = 1; // 充值
    const OPTION_IC_PAY = 33; // 代充
    const OPTION_GIFT = 2; // 送礼
    const OPTION_PAY_SEND = 3; // 充值赠送
    const OPTION_EXCHANGE = 4; // 兑换
    const OPTION_EXPRESS = 5; // 兑换
    // 冻结功能
    const OPTION_DIAMOND_FROZEN= 6;
    const OPTION_DIAMOND_UNFROZEN= 7;
    const OPTION_COUPON = 8; // 兑换



    const OPTION_WITHDRAW = 4; // 提现
    const OPTION_REMIT = 5; // 划账
    const OPTION_DANMU = 6; // 弹幕消费

    const OPTION_NICKNAME = 8; // 改昵称消费
    const OPTION_BOX = 10; // 礼物触发的宝箱
    const OPTION_LIVE_SEED = 11; //观看直播间领取礼物
    const OPTION_BILL = 12; //平账类型

    const OPTION_QUANMIN_ACT = 13; // 任务赠送 对应老系统3
    const OPTION_QUANMIN_MOBILE_REWARD = 14; // 手机注册奖励 对应老系统5
    const OPTION_QUANMIN_OLYMPIC_REWARD = 15; // 奥运活动奖励 对应老系统6
    const OPTION_QUANMIN_LPL_REWARD = 16; // LPL活动奖励 对应老系统7
    const OPTION_QUANMIN_ACT_REWARD = 17; // 活动奖励 对应老系统8
    const OPTION_QUANMIN_REDBAG = 18; // 红包领取 对应老系统2
    const OPTION_QUANMIN_ACT_DISNEY = 19; // 牛币买迪士尼门票
    const OPTION_SEED_MANUAL = 20; // 后台增减种子

    const OPTION_BOUNTY = 21; // 赏金任务
    const OPTION_LUCKGIFT_SEND = 22; // 幸运礼物
    const OPTION_LUCKGIFT_BALANCE = 23; // 幸运礼物结算
    const OPTION_REMIT_DIAMOND2STARLIGHT = 24; // 划账：牛币-》星光
    const OPTION_PROP = 25; // 使用道具消费
    const OPTION_PRETTYNO = 26; //购买靓号消费
    const OPTION_EXCAHNGE_YUANBAO = 27; //钻石兑换元宝

    //竞猜
    const OPTION_GUESS_BANKER = 28; //竞猜做庄
    const OPTION_GUESS_BET = 29; //竞猜下注
    const OPTION_GUESS_BACK = 30; //返还
    const OPTION_GUESS_BLANCE = 31; //竞猜结算
    const OPTION_GUESS_FLOW = 32; //流局
    const OPTION_GUESS_INCOME = 33; //收入

    const OPTION_WATCH_AWARD= 34; //观看直播间奖励
    const OPTION_FIGHT_MASSFANS= 35; //粉丝绑定主播军团

    const OPTION_COLOR_BARRAGE = 36; //炫彩弹幕消费
    const OPTION_LUCKY_GIFT_SEND_QUICKSILVER = 37; // 幸运礼物 2017-08-07
    const OPTION_SIGN_CARD = 38; // 补签卡 2017-08-10
    const OPTION_PRETTYNO_RESERVE= 39; // 靓号预约 2017-08-14

    // 开通贵族
    const OPTION_BUY_NOBLEMAN= 41;
    // 返还贵族牛币
    const OPTION_NOBLEMAN_RESTORE= 42;
    // 换取抽奖机会43

    // 续费贵族
    const OPTION_RENEWAL_NOBLEMAN= 44;
    // 赠送贵族
    const OPTION_SEND_NOBLEMAN= 45;
    // 回收贵族牛币
    const OPTION_NOBLEMAN_RETRIEVE= 46;

    const TYPE_DIAMOND = 1;
    const TYPE_FISHBALL = 2;
    const TYPE_COUPON = 3;
    const TYPE_CARD_WEEK = 4;
    const TYPE_CARD_MONTH = 5;

    const ACTION_INC = 1;
    const ACTION_DEC = 2;
    /**
     * 冻结
     */
    const ACTION_FREEZE = 3;
    /**
     * 解冻
     */
    const ACTION_UNFREEZE = 4;
}