<?php
// +----------------------------------------------------------------------
// | api设置
// +----------------------------------------------------------------------

return [
    //状态码
    "code"=>[
        0=>'fail',
        1=>'ok',
        20001=>'操作成功',
        43001=>'操作失败',
        43002=>'验证失败',
        43003=>'数据不存在',
        43004=>'参数错误',
        43005=>'无权操作',
        43006=>'未定义验证数据',
    ],
    //忽略认证的接口(token)
    "ignore_token"=>[
        'formToken',
        'index',
        'user/searchMobile',
        'user/saveLogin',
    ],
    //允许访问域名
    "allow_origin"=>[
        'http://s.api.wdtest.com',
        'http://saas.api.wd.com',
        'http://saas.api.beiface.com',
        'http://byerp.beiyan.com',
        'http://127.0.0.1',
    ],
    //允许访问请求类型
    'allow_method'=>[
        'OPTIONS',
        'POST',
        'GET'
    ],
    //TOKEN开关
    'token'=>true,
    //用户token名称
    'token_name'=>'token',
    //表单CSRF开关
    'csrf'=>true,
    //表单csrf名称
    'csrf_name'=>'formToken',
];