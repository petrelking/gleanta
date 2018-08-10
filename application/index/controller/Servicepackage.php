<?php

namespace app\index\controller;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Servicepackage extends Base
{

    public function lists()
    {
        $info    = input('get.');
        $success = FModel::instance('ServicePackage')->lists($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function cartPackage()
    {
        $info    = input('get.');
        $success = FModel::instance('ServicePackage')->cartPackage($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function categoryPackage()
    {
        $info    = input('get.');
        $success = FModel::instance('ServicePackage')->categoryPackage($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function add()
    {
        $info    = input('post.');
        $success = FModel::instance('ServicePackage')->add($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function edits()
    {
        $info    = input('post.');
        $success = FModel::instance('ServicePackage')->edits($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function del()
    {
        $info    = input('post.');
        $success = FModel::instance('ServicePackage')->del($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), ['删除成功']);
    }

    public static function getValidate($action)
    {

        switch ($action) {
            case 'cartpackage':
                return [
                    'dept_id' => input('get.dept_id'),
                ];
            case 'categorypackage':
                return [
                    'dept_id' => input('get.dept_id'),
                ];
            case 'add':
                return [
                    'sp_name'    => input('post.sp_name'),
                    'dept_id'    => input('post.dept_id'),
                    'price'      => input('post.price'),
                    'contents'   => input('post.contents'),
                    'lasttime'   => input('post.lasttime'),
                    'f_category' => input('post.f_category'),
                ];
            case 'edits':
                return [
                    'sp_id'      => input('post.sp_id'),
                    'sp_name'    => input('post.sp_name'),
                    'dept_id'    => input('post.dept_id'),
                    'price'      => input('post.price'),
                    'contents'   => input('post.contents'),
                    'lasttime'   => input('post.lasttime'),
                    'f_category' => input('post.f_category'),
                ];
            case 'del':
                return [
                    'sp_id' => input('post.sp_id'),
                ];
            default:
                return [''];
        }
    }
}
