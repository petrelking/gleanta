<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;
use app\weidoo\helper\Tree;

class ServiceCategory extends Base
{

    /**
     * 服务分类
     */

    public static $model;

    public function lists($info)
    {
        $where['type']   = intval($info['type']);
        $where['biz_id'] = 1;
        $where['status'] = 1;
        $data            = FModel::instance('ServiceCategory')->all($where);
        $tdata           = [];
        foreach ($data as $key => $v) {
            $tdata[$key]['sca_id'] = $v['sca_id'];
            $tdata[$key]['pid']    = $v['pid'];
            $tdata[$key]['name']   = $v['sca_name'];
        }
        Tree::getInstance()->init($tdata, 'sca_id');
        $result = Tree::getInstance()->getTreeArray(0);

        return $result;
    }

    public function add($info)
    {
        $info['pid'] = !empty($info['pid']) ? $info['pid'] : 0;
        $add         = $info;
        unset($add['token']);
        $add['addtime'] = time();
        $add['biz_id']  = 1;
        //$add['id_add_op'] = ADMIN;
        $result = FModel::instance('ServiceCategory')->addInfo($add);
        if (false === $result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }
        $add['sca_id'] = $result;

        return $add;
    }

    public function edits($info)
    {
        self::$model     = FModel::instance('ServiceCategory');
        $where['sca_id'] = intval($info['sca_id']);
        $where['biz_id'] = 1;
        $edata           = self::$model->get($where);
        if (!$edata) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }

        $update = $info;
        unset($update['token']);
        //$update['id_op'] = ADMIN;
        $result = self::$model->editInfo($update, $where);
        if (false === $result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }

        return $update;
    }

    public function del($info)
    {
        self::$model     = FModel::instance('ServiceCategory');
        $where['sca_id'] = intval($info['sca_id']);
        $where['biz_id'] = 1;
        $edata           = self::$model->get($where);
        if (!$edata) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }

        $update['status'] = 2;
        $result           = self::$model->editInfo($update, $where);
        if (false === $result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }

        return true;
    }

}