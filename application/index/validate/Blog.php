<?php
/**
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/24
 * Time: 21:18
 */

namespace app\index\validate;

use think\Validate;

class Blog extends Validate{

    protected $rule = [
        'title' => 'require|max:50',
        'author' => 'require',
        'desc' => 'require',
    ];

    protected $message  =   [
        'title.require' => '名称必须',
        'title.max' => '名称最多不能超过25个字符',
        'author.require' => '作者必须',
        'desc.require' => '内容必须',
    ];

    protected $scene = [
        'save'  =>  ['title','author','desc'],
        'update'  =>  ['title','author','desc'],
        'create'=> [''],
    ];
}