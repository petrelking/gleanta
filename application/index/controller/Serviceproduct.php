<?php

namespace app\index\controller;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Serviceproduct extends Base
{

    public function lists()
    {
        $info    = input('get.');
        $success = FModel::instance('ServiceProduct')->lists($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function categoryProduct()
    {
        $info    = input('get.');
        $success = FModel::instance('ServiceProduct')->categoryProduct($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function add()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceProduct')->add($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function edits()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceProduct')->edits($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function del()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceProduct')->del($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), ['删除成功']);
    }

    public static function getValidate($action)
    {

        switch ($action) {
            case 'categoryproduct':
                return [
                    'dept_id' => input('get.dept_id'),
                ];
            case 'add':
                return [
                    'sr_name'    => input('post.sr_name'),
                    'dept_id'    => input('post.dept_id'),
                    'price'      => input('post.price'),
                    'sr_type'    => input('post.sr_type'),
                    'unit'       => input('post.unit'),
                    'f_category' => input('post.f_category'),
                ];
            case 'edits':
                return [
                    'sr_id'      => input('post.sr_id'),
                    'sr_name'    => input('post.sr_name'),
                    'dept_id'    => input('post.dept_id'),
                    'price'      => input('post.price'),
                    'sr_type'    => input('post.sr_type'),
                    'unit'       => input('post.unit'),
                    'f_category' => input('post.f_category'),
                ];
            case 'del':
                return [
                    'sr_id' => input('post.sr_id'),
                ];
            default:
                return [''];
        }
    }
}
