<?php

namespace app\common\model\model;

class ServicePackage extends Base
{

    protected $table = 'by_crm_service_package';

    public function getPackage($exp = '', $where = [], $search = '')
    {
        $field = '*';//
        if ($search) {
            return $result = $this->field($field)->where($exp)->where('sp_name', 'like', '%' . $search . '%')->where($where)->select();
        }
        return $result = $this->field($field)->where($exp)->where($where)->select();
    }

}