<?php
namespace app\common\model\model;

class User extends Base {

    protected $table = "by_crm_user";

    public function getUserByMobile($where){
        $result = $this->alias("u")
            ->join("by_crm_business b ","u.biz_id=b.biz_id")
            ->where($where)
            ->field("u.user_id,u.biz_id,u.mobile,b.biz_name,b.biz_logo")
            ->select()->toArray();
        return $result;
    }

    public function getUserBase($where){
        $user = $this->alias("u")
            ->join("by_crm_business b ","u.biz_id=b.biz_id")
            ->where($where)
            ->field("u.user_id,u.dept_id,u.user_pass,u.biz_id,u.mobile,u.user_name,u.multi_login,b.biz_name,b.biz_logo")
            ->find();
        return $user ? $user->toArray() : [];
    }

    /**
     * 获取商户下的用户列表
     */
    public function getBisUser($where,$field='*',$expwhere='',$ordername=''){
        $ordername = trim($ordername);
        $data  = $this->alias('u')->join("by_crm_user_info i","u.user_id=i.user_id","left")
            ->field($field)
            ->where($where)
            ->where($expwhere)
            ->order($ordername)
            ->paginate(input('post.pagesize/d'))->toArray();
        return $data;
    }

    /**
     *  添加用户
     */
    public function addUser($user,$userinfo){
        $this->startTrans();
        try{
            $userinfo['user_id'] = $this->isUpdate(false)->save($user);
            UserInfo::create($userinfo);
            $this->commit();
            return true;
        }catch (\Exception $e) {
            $this->rollback();
            return false;
        }
    }

    public function userByIds($ids,$field){
        if(empty($ids)) return [];
        $field = $field.',user_id';
        $where['user_id'] = ["in",$ids];
        $where['status']  = [1,2];
        $list = $this->where($where)->field($field)->select();
        $data = [];
        if(!empty($list)){
            foreach ($list as $key=>$val){
                $res = $val->toArray();
                $data[$res['user_id']] = $res;
            }
        }
        return $data;
    }

}