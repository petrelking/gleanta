<?php
namespace app\common\model\model;

class CustomerCard extends Base {

    protected $table = "by_common_customer_card";

    public function getCardInfo($card_id,$biz_id,$cus_id){
        $where = ['card_id'=>$card_id,"biz_id"=>$biz_id,"cus_id"=>$cus_id];
        $one = $this->alias("my")->join("by_crm_service_card comm", "my.sc_id=comm.sc_id")
            ->field("my.btime as use_btime,my.exptime as use_exptime,my.face_value,comm.*")
            ->find();
        return !empty($one) ? $one->toArray() : [];
    }
}