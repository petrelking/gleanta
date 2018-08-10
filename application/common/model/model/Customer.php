<?php
namespace app\common\model\model;

class Customer extends Base {

    protected $table = "by_common_customer";

    /**
     * 根据客户IDs 获取客户信息
     */
    public function getInfoById($cus_id){
        $data = $this->where("cus_id",$cus_id)->cache()->find();
        if(!empty($data)){
            return $data->toArray();
        } else {
            return  [];
        }
    }

    /**
     * 根据客户IDs  获取列表
     */
    public function getCustomerByIds($cus_ids,$field='*'){
        if(empty($cus_ids)) return [];
        $field = $field.',cus_id';
        $where['cus_id'] = ["in",$cus_ids];
        $where['status'] = [1,2];
        $list = $this->where($where)->field($field)->select();
        $data = [];
        if(!empty($list)){
            foreach ($list as $key=>$val){
                $res = $val->toArray();
                $data[$res['cus_id']] = $res;
            }
        }
        return $data;
    }

    public function addCustomer($base){
        $this->startTrans();
        try{
            $cus_id = $this->insertGetId($base);
            $record = [
                'cus_id' => $cus_id,
                'biz_id' => $base['biz_id'],
                'uptime' => time(),
            ];
            $this->table("by_common_customer_account")->insert($record);
            $this->commit();
            return $cus_id;
        }catch (\Exception $e) {
            $this->rollback();
            return false;
        }

    }
}