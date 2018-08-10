<?php
namespace app\index\validate;

use think\Validate;

class Dept extends Validate
{


    protected $rule = [
        'dept_name' => 'require',
        'dept_id'   => 'require',
    ];

    protected $message  =   [
        'dept_name.require'       => '缺少参数【名称】',
        'dept_id.require'         => '缺少ID',
    ];

    protected $scene = [
        'lists'        => [''],
        'adddata'      => ['dept_name'],
        'editdata'     => ['dept_id','dept_name'],
        'one'          => ['dept_id'],
    ];
}