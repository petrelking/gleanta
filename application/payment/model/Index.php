<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2018/7/26
 * Time: 17:28
 */

namespace app\payment\model;

use app\common\model\model\Base;
use think\Db;

class Index extends Base
{
    protected $table        = 'by_payments_config_biz';
    protected $table_main   = 'by_payments_config_setting';
    protected $table_pay    = 'by_crm_order_pay';
    protected $table_order  = 'by_crm_order_main';

    public function addPayment($info)
    {
        return $this->table($this->table_main)->insert($info);
    }

    public function editPayment($info)
    {
        $where = ['pay_id',$info['pay_id']];
        return $this->table($this->table_main)->where($where)->update($info);
    }

    public function delPayment($info)
    {
        $where = ['pay_id',$info['pay_id']];
        return $this->table($this->table_main)->where($where)->update(['status',99]);
    }

    public function getPayment()
    {
        return $this->table($this->table_main)->select();
    }
    public function getPaymentsListm($biz_id)
    {
        $where = ['biz_id'=>$biz_id,'status'=>1];
       /* $result = $this->alias('s')
            ->join('by_payments_config_setting b ',' s.pay_id = b.pay_id')
            ->where($where)
            ->field('b.pay_id,b.pay_name,b.pay_type,b.pay_fee')
            ->select()->toArray();*/
        $result = $this->table($this->table)->where($where)->find();
        if($result){
            $where = array('pay_id'=>explode(',',$result['pay_ids']));
            $list = $this->table($this->table_main)->where($where)->select();
            if($list){
                return $list;
            }
        }
        return [];
        $this->table($this->table_main)->select();
    }
    public function getOrderInfo($order_id)
    {
        return  $this->table($this->table_order)->where(['main_id'=>$order_id])->find()?:[];
    }
    public function isPayTypeExist($info)
    {
        $winfo['pay_type'] = $info['pay_type'];
        $winfo['pay_id']  = $info['pay_id'];
        $isExist = $this->table($this->table_pay)->where($winfo)->find();
        if($isExist){
            return $isExist;
        }else{
            return false;
        }
    }
    public function payByType($info,$extinfo,$table_extra)
    {
        if($this->isPayTypeExist($info))
        {
            $rs = $this->table($this->table_pay)->update($info);
            if($rs){
                return $rs;
            }
        }else {
            $result = $this->table($this->table_pay)->addInfo($info);
            if ($result) {
                $extinfo['p_id'] = $result;
                if (isset($table_extra)) {
                    $result = $this->table($table_extra)->addInfo($extinfo);
                }
                return $result;
            }
        }
        return false;
    }

    public function getPayHistory($order_id)
    {
        return $this->table($this->table_pay)->where(['order_id'=>$order_id])->select()?:[];
    }

    public function setBizPayType($info)
    {
        //$info['biz_id'] =$this->user['biz_id'];
        $where[] = ['biz_id','eq',$info['biz_id']];
        $info['addtime'] = time();
        $where_q[] = ['pay_id','in',explode(',',$info['pay_ids'])];
        $checker = $this->table($this->table_main)->where($where_q)->select();
        if(count($checker)!=count(explode(',',$info['pay_ids']))){
            return 1006;
        }
        $isexist = $this->where($where)->find();
        if($isexist){
            $isexist = $isexist->toArray();
        }
        if(count($isexist)>0){
            $info['status'] = isset($info['status'])?$info['status']:1;
            $sinfo = array('pay_ids'=>$info['pay_ids'],'status'=>$info['status']);
            $fs =  $this->table($this->table)->where($where)->update($sinfo);
        }else{
            $fs = $this->table($this->table)->insert($info);
        }
        return $fs ;
    }

    public function chkBizPayType($biz_id,$pay_id)
    {
        //$where[] = ['','exp',Db::raw("FIND_IN_SET($pay_id,pay_ids)")];
        $where[] = ['pay_ids','gt',' 3  '];
        $where[] = ['pay_ids','lt',' 3  '];
        $this->getPk();
        //$map['biz_id'] = 1;//$biz_id; ->where($map)
         $this->table($this->table)->where($where)->find();
         echo $this->getLastSql();
    }
}