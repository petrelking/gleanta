<?php
namespace app\index\validate;

use think\Validate;

class Business extends Validate
{

    protected $regex = ['mobile_format' => '/1[3456789]{1}\d{9}$/'];

    protected $rule = [
        'mobile'    => 'require|mobile_format',
        'user_id'   => 'require',
        'user_pass' => 'require|length:6,16',
        'old_pwd'   => 'require|length:6,16',
        'status'    => 'require|between:1,2'
    ];

    protected $message  =   [
        'mobile.require'       => '缺少参数[手机号码]',
        'mobile.mobile_format' => '手机格式不正确',
        'user_id.require'      => '缺少参数[账户]',
        'user_pass.require'    => '缺少参数[密码]',
        'old_pwd.require'      => '缺少参数[原密码]',
        'status.require'       => '缺少参数[修改状态]',
    ];

    protected $scene = [
        'searchmobile'  => ['mobile'],
        'savelogin'     => ['user_id','user_pass'],
        'updatepwd'     => ['old_pwd','user_pass'],
        'outlogin'      => [''],
        'userlist'      => [''],
        'adduser'       => ['mobile',"user_pass"],
        'upuserstatus'  => ['user_id','status'],
    ];
}