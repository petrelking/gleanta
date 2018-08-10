<?php

namespace app\common\model\model;

class ServiceItem extends Base
{

    protected $table = 'by_crm_service_item';

    public function getItem($exp = '', $where = [], $search = '')
    {
        $field = '*';//
        if ($search) {
            return $result = $this->field($field)->where($exp)->where('si_name', 'like', '%' . $search . '%')->where($where)->select();
        }
        return $result = $this->field($field)->where($exp)->where($where)->select();
    }

}