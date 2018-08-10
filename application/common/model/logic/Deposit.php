<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Deposit extends Base
{

    public static $model;

    public function add($info,$login){
        $where = ['biz_id'=>$login['biz_id'],'cart_id'=>$info['cart_id']];
        $cart  = FModel::instance('Cart')->getOneInfo($where);
        if(empty($cart)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        } elseif($cart['status'] == 4){
            EModel::instance()->setError(lang('data status'));
            return false;
        }
        $insert = [
            'biz_id'  => $login['biz_id'],
            'cart_id' => $info['cart_id'],
            'cus_id'  => $cart['cus_id'],
            'user_id' => $login['user_id'],
            'dep_amount'  => round($info['dep_amount'],2),
            'pay_type' => intval($info['pay_type']),
            'dep_meno' => !empty($info['dep_meno']) ? $info['dep_meno'] : '',
            'addtime'  => time(),
        ];
        $result = FModel::instance('Deposit')->addInfo($insert);
        if(empty($result)){
            EModel::instance()->setError(lang('execute error'));
            return false;
        } else {
            return true;
        }
    }
}