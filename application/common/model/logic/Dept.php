<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Dept extends Base
{

    public static $model;

    public static function getDeptList($info,$login)
    {
        $where['biz_id'] = $login['biz_id'];
        $where['status'] = 1;
        $field = "dept_id,parent_id,dept_name,dept_letter,status";
        $list = FModel::instance('Dept')->getDeptList($where,$field);
        if(empty($list)){
            return [];
        }
        foreach ($list as $key=>$val){
            if($val['parent_id'] == 0){
                $parent[$val['dept_id']]['info'] = $val;
            }
        }
        foreach ($list as $key=>$val){
            if($val['parent_id']>0){
                $parent[$val['parent_id']]['sub'][] = $val;
            }
        }
        foreach ($parent as $key=>$val){
            if(!isset($val['sub'])){
                $parent[$key]['sub'] = [];
            }
        }
        return $parent;
    }

    public static function addDeptData($info,$login)
    {
        self::$model = FModel::instance('Dept');
        if(!empty($info['parent_id'])){
            $parent = self::$model->getOneInfo(['dept_id'=>$info['parent_id'],"biz_id"=>$login['biz_id']]);
            if(empty($parent) || $parent['status'] != 1){
                EModel::instance()->setError(lang('query not-exist'));
                return false;
            }
        }
        $where  = [
            'biz_id'    => $login['biz_id'],
            'dept_name' => $info['dept_name'],
            'status'    =>[1,2],
        ];
        $checkName = self::$model->getValue($where,"dept_id");
        if(!empty($checkName)){
            EModel::instance()->setError(lang('deptname exits'));
            return false;
        }
        $info['biz_id'] = $login['biz_id'];
        $insert = self::expDeptData($info);
        $result = self::$model->addInfo($insert);
        return $result;
    }

    public static function editDeptData($info,$login)
    {
        self::$model = FModel::instance('Dept');
        $where = [
            'dept_id'=> $info['dept_id'],'biz_id'=>$login['biz_id'],'status'=>[1,2],
        ];
        $dept = self::$model->getOneInfo($where,"dept_id,parent_id");
        if(empty($dept)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        if(!empty($info['parent_id'])){
            $parent = self::$model->getOneInfo(['dept_id'=>$info['parent_id'],"biz_id"=>$login['biz_id']]);
            if(empty($parent) || $parent['status'] != 1){
                EModel::instance()->setError(lang('query not-exist'));
                return false;
            }
        }
        $where  = [
            'biz_id'    => $login['biz_id'],
            'dept_name' => $info['dept_name'],
            'status'    =>[1,2],
        ];
        $checkName = self::$model->getValue($where,"dept_id");
        if(!empty($checkName) && $checkName['dept_id']!= $info['dept_id']){
            EModel::instance()->setError(lang('deptname exits'));
            return false;
        }
        $info['biz_id'] = $login['biz_id'];
        $insert = self::expDeptData($info);
        $result = self::$model->updateInfo(['dept_id'=>$info['dept_id']],$insert);
        return $result;
    }

    public static function oneDept($info,$login)
    {
        self::$model = FModel::instance('Dept');
        $where = [
            'dept_id'=> $info['dept_id'],'biz_id'=>$login['biz_id'],'status'=>[1,2],
        ];
        $dept = self::$model->getOneInfo($where);
        if(empty($dept)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        return $dept;
    }



    private static function expDeptData($info)
    {
        $insert = [
            'parent_id'   => !empty($info['parent_id']) ? intval($info['parent_id']) : 0,
            'biz_id'      => $info['biz_id'],
            'dept_name'   => $info['dept_name'],
            'dept_letter' => getFirstCharter($info['dept_name']),
            'manage_id'   => !empty($info['manage_id']) ? intval($info['manage_id']) : 0,
            'manage_mobile' => !empty($info['manage_mobile']) ? $info['manage_mobile'] : '',
            'remarks'     => !empty($info['remarks']) ? $info['remarks'] : '',
            'is_store'    => isset($info['is_store']) && $info['is_store'] == 1 ?1 : 0,
        ];
        return $insert;
    }



}