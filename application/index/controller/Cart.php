<?php
/**
 * 购物车
 */
namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Cart extends Base
{

    /**
     * 预约列表
     */
    public function lists()
    {
        $success = FModel::instance('Cart')->getCartList(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    //取消预约
    public function delData(){
        $success = FModel::instance('Cart')->del(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 新增预约
     */
    public function addData(){
        $success = FModel::instance('Cart')->add(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 保存修改预约
     */
    public function editData(){
        $success = FModel::instance('Cart')->editCart(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 预约跟进
     */
    public function addFollow(){
        $success = FModel::instance('Cart')->cartFollow(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 获取购物车详情
     */
    public function getDetail(){
        $success = FModel::instance('Cart')->detail(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }


    /**
     * 验证参数配置
     */
    public static function getValidate($action){
        switch ($action){
            case 'lists':
                return [''];
            case 'addfollow':
                return [
                    'cart_id'        => input("post.cart_id/d"),
                    'cus_id'         => input("post.cus_id/d"),
                    'follow_type'    => input("post.follow_type/d"),
                    'follow_status'  => input("post.follow_status/d"),
                    'follow_time'    => input("post.follow_time/s"),
                    'follow_next'    => input("post.follow_next/s"),
                    'content'        => input("post.content/s"),
                ];
            case 'adddata':
                return [
                    'cus_id'       => input("post.cus_id/d"),
                    'btime'        => input("post.btime/s"),
                    'etime'        => input("post.etime/d"),
                    'dept_id'      => input("post.dept_id/s"),
                    'room_id'      => input("post.room_id/s"),
                    'proj_data'    => input("post.proj_data/s"),
                ];
            case 'editdata':
                return [
                    'cart_id'      => input("post.cart_id/d"),
                    'cus_id'       => input("post.cus_id/d"),
                    'btime'        => input("post.btime/s"),
                    'etime'        => input("post.etime/d"),
                    'dept_id'      => input("post.dept_id/s"),
                    'room_id'      => input("post.room_id/s"),
                    'proj_data'    => input("post.proj_data/s"),
                ];
                break;
            case 'deldata':
                return [
                    'cart_id'   => input('cart_id/d'),
                ];
                break;
            case 'getdetail':
                return [
                    'cart_id'   => input('cart_id/d'),
                ];
                break;
            default :
                return [''];
        }
    }
}