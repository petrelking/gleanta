<?php
namespace app\index\validate;

use think\Validate;

class Cart extends Validate
{

    protected $rule = [
        'cart_id'      => 'require',
        'cus_id'       => 'require',
        'btime'        => 'require',
        'etime'        => 'require',
        'dept_id'      => 'require',
        'room_id'      => 'require',
        'follow_type'  => 'require',
        'follow_status'=> 'require',
        'follow_time'  => 'require',
        'content'      => 'require',
    ];

    protected $message = [
        'cart_id.require'      => '选择预约信息',
        'cus_id.require'       => '缺少参数【客户】',
        'btime.require'        => '缺少参数【预约开始时间】',
        'etime.require'        => '缺少参数【预约结束时间】',
        'dept_id.require'      => '缺少参数【门店】',
        'room_id.require'      => '缺少参数【房间】',
        'follow_type.require'  => '缺少参数【跟进方式】',
        'follow_status.require'=> '缺少参数【跟进状态】',
        'follow_time.require'  => '缺少参数【跟进时间】',
        'content.require'      => '缺少参数【跟进内容】',
    ];

    protected $scene = [
        'deldata'     => ["cart_id"],
        'adddata'     => ['cus_id','btime','etime','dept_id','room_id'],
        'tbadd'       => ['btime','etime','dept_id','room_id'],
        'editdata'    => ['cart_id','cus_id','btime','etime','dept_id','room_id'],
        'addfollow'   => ['cart_id','cus_id','follow_type','follow_status','follow_time','content'],
        'getdetail'   => ['cart_id'],
        'lists'       => [''],
    ];
}