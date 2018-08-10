<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2018/7/26
 * Time: 13:59
 */

namespace app\index\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'cart_id'      => "require",
        'cus_id'       => 'require',
        'appoint_time' => 'require',
        'items'        => 'require',
        'order_id'     => 'require',
        'dept_id'      => 'require',
    ];

    protected $message  =   [
        'cart_id.require'       => '缺少参数[预约ID]',
        'cus_id.require'        => '缺少参数[客户ID]',
        'items.require'         => '缺少参数[预约详情]',
        'order_id.require'      => '缺少参数[订单ID]',
        'dept_id.require'       => '缺少参数[门店ID]',
        'appoint_time.require'  => '缺少参数[时间]',
    ];

    protected $scene = [
        'saveorder' => ['cus_id','items','dept_id','appoint_time'],
        'detail'    => ['order_id'],
        'lists'     => [''],
        'optrecord' => ['order_id'],
        'cancel'    => ['order_id'],
    ];
}