<?php

namespace app\index\controller;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Serviceitem extends Base
{

    public function lists()
    {
        $info    = input('get.');
        $success = FModel::instance('ServiceItem')->lists($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function cartItem()
    {
        $info    = input('get.');
        $success = FModel::instance('ServiceItem')->cartItem($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function categoryItem()
    {
        $info    = input('get.');
        $success = FModel::instance('ServiceItem')->categoryItem($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function add()
    {
        //self::$validateData只包括必要验证的，数据不够齐;
        $info    = input('post.');
        $success = FModel::instance('ServiceItem')->add($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function edits()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceItem')->edits($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function del()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceItem')->del($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), ['删除成功']);
    }

    public static function getValidate($action)
    {

        switch ($action) {
            case 'cartitem':
                return [
                    'dept_id' => input('get.dept_id'),
                ];
            case 'categoryitem':
                return [
                    'dept_id' => input('get.dept_id'),
                ];
            case 'add':
                return [
                    'si_name'    => input('post.si_name'),
                    'dept_id'    => input('post.dept_id'),
                    'price'      => input('post.price'),
                    'lasttime'   => input('post.lasttime'),
                    'f_category' => input('post.f_category'),
                ];
            case 'edits':
                return [
                    'si_id'      => input('post.si_id'),
                    'si_name'    => input('post.si_name'),
                    'dept_id'    => input('post.dept_id'),
                    'price'      => input('post.price'),
                    'lasttime'   => input('post.lasttime'),
                    'f_category' => input('post.f_category'),
                ];
            case 'del':
                return [
                    'si_id' => input('post.si_id'),
                ];
            default:
                return [''];
        }
    }
}
