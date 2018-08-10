<?php
/**
 * 门店相关
 */
namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Dept extends Base
{
    /**
     * 获取部门列表
     */
    public function lists()
    {
        $success = FModel::instance('Dept')->getDeptList(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 添加部门门店信息
     */
    public function addData()
    {
        $success = FModel::instance('Dept')->addDeptData(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 编辑门店
     */
    public function editData()
    {
        $success = FModel::instance('Dept')->editDeptData(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 获取一条部门信息
     */
    public function one()
    {
        $success = FModel::instance('Dept')->oneDept(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 验证参数配置
     */
    public static function getValidate($action){
        switch ($action){
            case 'lists':
                return [''];
            case 'adddata':
                return [
                    'parent_id'    => input("post.parent_id/d"),
                    'dept_name'    => input("post.dept_name/s"),
                    'manage_id'    => input("post.manage_id/d"),
                    'manage_mobile'=> input("post.manage_mobile/s"),
                    'remarks'      => input("post.remarks/s"),
                    'is_store'     => input("post.is_store/s"),
                ];
            case 'editdata':
                return [
                    'dept_id'      => input("post.dept_id/d"),
                    'parent_id'    => input("post.parent_id/d"),
                    'dept_name'    => input("post.dept_name/s"),
                    'manage_id'    => input("post.manage_id/d"),
                    'manage_mobile'=> input("post.manage_mobile/s"),
                    'remarks'      => input("post.remarks/s"),
                    'is_store'     => input("post.is_store/s"),
                ];
                break;
            case 'one':
                return [
                    'dept_id'   => input('dept_id/d'),
                ];
                break;
            default :
                return [''];
        }
    }
}