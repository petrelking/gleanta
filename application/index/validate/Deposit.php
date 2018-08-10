<?php
namespace app\index\validate;

use think\Validate;

class Deposit extends Validate
{


    protected $rule = [
        'pay_type'     => 'require',
        'dep_amount'   => 'require',
        'cart_id'      => 'require',
    ];

    protected $message  =   [
        'pay_type.require'       => '缺少参数【类型】',
        'dep_amount.require'     => '缺少参数【金额】',
        'cart_id.require'        => '缺少参数【预约ID】'
    ];

    protected $scene = [
        'adddata'      => ['pay_type','dep_amount','cart_id'],
    ];
}