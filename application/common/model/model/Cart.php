<?php
namespace app\common\model\model;

class Cart extends Base {

    protected $table = "by_crm_cart_main";

    public function getCartList($where,$field,$order){
        $order = trim($order);
        $data  = $this->field($field)
            ->where($where)
            ->order($order)
            ->paginate(input('post.pagesize/d'))->toArray();
        return $data;
    }

    public function addCartData($main,$list,$isedit=0){
        $this->startTrans();
        try{
            $cart_id = $this->insertGetId($main);
            foreach ($list as $key=>$val){
                $val['cart_id'] = $cart_id;
                $list[$key] = $val;
            }
            if($isedit == 1){
                $this->where(['cart_id'=> $cart_id])->delete();
            }
            $this->table("by_crm_cart_list")->insertAll($list);
            $this->commit();
        }catch (\Exception $e) {
            $this->rollback();
            return false;
        }
        return true;
    }

}