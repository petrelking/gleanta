<?php

namespace app\index\controller;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Servicecategory extends Base
{

    public function lists()
    {
        $info    = input('get.');
        $success = FModel::instance('ServiceCategory')->lists($info);
        return out_success_json(lang('execute success'), $success);
    }

    public function add()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceCategory')->add($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function edits()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceCategory')->edits($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public function del()
    {
        $info    = input('post.');
        $success = FModel::instance('ServiceCategory')->del($info);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), ['删除成功']);
    }

    public static function getValidate($action)
    {

        switch ($action) {
            case 'add':
                return [
                    'type'     => input('post.type'),
                    'sca_name' => input('post.sca_name'),
                ];
            case 'edits':
                return [
                    'sca_id'   => input('post.sca_id'),
                    'type'     => input('post.type'),
                    'sca_name' => input('post.sca_name'),
                ];
            case 'del':
                return [
                    'sca_id' => input('post.sca_id'),
                ];
            default:
                return [''];
        }
    }
}
