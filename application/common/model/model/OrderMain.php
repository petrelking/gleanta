<?php

namespace app\common\model\model;

class OrderMain extends Base
{

    protected $table = "by_crm_order_main";

    public function addOrder($main,$list,$give=[]){
        $this->startTrans();
        try{
            $main_id  = $this->insertGetId($main);
            if(empty($main_id)) {
                return false;
            }
            foreach ($list as $key=>$val){
                $use_card = $val['use_card'];
                unset($val['use_card']);
                $list_id = $this->table("by_crm_order_list")->insertGetId($val);
                if(!empty($use_card)){ //插入卡类型使用记录
                    $card = [];
                    foreach ($use_card as $k=>$v){
                        $card[] = [
                            'order_no' => $main['order_no'],
                            'biz_id'   => $main['biz_id'],
                            'cus_id'   => $main['cus_id'],
                            'card_id'  => $v['id'],
                            'card_type'=> $v['card_type'],
                            'list_id'  => $list_id,
                            'num'      => $v['num'],
                            'addtime'  => time(),
                        ];
                    }
                    $this->table("by_crm_order_card")->insertAll($card);
                }
            }
            //其他业务待定
            $this->commit();
            return $main_id;
        }catch (\Exception $e) {
            $this->rollback();
            return false;
        }
    }
}