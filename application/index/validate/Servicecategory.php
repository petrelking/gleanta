<?php
namespace app\index\validate;

use think\Validate;

class Servicecategory extends Validate
{

    protected $rule = [
        'sca_id'   => 'require',
        'type'     => 'require',
        'sca_name' => 'require',
    ];

    protected $message = [
        'sca_id.require'   => '服务分类id不为空',
        'type.require'     => '分类类型不为空',
        'sca_name.require' => '分类名称不为空',
    ];

    protected $scene = [
        'lists' => [''],
        'add'   => ['type', 'sca_name'],
        'edits' => ['sca_id', 'sca_name'],
        'del'   => ['sac_id'],
    ];
}