<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Cart extends Base
{

    public static $model;

    public static function getCartList($info, $login)
    {
        $where = [
            'biz_id' => $login['biz_id'],
            'status' => [1, 2, 3, 4],
        ];
        !empty($info['cus_type']) && in_array($info['cus_type'], [1, 2, 3]) && $where['cus_type'] = $info['cus_type'];
        !empty($info['status']) && in_array($info['status'], [1, 2, 3, 4, 4]) && $where['status'] = $info['status'];
        if (!empty($info['dept_id'])) {
            $depts = strToArr($info['dept_id']);
            $depts && $where['dept_id'] = ['in', implode(',', $depts)];
        }
        $search_time = self::getSearchTime($info);
        if (!empty($search_time)) {
            $begin = $search_time['begin'];
            $end = $search_time['end'];
            !empty($begin) && empty($end) && ($end = $begin + 86400 - 1);
            empty($begin) && !empty($end) && ($begin = $end - 86400 + 1);
            $where['appoint_time'] = [
                'betwend', "{$begin},{$end}"
            ];
        }
        $field = "cart_id,cus_id,dept_id,room_id,user_num,appoint_time,cus_type,status,end_time,remarks";
        $result = FModel::instance('Cart')->getCartList($where, $field, "cart_id desc");
        if (empty($result['data'])) {
            return $result;
        } else {
            $cus_ids = '';
            $teach_ids = $prod_ids = [];
            foreach ($result['data'] as $key => $val) {
                $cus_ids .= ($cus_ids ? ',' : '') . $val['cus_id'];
                $subwhere = ['cart_id' => $val['cart_id'], "status" => 1];
                $cartlist = FModel::instance('CartList')->getListInfo($subwhere, "proj_id,teacher_id");
                $cartlist = $cartlist->toArray();
                $val['prod_ids'] = array_unique(getArrayColumn($cartlist, "proj_id", 0));
                $val['teach_ids'] = array_unique(getArrayColumn($cartlist, "teacher_id", 0));
                $teach_ids = $teach_ids ? array_merge($teach_ids, $val['teach_ids']) : $val['teach_ids'];
                $prod_ids = $prod_ids ? array_merge($prod_ids, $val['prod_ids']) : $val['prod_ids'];
                $result['data'][$key] = $val;
            }
            $teach_ids = array_unique($teach_ids);
            $prod_ids = array_unique($prod_ids);
            $cuslist = FModel::instance('Customer')->getCustomerByIds($cus_ids, "cus_id,cus_name");
            $teach_list = FModel::instance("User")->userByIds(implode(",", $teach_ids), 'user_id,user_name');
            foreach ($result['data'] as $key => $val) {
                $val['cus_name'] = !empty($cuslist[$val['cus_id']]) ? $cuslist[$val['cus_id']]['cus_name'] : ' - ';
                if (!empty($val['teach_ids'])) {
                    $teach = '';
                    foreach ($val['teach_ids'] as $k => $v) {
                        if (!empty($teach_list[$v])) {
                            $teach .= ($teach ? ',' : '') . $teach_list[$v]['user_name'];
                        }
                    }
                    $val['teach_name'] = $teach;
                }
                $val['appoint_between'] = date('Y-m-d') . '   ' . date('H:i', $val['appoint_time']) . ' - ' . date('H:i', $val['end_time']);
                unset($val['teach_ids'], $val['appoint_time'], $val['end_time']);
                $result['data'][$key] = $val;
            }
        }
        return $result;
    }

    public static function del($info, $login)
    {
        $where = ['cart_id' => $info['cart_id'], "biz_id" => $login['biz_id']];
        $cart = FModel::instance('Cart')->getOneInfo($where);
        if (empty($cart)) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        if ($cart['status'] != 1) {
            EModel::instance()->setError(lang('data status'));
            return false;
        }
        $update = ["status" => 4];
        $result = FModel::instance('Cart')->updateInfo(['cart_id' => $info['cart_id']], $update);

        return $result;
    }

    public static function add($info, $login)
    {
        $list = self::checkCartData($info);
        if (empty($list)) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $cur_info = FModel::instance("Customer")->getInfoById($info['cus_id']);
        if (empty($cur_info) || $cur_info['status'] != 1 || $cur_info['biz_id'] != $login['biz_id']) {
            EModel::instance()->setError(lang('customer not exits'));
            return false;
        }
        $list = self::checkCartData($info);
        if (empty($list)) {
            EModel::instance()->setError(lang('cart not data'));
            return false;
        }
        $cart = [
            'biz_id' => $login['biz_id'],
            'cus_id' => $info['cus_id'],
            'dept_id' => $info['dept_id'],
            'room_id' => $info['room_id'],
            'user_num' => !empty($info['user_num']) ? intval($info['user_num']) : 1,
            'appoint_time' => strtotime($info['btime']),
            'end_time' => strtotime($info['etime']),
            'cus_type' => 2,
            'remarks' => !empty($info['remarks']) ? $info['remarks'] : '',
            'addtime' => time(),
            'user_id' => $login['user_id'],
        ];
        $result = FModel::instance("Cart")->addCartData($cart, $list);
        if ($result === false) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        } else {
            return true;
        }
    }

    public static function editCart($info, $login)
    {
        $list = self::checkCartData($info);
        if (empty($list)) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $where = [
            'cart_id' => $info['cart_id'], 'status' => [1, 2, 3, 4], 'biz_id' => $login['biz_id'],
        ];
        $one = FModel::instance("Cart")->getOneInfo($where);
        if (empty($one)) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $cur_info = FModel::instance("Customer")->getInfoById($info['cus_id']);
        if (empty($cur_info) || $cur_info['status'] != 1 || $cur_info['biz_id'] != $login['biz_id']) {
            EModel::instance()->setError(lang('customer not exits'));
            return false;
        }
        $list = self::checkCartData($info);
        if (empty($list)) {
            EModel::instance()->setError(lang('cart not data'));
            return false;
        }
        $cart = [
            'biz_id' => $login['biz_id'],
            'cus_id' => $info['cus_id'],
            'dept_id' => $info['dept_id'],
            'room_id' => $info['room_id'],
            'user_num' => !empty($info['user_num']) ? intval($info['user_num']) : 1,
            'appoint_time' => strtotime($info['btime']),
            'end_time' => strtotime($info['etime']),
            'cus_type' => 2,
            'remarks' => !empty($info['remarks']) ? $info['remarks'] : '',
            'addtime' => time(),
            'user_id' => $login['user_id'],
        ];
        $result = FModel::instance("Cart")->addCartData($cart, $list, 1);
        if ($result === false) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        } else {
            return true;
        }
    }

    public static function cartFollow($info, $login)
    {
        $where = ['cart_id' => $info['cart_id'], "biz_id" => $login['biz_id']];
        $cart = FModel::instance('Cart')->getOneInfo($where);
        if (empty($cart) || in_array($cart['status'], [1, 2, 3])) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $insert = [
            'biz_id' => $login['biz_id'],
            'cart_id' => intval($info['cart_id']),
            'cus_id' => intval($info['cus_id']),
            'follow_type' => intval($info['follow_type']),
            'follow_status' => intval($info['follow_status']),
            'follow_time' => strtotime($info['follow_time']),
            'follow_next' => !empty($info['follow_next']) ? strtotime($info['follow_next']) : 0,
            'content' => $info['content'],
            'addtime' => time(),
        ];
        dd($insert);
        $result = FModel::instance('CartFollow')->addInfo($insert);
        if (!$result) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        } else {
            $inser['fol_id'] = $result;
            return $insert;
        }
    }

    /**
     * 验证预约项目信息
     */
    private static function checkCartData($info)
    {
        if (empty($info['proj_data'])) {
            return [];
        }
        $proj_data = explode(',', $info['proj_data']);
        foreach ($proj_data as $key => $val) {
            if (empty($val)) continue;
            $arr = explode("|", $val);
            $proj_id = intval($arr[0]);
            if (!$proj_id) continue;
            $list[] = [
                'proj_id' => $proj_id,
                'teacher_id' => !empty($arr[1]) ? intval($arr[1]) : 0,
                'unit_price' => !empty($arr[2]) ? floatval($arr[2]) : 0,
                'buy_num' => !empty($arr[3]) ? intval($arr[3]) : 1,
                'addtime' => time(),
            ];
        }
        return $list;
    }

    /**
     * 时间筛选
     */
    public static function getSearchTime($info)
    {
        if (empty($info['time_type'])) {
            return [];
        }
        switch ($info['time_type']) {
            case 1:  //今日
                $time = [
                    'begin' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                    'end' => mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1,
                ];
                break;
            case 2: //昨天
                $time = [
                    'begin' => mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')),
                    'end' => mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1,
                ];
                break;
            case 3: //本周
                $time = [
                    'begin' => strtotime(date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600))),
                    'end' => time(),
                ];
                break;
            case 4: //上周
                $time = [
                    'begin' => mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y')),
                    'end' => mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - 7, date('Y')),
                ];
                break;
            case 5: //本月
                $time = [
                    'begin' => mktime(0, 0, 0, date('m'), 1, date('Y')),
                    'end' => mktime(23, 59, 59, date('m'), date('t'), date('Y')),
                ];
                break;
            case 6: //上月
                $time = [
                    'begin' => mktime(0, 0, 0, date('m') - 1, 1, date('Y')),
                    'end' => mktime(23, 59, 59, date('m') - 1, date('t'), date('Y')),
                ];
                break;
            default:
                !empty($info['btime']) && ($time['begin'] = strtotime($info['btime']));
                !empty($info['etime']) && ($time['time'] = strtotime($info['etime']) + 86400 - 1);
                break;
        }
        return !empty($time) ? $time : [];
    }

    /**
     * 获取购物车详情
     */
    public function detail($info,$login)
    {
        $where = ['cart_id' => $info['cart_id'], "biz_id" => $login['biz_id']];
        $field = 'cart_id,cus_id,dept_id,room_id,user_num,appoint_time,end_time,cus_type,remarks,status';
        $cart  = FModel::instance('Cart')->getOneInfo($where,$field);
        $cart  = $cart ? $cart->toArray() : [];
        if (empty($cart) || in_array($cart['status'], [1, 2, 3])) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $where = ['cart_id'=>$info['cart_id'],'status'=>1];
        $field = ['list_id','proj_id','teacher_id','unit_price','buy_num'];
        $list  = FModel::instance("CartList")->getListInfo($where,$field);
        $list  = !empty($list) ? $list->toArray() : [];
        if(empty($list)){
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }
        $return = [
            'info' => $cart,
            'list' => $list,
        ];
        return $return;
    }
}