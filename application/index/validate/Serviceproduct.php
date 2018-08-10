<?php
namespace app\index\validate;

use think\Validate;

class Serviceproduct extends Validate
{

    protected $rule = [
        'sr_id'      => 'require',
        'sr_name'    => 'require',
        'dept_id'    => 'require',
        'price'      => 'require|float',
        'sr_type'    => 'require|integer',
        'unit'       => 'require',
        'f_category' => 'require|integer',
    ];

    protected $message = [
        'sr_id'              => '服务产品id不为空',
        'sr_name.require'    => '服务产品不为空',
        'dept_id.require'    => '所属门店不为空',
        'price.require'      => '统一售价不为空',
        'price.float'        => '统一售价格式不正确',
        'sr_type.require'    => '服务产品类型不为空',
        'sr_type.integer'    => '服务产品类型不为空',
        'unit.require'       => '服务产品单位不为空',
        'f_category.require' => '项目所属大类不为空',
        'f_category.integer' => '项目所属大类不为空',
    ];

    protected $scene = [
        'lists'           => [''],
        'categoryproduct' => ['dept_id'],
        'add'             => ['sr_name', 'dept_id', 'price', 'sr_type', 'unit', 'f_category'],
        'edits'           => ['sr_id', 'sr_name', 'dept_id', 'price', 'sr_type', 'unit', 'f_category'],
        'del'             => ['sr_id'],
    ];
}