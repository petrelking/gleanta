<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2018/7/26
 * Time: 18:56
 */

namespace app\payment\logic;


use app\common\model\logic\Base;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Index extends Base
{
    public static $model;
    public function index()
    {
       echo 1;
    }
    public function addPayment($info)
    {
        self::$model = FModel::instance('Index','payment',false,'model');
        $result = self::$model->addPayment($info);
        return $result;
    }

    public function editPayment($info)
    {
        self::$model = FModel::instance('Index','payment',false,'model');
        $result = self::$model->editPayment($info);
        return $result;
    }

    public function delPayment($info)
    {
        self::$model = FModel::instance('Index','payment',false,'model');
        $result = self::$model->delPayment($info);
        return $result;
    }

    public function getPayment()
    {
        self::$model = FModel::instance('Index','payment',false,'model');
        $result = self::$model->getPayment();
        return $result;
    }

    public function getPaymentsListl($biz_id)
    {
        self::$model = FModel::instance('Index','payment',false,'model');
        $result = self::$model->getPaymentsListm($biz_id);
        return $result;
    }

    public function chkBizPayType()
    {
        $biz_id = 1; // $this->user['biz_id'];
        self::$model = FModel::instance('Index','payment',false,'model');
        $result = self::$model->chkBizPayType($biz_id,1);
        return $result;
    }

    public function payByType($info,$extinfo)
    {
        $biz_id = 1; // $this->user['biz_id'];
        self::$model = FModel::instance('Index','payment',false);
        $ext_info =[];
        $orderinfo = self::$model->getOrderInfo(intval($info['order_id']));
        if(!$orderinfo) {
            EModel::setError(lang('order_not_exist'));
            return false;
        }
        if($orderinfo['rest_amount']<$info['pay_amount']){
            EModel::setError(lang('pay_amount_not_match'));
            return false;
        }
        $type = $info['pay_type'];
        if(!in_array($type,array(1,2,3,4))){
            Emodel::setError(lang('pay_type_undefined'));
            return false;
        }
        if(self::$model->chkBizPayType($biz_id,$info['pay_id'])){
            Emodel::setError(lang('pay_type_auth_fail'));
            return false;
        }
        if($type==-2) {
            $table_extra = 'by_payments_coupon_usages';
            $ext_info = [
                'coupon_id'    => $extinfo["coupon_id"],
                'pay_amount'   => $extinfo['pay_amount'],
                'dept_id'      => $extinfo['dept_id'],
                'user_id'      => $this->user['user_id'],
                'biz_id'       => $this->user['biz_id'],
            ];
        }
        if($type==3) {
            $table_extra = 'by_payments_online';
            $ext_info = [
                'order_no'      => $extinfo['order_no'],
                'out_trade_no'  => $extinfo['out_trade_no'],
                'total_amount'  => $extinfo['total_amount'],
                'total_fee'     => $extinfo['total_fee'],
            ];
        }
        if($type==4) {
            $table_extra = 'by_payments_groupons';
            $ext_info = ['gpon_cardno'    => $extinfo['gpon_cardno'],];
        }
        $rs = self::$model->payByType($info,$ext_info,$table_extra);
        if($rs>0){
            $this->result($rs,'200','操作成功');
        }else{
            $this->result($rs,'100','操作失败');
        }
    }

    public function getPayHistory($order_id)
    {
        self::$model = FModel::instance('Index','payment',false);
        if($order_id>0){
            $rs = self::$model->getPayHistory($order_id);
            if(count($rs)>0){
                return $rs ;
            }
        }
        EModel::setError('order doesnot exist ');
        return false;
    }
    public function setBizPayType($info)
    {
        self::$model = FModel::instance('Index','payment',false);
        $rs = self::$model->setBizPayType($info);
        if($rs) {
            return $rs;
        }else if($rs == 1006){
            Emodel::setError(lang('date error'));
            return false;
        }else{
            Emodel::setError(lang('operation failed'));
            return false;
        }
    }
}