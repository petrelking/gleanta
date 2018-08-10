<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;
use app\weidoo\helper\Tree;

class ServiceProduct extends Base
{

    /**
     * 服务产品,type=2
     */

    public static $model;

    public function lists($info)
    {
        $where['biz_id'] = 1;
        if (!empty($info['sr_type'])) {
            $where['sr_type'] = $info['sr_type'];
        }
        if (!empty($info['f_category'])) {
            $where['f_category'] = $info['f_category'];
        }
        if (!empty($info['status'])) {
            $where['status'] = intval($info['status']);
        }
        $search = '';
        if (!empty($info['search'])) {
            $search = $info['search'];
        }
        $exp = '';
        if (!empty($info['dept_id'])) {
            $exp = 'FIND_IN_SET(' . intval($info['dept_id']) . ',dept_id)';
        }
        $data   = FModel::instance('ServiceProduct')->getProduct($exp, $where, $search);
        $result = [];
        foreach ($data as $key => $v) {
            $result[$key]            = $v;
            $result[$key]['addtime'] = date_for($v['addtime']);
        }
        return $result;
    }

    /**
     * 产品分类及其数据列表
     */
    public function categoryProduct($info)
    {
        $where['type']   = 2;
        $where['biz_id'] = 1;
        $where['status'] = 1;
        $data            = FModel::instance('ServiceCategory')->all($where);
        $category        = [];
        foreach ($data as $v) {
            $category[$v['sca_id']]['id']      = $v['sca_id'];
            $category[$v['sca_id']]['biz_id']  = $v['biz_id'];
            $category[$v['sca_id']]['type']    = $v['type'];
            $category[$v['sca_id']]['pid']     = $v['pid'];
            $category[$v['sca_id']]['name']    = $v['sca_name'];
            $category[$v['sca_id']]['service'] = [];
        }

        unset($where['type']);
        $arr_dept = explode(',', $info['dept_id']);
        $exp      = '';
        foreach ($arr_dept as $key => $v) {
            if (0 === $key) {
                $exp .= 'FIND_IN_SET(' . intval($v) . ',dept_id)';
                continue;
            }
            $exp .= ' or FIND_IN_SET(' . intval($v) . ',dept_id)';
        }
        $search = '';
        if (!empty($info['search'])) {
            $search = $info['search'];
        }
        $data = FModel::instance('ServiceProduct')->getProduct($exp, $where, $search);

        foreach ($data as $key => $v) {
            if (isset($category[$v['s_category']])) {
                $category[$v['s_category']]['service'][$key]['id']   = $v['sr_id'];
                $category[$v['s_category']]['service'][$key]['name'] = $v['sr_name'];
                $category[$v['s_category']]['service'][$key]['type'] = 2;
                continue;
            }
            if (isset($category[$v['f_category']])) {
                $category[$v['f_category']]['service'][$key]['id']   = $v['sr_id'];
                $category[$v['f_category']]['service'][$key]['name'] = $v['sr_name'];
                $category[$v['f_category']]['service'][$key]['type'] = 2;
            }
        }
        Tree::getInstance()->init($category);
        $result = Tree::getInstance()->getTreeArray(0);

        return $result;
    }

    public function add($info)
    {
        $add = $info;
        unset($add['token']);
        $add['addtime'] = time();
        $add['biz_id']  = 1;
        //$add['id_add_op'] = ADMIN;
        $result = FModel::instance('ServiceProduct')->addInfo($add);
        if (!$result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }
        $add['sr_id'] = $result;

        return $add;
    }

    public function edits($info)
    {
        self::$model     = FModel::instance('ServiceProduct');
        $where['sr_id']  = intval($info['sr_id']);
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
        self::$model     = FModel::instance('ServiceProduct');
        $where['sr_id']  = intval($info['sr_id']);
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