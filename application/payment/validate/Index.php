<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2018/7/27
 * Time: 17:55
 */

namespace app\payment\validate;


use think\Validate;

class Index extends Validate
{
    protected $rule = [
        'order_id'     => 'require',
        'pay_id'  => 'require',
        'dept_id'    => 'require',
        'cus_id'  => 'require',
        'pay_amount' => 'require',
        'pay_type' => 'require',
        'pay_steps'    => 'require',
        'order_no'    => 'require',
        'out_trade_no'  => 'require',
        'total_amount' => 'require',
        'gpon_cardno'    => 'require',
        'coupon_id'    => 'require',
    ];

    protected $message  =   [
        'order_id.require' => '缺少参数[订单号]',
        'pay_id.require' => '缺少参数[支付id]',
        'dept_id.require' => '缺少参数[部门]',
        'cus_id.require' => '缺少参数[客户]',
        'pay_amount.require' => '缺少参数[付款金额]',
        'pay_type.require' => '缺少参数[付款类型]',
        'pay_steps.require' => '缺少参数[期数]',
        'order_no.require' => '缺少参数[订单内部号]',
        'out_trade_no.require' => '缺少参数[外部订单号]',
        'total_amount.require' => '缺少参数[总金额-第三方]',
        'gpon_cardno.require' => '缺少参数[团购券号]',
        'coupon_id.require' => '缺少参数[使用的券编号]',
    ];

    protected $scene = [
        'addpay'=>['pay_name','pay_type','pay_fee','pay_min','pay_max'],
        'addcoupon'=>[],
        'addgroupon'=>[],
        'addonline'=>[],
        'getPaymentsList'=>[],
    ];
}