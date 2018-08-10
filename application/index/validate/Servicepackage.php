<?php
namespace app\index\validate;

use think\Validate;

class Servicepackage extends Validate
{

    protected $rule = [
        'sp_name'    => 'require',
        'dept_id'    => 'require',
        'price'      => 'require|float',
        'contents'   => 'require',
        'lasttime'   => 'require|number',
        'f_category' => 'require|integer',
    ];

    protected $message = [
        'sp_name.require'    => '套餐名称不为空',
        'dept_id.require'    => '所属门店不为空',
        'price.require'      => '套餐价格不为空',
        'price.float'        => '套餐价格格式不正确',
        'contents.require'   => '套餐内容不为空',
        'lasttime.require'   => '套餐时长不为空',
        'lasttime.number'    => '套餐时长必须为整数',
        'f_category.require' => '套餐所属大类不为空',
        'f_category.integer' => '套餐所属大类不为空',
    ];

    protected $scene = [
        'lists'           => [''],
        'cartpackage'     => ['dept_id'],
        'categorypackage' => ['dept_id'],
        'add'             => ['sp_name', 'dept_id', 'price', 'contents', 'lasttime', 'f_category'],
        'edits'           => ['sp_id', 'sp_name', 'dept_id', 'price', 'contents', 'lasttime', 'f_category'],
        'del'             => ['sp_id'],
    ];
}