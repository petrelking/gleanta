<?php

namespace app\common\model\model;

class ServiceProduct extends Base
{

    protected $table = 'by_crm_service_product';

    public function getProduct($exp = '', $where = [], $search = '')
    {
        $field = '*';//sr_id,sr_name,dept_id,addtime
        if ($search) {
            return $result = $this->field($field)->where($exp)->where('sr_name', 'like', '%' . $search . '%')->where($where)->select();
        }
        return $result = $this->field($field)->where($exp)->where($where)->select();
    }

}