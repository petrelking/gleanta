<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;
use app\weidoo\helper\Tree;

class ServiceItem extends Base
{

    /**
     * 服务项目,type=1
     */

    public static $model;

    public function lists($info)
    {
        $where['biz_id'] = 1;
        if (!empty($info['f_category'])) {
            $where['f_category'] = $info['f_category'];
        }
        //不传为全部状态,所以令停售为2
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
        $data   = FModel::instance('ServiceItem')->getItem($exp, $where, $search);
        $result = [];
        foreach ($data as $key => $v) {
            $result[$key]            = $v;
            $result[$key]['addtime'] = date_for($v['addtime']);
        }
        return $result;
    }

    /**
     * 预约项目列表
     */
    public function cartItem($info)
    {
        $where['biz_id'] = 1;
        $where['status'] = 1;
        if (!empty($info['f_category'])) {
            $where['f_category'] = $info['f_category'];
        }
        if (!empty($info['s_category'])) {
            $where['s_category'] = $info['s_category'];
        }
        $exp    = 'FIND_IN_SET(' . intval($info['dept_id']) . ',dept_id)';
        $search = '';
        if (!empty($info['search'])) {
            $search = $info['search'];
        }
        $data   = FModel::instance('ServiceItem')->getItem($exp, $where, $search);
        $result = [];
        foreach ($data as $key => $v) {
            $result[$key]['type']  = 1;
            $result[$key]['id']    = $v['si_id'];
            $result[$key]['name']  = $v['si_name'];
            $result[$key]['price'] = $v['price'];
        }

        return $result;
    }

    /**
     * 项目分类及其数据列表
     */
    public function categoryItem($info)
    {
        $where['type']   = 1;
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
        $data = FModel::instance('ServiceItem')->getItem($exp, $where, $search);

        foreach ($data as $key => $v) {
            if (isset($category[$v['s_category']])) {
                $category[$v['s_category']]['service'][$key]['id']   = $v['si_id'];
                $category[$v['s_category']]['service'][$key]['name'] = $v['si_name'];
                $category[$v['s_category']]['service'][$key]['type'] = 1;
                continue;
            }
            if (isset($category[$v['f_category']])) {
                $category[$v['f_category']]['service'][$key]['id']   = $v['si_id'];
                $category[$v['f_category']]['service'][$key]['name'] = $v['si_name'];
                $category[$v['f_category']]['service'][$key]['type'] = 1;
            }
        }
        Tree::getInstance()->init($category);
        $result = Tree::getInstance()->getTreeArray(0);

        return $result;
    }

    public function add($info)
    {
        //统一售价及各门店售价处理,个数及字段不确定,validate无法验证,在这里验证,前端以price+门店id传值
        $arr_dept = explode(',', $info['dept_id']);
        foreach ($arr_dept as $v) {
            if (empty($info['price' . $v])) {
                EModel::instance()->setError('存在门店售价未填写,请补齐');
                return false;
            }
            $price[$v] = $info['price' . $v];
            unset($info['price' . $v]);
        }
        $add = $info;
        unset($add['token']);
        $add['biz_id']     = 1;
        $add['addtime']    = time();
        $add['dept_price'] = serialize($price);
        //$add['id_add_op'] = ADMIN;
        $result = FModel::instance('ServiceItem')->addInfo($add);
        if (!$result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }
        $add['si_id'] = $result;

        return $add;
    }

    public function edits($info)
    {
        $info['si_id'] = intval($info['si_id']);
        $arr_dept      = explode(',', $info['dept_id']);
        foreach ($arr_dept as $v) {
            if (empty($info['price' . $v])) {
                EModel::instance()->setError('存在门店售价未填写,请补齐');
                return false;
            }
            $price[$v] = $info['price' . $v];
            unset($info['price' . $v]);
        }
        $update = $info;
        unset($update['token']);
        $update['dept_price'] = serialize($price);
        //$update['id_op'] = ADMIN;
        self::$model     = FModel::instance('ServiceItem');
        $where['si_id']  = $info['si_id'];
        $where['biz_id'] = 1;
        $edata           = self::$model->get($where);
        if (!$edata) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $result = self::$model->editInfo($update, $where);
        if (false === $result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }

        return $update;
    }

    public function del($info)
    {
        self::$model     = FModel::instance('ServiceItem');
        $where['si_id']  = intval($info['si_id']);
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