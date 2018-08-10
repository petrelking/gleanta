<?php

namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Business extends Base
{

    /**
     * 商户登录通过手机查找所有的商户列表
     * 参数 mobile
     */
    public function searchMobile()
    {
        $success = FModel::instance('Business')->searchBusiness(self::$validateData);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 保存登录
    */
    public function saveLogin()
    {
        $success = FModel::instance('Business')->businessLogin(self::$validateData);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
    * 商户修改登录密码
    * old_pwd   string  旧密码
    * user_pass string  新密码
    * token 登录TOKEN
    */
    public function updatePwd()
    {
        //$this->user = ['user_id'=>1];
        $success = FModel::instance('Business')->updateBusinessPwd(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 退出登录
     */
    public function outLogin()
    {
        $token = input("token");
        app('cache_wd_redis')->set('token_'.$token,[]);
        app('cache_wd_redis')->expire('token_'.$token,2);
        return out_success_json(lang('execute success'), []);
    }

    /**
     * 员工列表
     */
    public function userList()
    {
        //$this->user = ['user_id'=>1,'biz_id'=>1];
        $success    = FModel::instance('Business')->getUserListByBiz(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 添加新用户
     */
    public function addUser()
    {
        //$this->user = ['user_id'=>1,'biz_id'=>1,'user_name'=>'狂奔的破车'];
        $success    = FModel::instance('Business')->addUser(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 修改员工状态
     */
    public function upUserStatus()
    {
        $this->user = ['user_id'=>1,'biz_id'=>1,'user_name'=>'狂奔的破车'];
        $success    = FModel::instance('Business')->upStatus(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 验证参数配置
     */
    public static function getValidate($action){
        switch ($action){
            case 'searchmobile':
                return [ 'mobile' => input("post.mobile"),];
            case 'savelogin':
                return [
                    'user_id'   => input("post.user_id/d"),
                    'user_pass' => input("post.user_pass/s")
                ];
            case 'updatepwd':
                return [
                    'old_pwd'   => input("post.old_pwd/s"),
                    'user_pass' => input("post.user_pass/s"),
                ];
            case 'adduser':
                return [
                    'mobile'     => input("post.mobile/s"),
                    'user_pass'  => input("post.user_pass/s"),
                    'dept_id'    => input("post.dept_id/s"),
                    'user_name'  => input("post.user_name/s"),
                    'user_alias' => input("post.user_alias/s"),
                    'user_joint' => input("post.user_joint/s"),
                    'user_sn'    => input("post.user_sn/s"),
                    'user_wx'    => input("post.user_wx/s"),
                    'user_idcard'=> input("post.user_idcard/s"),
                    'user_photo' => input("post.user_photo/s"),
                    'user_desc'  => input("post.user_desc/s"),
                    'user_addr'  => input("post.user_addr/s"),
                    'user_emg'   => input("post.user_emg/s"),
                    'user_emg_mobile'  => input("post.user_emg_mobile/s"),
                ];
            case 'upuserstatus':
                return [
                    'user_id'  => input("post.user_id/d"),
                    'status'   => input("post.status/d"),
                ];
                break;
            default :
                return [''];
        }
    }
}