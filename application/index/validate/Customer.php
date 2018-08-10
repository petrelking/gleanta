<?php

namespace app\index\validate;

use think\Validate;

class Customer extends Validate
{

    protected $regex = ['mobile_format' => '/1[3456789]{1}\d{9}$/'];

    protected $rule = [
        'biz_id'       => 'require',
        'mobile'       => 'require|mobile_format',
        'cus_name'     => 'require',
        'channel_id'   => 'require',
        'add_dept_id'  => 'require',
        'cus_sex'      => 'require',
    ];

    protected $message = [
        'mobile.require'       => '手机号码不为空',
        'mobile.mobile_format' => '手机格式不正确',
        'biz_id.require'       => "缺少商户ID",
        'cus_name'             => '缺少参数【姓名】',
        'channel_id'           => '缺少参数【来源】',
        'dept_id'              => '缺少参数【门店】',
        'cus_sex'              => '缺少参数【性别】',
    ];

    protected $scene = [
        'checkMobile'  => ["mobile"],
        'adddata'      => ['mobile','cus_name','channel_id','dept_id','cus_sex']
    ];

}