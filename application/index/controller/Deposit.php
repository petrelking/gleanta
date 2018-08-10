<?php
/**
 * 预约金
 */
namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Deposit extends Base
{

    /**
     * 添加预约金 - 定金
     */
    public function addData()
    {
        $success = FModel::instance('Deposit')->add(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    public static function getValidate($action)
    {
        switch ($action){
            case 'adddata' :
                return [
                    'pay_type'   => input("post.pay_type/d"),
                    'dep_amount' => input("post.dep_amount/d"),
                    'cart_id'    => input("post.cart_id/d"),
                ];
            default:
                return [''];
                break;
        }
    }
}