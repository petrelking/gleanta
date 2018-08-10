<?php
namespace app\index\validate;

use think\Validate;

class Serviceitem extends Validate
{

    protected $rule = [
        'si_id'      => 'require',
        'si_name'    => 'require',
        'dept_id'    => 'require',
        'price'      => 'require|float',
        'lasttime'   => 'require|number',
        'f_category' => 'require|integer',
    ];

    protected $message = [
        'si_id'              => '服务项目id不为空',
        'si_name.require'    => '服务项目名不为空',
        'dept_id.require'    => '所属门店不为空',
        'price.require'      => '统一售价不为空',
        'price.float'        => '统一售价格式不正确',
        'lasttime.require'   => '项目时长不为空',
        'lasttime.number'    => '项目时长必须为整数',
        'f_category.require' => '项目所属大类不为空',
        'f_category.integer' => '项目所属大类不为空',
    ];

    protected $scene = [
        'lists'        => [''],
        'cartitem'     => ['dept_id'],
        'categoryitem' => ['dept_id'],
        'add'          => ['si_name', 'dept_id', 'price', 'lasttime', 'f_category'],
        'edits'        => ['si_id', 'si_name', 'dept_id', 'price', 'lasttime', 'f_category'],
        'del'          => ['si_id'],
    ];
}