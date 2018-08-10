<?php
namespace app\common\model\model;

class Dept extends Base {

    protected $table = "by_crm_dept";

    public function getDeptList($where,$field)
    {
        $list = $this->where($where)->field($field)->select()->toArray();
        return $list;
    }

}