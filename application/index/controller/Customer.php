<?php
/**
 * 预约金
 */
namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Customer extends Base
{

    /**
     * 验证手机号码
     */
    public function checkMobile()
    {
        $info   = input("get.");
        $info['biz_id'] = $this->login_userinfo['biz_id'];
        $result = $this->logic->checkCustomerMobile($info);
        if (!$result) {
            $this->resultErr($this->logic->getError(), -2000);
        }
        $this->resultSuc([]);
    }

    /**
     * 新增客户 （及同步预约）
     */
    public function addData()
    {
        $success = FModel::instance('Customer')->add(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 验证参数配置
     */
    public static function getValidate($action){
        switch ($action){
            case 'adddata':
                return [
                    'add_dept_id'    => input("post.add_dept_id/d"),
                    'channel_id'     => input("post.channel_id/d"),
                    'cus_name'       => input("post.cus_name/s"),
                    'mobile'         => input("post.cus_mobile/s"),
                    'cus_sex'        => input("post.cus_sex/d"),
                    'cus_grade'      => input("post.cus_grade/d"),
                    'cus_wx'         => input("post.cus_wx/s"),
                    'cus_qq'         => input("post.cus_qq/s"),
                    'cus_email'      => input("post.cus_email/s"),
                    'cus_area'       => input("post.cus_area/s"),
                    'cus_addr'       => input("post.cus_addr/s"),
                    'add_remarks'    => input("post.add_remarks/s"),
                    'is_cart'        => input("post.is_cart/d"),

                    'btime'          => input("post.btime/s"),
                    'etime'          => input("post.etime/d"),
                    'dept_id'        => input("post.dept_id/s"),
                    'room_id'        => input("post.room_id/s"),
                    'proj_data'      => input("post.proj_data/s"),
                ];
            default :
                return [''];
        }
    }

}