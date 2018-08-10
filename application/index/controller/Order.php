<?php
/**
 * 门店相关
 */
namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Order extends Base
{

    /**
     * 下订单
     */
    public function saveOrder()
    {
        $success = FModel::instance('Order')->addOrderData(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $success = FModel::instance('Order')->orderDetail(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 我的订单列表
     */
    public function lists()
    {
        $success = FModel::instance('Order')->myOrderList(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 操作记录
     */
    public function optrecord()
    {
        $success = FModel::instance('Order')->optOrderRecord(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 取消订单
     */
    public function cancel(){
        $success = FModel::instance('Order')->cancelOrder(self::$validateData,$this->user);
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }


    /**
     * 数据验证
     * @return array|void
     */
    public static function getValidate($action)
    {
        switch ($action){
            case 'saveorder' :
                return [
                    'cart_id'      => input("post.cart_id/d"), //购物车ID 非必须
                    'cus_id'       => input("post.cus_id/d"),  //客户ID
                    'items'        => input("post.items"),     //项目详情
                    'remarks'      => input("post.remarks/s"), //备注
                    'dept_id'      => input("post.dept_id/d"), //部门
                    'appoint_time' => input("post.appoint_time/s"),//预约时间
                ];
            case 'detail':
                return [
                    'order_id' => input("post.order_id/d"),
                ];
                break;
            case 'cancel':
                return [
                    'order_id' => input("post.order_id/d"),
                ];
                break;
            case 'lists':
                return [
                    'ctype'     => input("post.ctype/d"),    //付款类型
                    'time_type' => input("post.time_type/d"),//时间类型
                    'btime'     => input("post.btime/s"),    //自定义开始时间
                    'etime'     => input("post.etime/s"),    //自定义结束时间
                    'dept_ids'  => input("post.dept_ids/s"), //门店 多个逗号分隔
                    'page'      => input("post.page/d",1),   //页码
                ];
                break;
            case 'optrecord': //操作记录
                return [
                    'order_id'  => input("post.order_id/d")
                ];
                break;
            default:
                return [''];
                break;
        }
    }

}
