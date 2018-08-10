<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Customer extends Base
{

    public function add($info,$login)
    {
        $checkMobile = FModel::instance('Customer')->getOneInfo(
            ['biz_id'=>$login['biz_id'],"cus_mobile"=>$info['mobile']]
        );
        if(!empty($checkMobile)){
            EModel::instance()->setError(lang('query exist'));
            return false;
        }
        if(!empty($info['is_cart']) && $info['is_cart'] == 1 && !validate("Cart")->scene("tbadd")->check($info)){
            EModel::instance()->setError(validate("Cart")->getError());
            return false;
        }
        $insert = [
            'biz_id'     => $login['biz_id'],
            'user_id'    => $login['user_id'],
            'dept_id'    => intval($info['add_dept_id']),
            'channel_id' => intval($info['channel_id']),
            'cus_name'   => $info['cus_name'],
            'cus_mobile' => $info['mobile'],
            'cus_sex'    => !empty($info['cus_sex']) && in_array($info['cus_sex'],[1,2,3]) ? $info['cus_sex'] : 3,
            'cus_grade'  => !empty($info['cus_grade']) ? intval($info['cus_grade']) : 1,
            'cus_wx'     => !empty($info['cus_wx']) ? $info['cus_wx'] : '',
            'cus_qq'     => !empty($info['cus_qq']) ? $info['cus_qq'] : '',
            'cus_email'  => !empty($info['cus_email']) ? $info['cus_email'] : '',
            'cus_area'   => !empty($info['cus_area']) ? $info['cus_area'] : '',
            'cus_addr'   => !empty($info['cus_addr']) ? $info['cus_addr'] : '',
            'remarks'    => !empty($info['add_remarks']) ? $info['add_remarks'] : '',
            'addtime'    => time(),
        ];
        $cus_id = FModel::instance('Customer')->addCustomer($insert);
        if(!$cus_id){
            EModel::instance()->setError(lang('execute error'));
            return false;
        }
        if(!empty($info['is_cart']) && $info['is_cart'] == 1){
            $info['cus_id'] = $cus_id;
            $result = FModel::instance("Cart","common","logic")->add($info,$login);
        }
        return true;
    }
}