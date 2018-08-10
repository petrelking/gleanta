<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;
use app\weidoo\helper\Tree;

class ServicePackage extends Base
{

    /**
     * 服务套餐,type=3
     */

    public static $model;

    public function lists($info)
    {
        $where['biz_id'] = 1;
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
        $data   = FModel::instance('ServicePackage')->getPackage($exp, $where, $search);
        $result = [];
        foreach ($data as $key => $v) {
            $result[$key]             = $v;
            $result[$key]['contents'] = unserialize($v['contents']);
            $result[$key]['addtime']  = date_for($v['addtime']);
        }

        return $result;
    }

    /**
     * 预约套餐列表
     */
    public function cartPackage($info)
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
        $data   = FModel::instance('ServicePackage')->getPackage($exp, $where, $search);
        $result = [];
        foreach ($data as $key => $v) {
            $result[$key]['type']  = 3;
            $result[$key]['id']    = $v['sp_id'];
            $result[$key]['name']  = $v['sp_name'];
            $result[$key]['price'] = $v['price'];
        }

        return $result;
    }

    /**
     * 套餐分类及其数据列表
     */
    public function categoryPackage($info)
    {
        $where['type']   = 3;
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
        $data = FModel::instance('ServicePackage')->getPackage($exp, $where, $search);

        foreach ($data as $key => $v) {
            if (isset($category[$v['s_category']])) {
                $category[$v['s_category']]['service'][$key]['id']   = $v['sp_id'];
                $category[$v['s_category']]['service'][$key]['name'] = $v['sp_name'];
                $category[$v['s_category']]['service'][$key]['type'] = 3;
                continue;
            }
            if (isset($category[$v['f_category']])) {
                $category[$v['f_category']]['service'][$key]['id']   = $v['sp_id'];
                $category[$v['f_category']]['service'][$key]['name'] = $v['sp_name'];
                $category[$v['f_category']]['service'][$key]['type'] = 3;
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
        //处理套餐内容数据后,serialize保存,前端给数据后增加判断所选的次数/张数
        /**
         * 内容格式
         * ['format_type' => 'package_content','data' => [
         *      ['0'] = ['type' => 1, 'id' => 10, 'count' => 2,],
         *      ['1'] = ['type' => 2, 'id' => 6, 'count' => 3,],
         *  ],
         * ]
         */
        $add['contents'] = serialize($info['contents']);
        $add['biz_id']   = 1;
        //$add['id_add_op'] = ADMIN;
        $result = FModel::instance('ServicePackage')->addInfo($add);
        if (!$result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }
        $add['sp_id'] = $result;

        return $add;
    }

    public function edits($info)
    {
        self::$model     = FModel::instance('ServicePackage');
        $where['sp_id']  = intval($info['sp_id']);
        $where['biz_id'] = 1;
        $edata           = self::$model->get($where);
        if (!$edata) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }

        $update = $info;
        unset($update['token']);
        $update['contents'] = serialize($info['contents']);
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
        self::$model     = FModel::instance('ServicePackage');
        $where['sp_id']  = intval($info['sp_id']);
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