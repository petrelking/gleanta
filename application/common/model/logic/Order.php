<?php
namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;
use Respect\Validation\Rules\SubdivisionCode\FmSubdivisionCode;

class Order extends Base
{

    public static $model;

    /**
     * 下单
     * @param $info
     * @param $login
     * @return bool
     */
    public function addOrderData($info,$login)
    {
        $where   = ['cus_id'=>$info['cus_id'],'biz_id'=>$login['biz_id'],'status'=>1];
        $cusinfo = FModel::instance('Customer')->getOneInfo($where,"cus_id,cus_name");
        if(empty($cusinfo)){
            EModel::instance()->setError(lang('customer not exits'));
            return false;
        }
        $prod = $tech  = $prodAll = [];
        $info['items'] = $this->zlProdData($info['items']);
        foreach ($info['items'] as $key=>$val){ //验证数据
            $prod[] = $val['prod_id'];
            $tech[] = $val['teacher_id'];
            $val['use_card']  = !empty($val['use_card']) ? $this->arrangeCard($val['use_card']) : [];//使用的卡
            if(!empty($val['use_card'])){
                foreach ($val['use_card'] as $k=>$v){
                    (empty($prodAll) || !in_array($v['id'],$prodAll)) && ($prodAll[] = $v['id']);
                }
            }
            $val['all_money'] = $val['pay_money'] = $val['unit_price'] * $val['buy_num'];
            $val['give_data'] = !empty($val['give_data']) ? $this->arrangeCard($val['give_data']) : [];
            $info['items'][$key] = $val;
        }
        //验证数据
        $prod_list = $this->getProductByIds($login['biz_id'],$prod);
        $tech_list = FModel::instance('User')->getColumn(['user_id'=>$tech,'biz_id'=>$login['biz_id'],'status'=> 1],'dept_id,user_name,biz_id,mobile',"user_id");
        $field     = 'cc_id,card_name,sc_type,stime_type,stime,etime_type,etime,sc_type,card_balance,get_type,consumetime';
        $where     = ['cc_id'=>$prodAll,"cus_id"=>$info['cus_id'],"dept_id"=>$info['dept_id'],"status"=>1];
        $card_list = $prodAll ? FModel::instance("CustomerCard")->getColumn($where,$field,"cc_id") : '';
        $where     = ['cc_id'=>$prodAll];
        $field     = "cc_id,card_id,get_type,ce_content";
        $card_extra= $prodAll ? FModel::instance("CustomerCardExtra")->getColumn($where,$field,"cc_id") : '';
        if(!empty($card_extra)){
            foreach ($card_extra as $key=>$val){
                $card_extra[$key]['ce_content'] = !empty($val['ce_content']) ? unserialize($val['ce_content']) : [];
            }
        }
        $cardSort  = [1=>5,2=>4,3=>1,4=>3,5=>2];
        foreach($info['items'] as $key=>$val){
            if(empty($prod_list[$val['prod_id']])){ //验证产品
                EModel::instance()->setError(lang('prod not exits')." - ".$val['prod_id']);
                return false;
            } elseif(empty($tech_list[$val['teacher_id']])){ //验证老师
                EModel::instance()->setError(lang('teach not exits')." - ".$val['teacher_id']);
                return false;
            }
            $val['cus_id']   = $info['cus_id'];
            $val['dept_id']  = $info['dept_id'];
            $val['buy_type'] = 1; //项目类型  1=>商品 2=>项目 3=>套餐 4=>卡
            if(!empty($val['use_card'])){
                $use_card = $val['use_card'];
                $zhekou   = 0;
                foreach ($use_card as $k=>$v){
                    $v['card_type'] = !empty($card_list[$v['id']]) ? $card_list[$v['id']]['sc_type'] : 0;
                    $v['card_sort'] = $v['card_type'] ? $cardSort[$v['card_type']] : 0;
                    $v['more']      = !empty($card_list[$v['id']]) ? $card_list[$v['id']] : [];
                    if($v['card_type'] == 2){
                        $zhekou++;
                    }
                    $use_card[$k]   = $v;
                }
                if($zhekou>1){
                    EModel::instance()->setError(lang('only one dis card')." - ".$val['prod_id']);
                    return false;
                }
                $val['use_card'] = $this->arraySequence($use_card, "card_sort", $sort = 'SORT_ASC');
            }
            $info['items'][$key] = $val;
            //$this->userCardData($val,$card_list,$login);
        }
        $this->checkCardOk($info['items'],$card_list,$card_extra);
        exit();



        $give[]   = [];
        $order_no = date('YmdHis').mt_rand(1000,9999);
        foreach ($info['items'] as $key=>$val){
            $list[] = [
                'biz_id'     => $login['biz_id'],
                'order_no'   => $order_no,
                'prod_id'    => $val['prod_id'],
                'teacher_id' => $val['teacher_id'],
                'unit_price' => $val['unit_price'],
                'buy_num'    => $val['buy_num'],
                'money'      => $val['unit_price'] * $val['buy_num'],
                'addtime'    => time(),
                'use_card'   => $val['use_card'],
            ];
        }
        $main = [
            'biz_id'       => $login['biz_id'],
            'order_no'     => $order_no,
            'cus_id'       => $info['cus_id'],
            'dept_id'      => $cusinfo['dept_id'],
            'room_id'      => !empty($info['room_id']) ? $info['room_id'] : 0,
            'appoint_time' => $info['appoint_time'] ? strtotime($info['appoint_time']) : 0,
            'cus_type'     => 1,
            'remarks'      => $info['remarks'],
        ];
        $result = FModel::instance('OrderMain')->addOrder($main,$list,$give);
        if($result === false){
            EModel::instance()->setError(lang('execute error'));
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 整理收到的数组数据
     */
    private function zlProdData($data)
    {
        foreach ($data as $key=>$val){
            $val['prod_id']    = !empty($val['prod_id']) ? intval($val['prod_id']) : 0;
            $val['teacher_id'] = !empty($val['teacher_id']) ? intval($val['teacher_id']) : 0;
            $val['unit_price'] = !empty($val['unit_price']) ? intval($val['unit_price']) : 0;
            $val['buy_num']    = !empty($val['buy_num']) ? intval($val['buy_num']) : 1;
            $val['give_data']  = !empty($val['give_data']) ? trim($val['give_data']) : '';//使用的赠品
            if(empty($val['prod_id']) || empty($val['teacher_id'])){
                EModel::instance()->setError(lang('parameter empty'));
                return false;
            }
            $data[$key] = $val;
        }
        return $data;
    }

    /**
     * 验证卡类的数据是否满足
     */
    public function checkCardOk($list,$card_list,$card_extra)
    {
        $expType = [1=>'product',2=>'item',3=>'package',4=>'card']; //1=>商品 2=>项目 3=>套餐 4=>卡
        $ectype  = [1=>'cash',2=>'cutoff',3=>'timerange',4=>'totaltimes',5=>'stimes'];//1=>面值卡 2=>折扣 3=>时段卡 4=>疗程总次卡 5=>疗程分次卡
        foreach ($list as $k => $v) {
            if(intval($v['pay_money'] * 100)<1) {
                continue;
            }
            if(empty($v['use_card'])){
                continue;
            }
            foreach ($v['use_card'] as $key => $val) {
                if (empty($card_list[$val['id']]) || empty($card_extra[$val['id']])) {
                    echo "c";
                    EModel::instance()->setError(lang('card not exits') . " - " . $val['id']);
                    return false;
                }
                $cardinfo  = $card_list[$val['id']];
                $extrainfo = $card_extra[$val['id']];
                //验证时间
                $checkTime = $this->checkUseTime($cardinfo);
                if (time() < $checkTime['btime'] || time() > $checkTime['etime']) {
                    echo 'b';
                    EModel::instance()->setError(lang('card time error') . " - " . $val['id']);
                    return false;
                }
                $extra      = $extrainfo['ce_content'];
                $ce_content = !empty($extra[$ectype[$cardinfo['sc_type']]][$expType[$v['buy_type']]]) ? $extra[$ectype[$cardinfo['sc_type']]][$expType[$v['buy_type']]] : [];
                if(empty($ce_content)){
                    echo 'a';
                    EModel::instance()->setError('10001-'.lang('card not exits') . " - " . $val['id']);
                    return false;
                }
                dd($cardinfo,2);
                switch ($cardinfo['sc_type']){
                    case 1: //储值卡
                        if(intval($val['num'] * 100 ) > intval($cardinfo['card_balance'] * 100)){
                            EModel::instance()->setError(lang('money less than')." - ".$val['id']);
                            return false;
                        }
                        $card_list[$val['id']]['card_balance'] = $cardinfo['card_balance'] - $val['num'];
                        $dk_money = $val['num']; //抵扣的金额
                        $list[$k]['pay_money'] = $v['pay_money'] = $v['pay_money']- $val['num'];
                        break;
                    case 2: //折扣卡 - 分别按比例和金额
                        if(!empty($ce_content['ids']['cash'][$v['prod_id']])){ //抵扣金额
                            $front_money    = $v['pay_money'];
                            $zkmoney        = $ce_content['ids']['cash'][$v['prod_id']] * $v['buy_num'];
                            $v['pay_money'] = $v['pay_money'] > $zkmoney ? $zkmoney : $v['pay_money'];
                            $dk_money       = $front_money - $v['pay_money'];
                        } elseif(!empty($ce_content['ids']['cutoff'][$v['prod_id']])){ //抵扣固定比例
                            $front_money    = $v['pay_money'];
                            $v['pay_money'] = round($v['pay_money'] * $ce_content['ids']['cutoff'][$v['prod_id']] / 100,2);
                            $dk_money       = $front_money - $v['pay_money'];
                        }
                        $list[$k]['pay_money'] = $v['pay_money'];
                        break;
                    case 3: //时段卡
                        if(!in_array($v['prod_id'],$ce_content)){
                            if(intval($val['num'] * 100 ) > intval($cardinfo['card_balance'] * 100)){
                                echo 'x';
                                EModel::instance()->setError(lang('money less than')." - ".$val['id']);
                                return false;
                            }
                        }
                        if($cardinfo['consumetime'] == 0){
                            $dk_money = $v['pay_money'];
                            $list[$k]['pay_money'] = $v['pay_money'] = 0;
                        } elseif(intval($cardinfo['card_balance'])< $val['num']){ //折扣卡不足
                            echo "y";
                            EModel::instance()->setError(lang('card less than')." - ".$val['id']);
                            return false;
                        } else {
                            $card_list[$val['id']]['card_balance']   = $cardinfo['card_balance'] - $val['num'];
                            $list[$k]['pay_money'] = $v['pay_money'] =  $v['pay_money'] - $val['num'] * $v['unit_price'];
                        }
                        break;
                    case 4: //疗程总次卡
                        if(!in_array($v['prod_id'],$ce_content)){
                            if(intval($val['num'] * 100 ) > intval($cardinfo['card_balance'] * 100)){
                                echo 'cx';
                                EModel::instance()->setError(lang('money less than')." - ".$val['id']);
                                return false;
                            }
                        }
                        if($cardinfo['card_balance']<$val['num']){ //次数不足
                            EModel::instance()->setError(lang('card less than')." - ".$val['id']);
                            return false;
                        }
                        $card_list[$val['id']]['card_balance']   = $card_list[$val['id']]['card_balance'] - $val['num'];
                        $list[$k]['pay_money'] = $v['pay_money'] =  $v['pay_money'] - $cardinfo['num'] * $v['unit_price'];
                        break;
                    case 5: //疗程分次卡
                        dd($ce_content,2);
                        if(empty($ce_content[$v['prod_id']]) || $ce_content[$v['prod_id']] < $val['num']){
                            echo "xyx";
                            EModel::instance()->setError(lang('error - 1002')." - ".$val['id']);
                            return false;
                        }
                        $ce_content[$v['prod_id']] = $ce_content[$v['prod_id']] - $val['num'];
                        $list[$k]['pay_money'] = $v['pay_money'] =  $v['pay_money'] - $val['num'] * $v['unit_price'];
                        dd($ce_content);
                        break;
                }
            }

        }
        return [];

    }

    /**
     * 临时使用 - 根据商品ids 获取商品信息
     */
    public function getProductByIds($biz_id,$ids)
    {
        $where = ['biz_id'=>$biz_id,'sr_id'=>$ids,'status'=>1];
        $list  = FModel::instance('ServiceProduct')->getColumn($where,'sr_name,sr_type,price','sr_id');
        return $list ? : [];
    }

    /**
     * 整理前端过来的使用的卡信息
     */
    private function arrangeCard($data)
    {
        if(empty($data)){
            return [];
        }
        $data = explode(",",$data);
        $ids  = [];
        foreach ($data as $key=>$val){
            $arr = explode("|",$val);
            $id  = intval($arr[0]);
            if($id<1) continue;
            $num    = !empty($arr[1]) ? intval($arr[1]) : 1;
            $ids[]  = $id;
            $list[$id] = [
                'id'        => $id,
                'num'       => $num,
                'card_type' => 0,
            ];
        }
        return $list;
    }

    /**
     * 匹配使用的卡
     */
    private function userCardData($info,$card_list,$login)
    {
        $list  = $info['use_card'];
        $model = FModel::instance('CustomerCard');
        foreach ($list as $key=>$val){
            $info = $card_list[$val['id']];
            if(empty($info)){
                EModel::instance()->setError(lang('card not exits')." - ".$val['id']);
                return false;
            }
            //验证时间
            $checkTime = $this->checkUseTime($info);
            if(time()<$checkTime['btime'] || time()>$checkTime['etime']){
                EModel::instance()->setError(lang('card time error')." - ".$val['id']);
                return false;
            }
            switch ($info['sc_type']){
                case 1: //储值卡
                    if(intval($val['num'] * 100 ) > intval($info['vprice'] * 100)){
                        EModel::instance()->setError(lang('money less than')." - ".$val['id']);
                        return false;
                    }
                    $dk_money = $val['num'];
                    break;
                case 2: //折扣卡

                    break;
                case 3: //时段卡

                    break;
                case 4: //疗程总次卡

                    break;
                case 5: //疗程分次卡

                    break;
            }

        }
        exit();
        return $list;
    }

    /**
     * 我的订单列表
     */
    public function myOrderList($info,$login)
    {
        $where['biz_id'] = $login['biz_id'];
        !empty($info['cus_id']) && $where['cus_id'] = $info['cus_id'];
        !empty($info['dept_ids']) && $where['dept_id'] = explode(',',$info['dept_ids']);
        //支付状态
        switch ($info['ctype']){
            case 1: //未付款
                $where['status'] = 0;
                break;
            case 2://已付款
                $where['status'] = 1;
                break;
            case 3://分期
                $where['is_rest'] = 1;
                break;
        }
        $search_time = FModel::instance("Cart","common","logic")->getSearchTime($info);
        if (!empty($search_time)) {
            $begin = $search_time['begin'];
            $end   = $search_time['end'];
            !empty($begin) && empty($end) && ($end = $begin + 86400 - 1);
            empty($begin) && !empty($end) && ($begin = $end - 86400 + 1);
            $where['appoint_time'] = [
                'betwend', "{$begin},{$end}"
            ];
        }
        $paginate = [
            'recent'=> ['rows' => 1, 'simple' => false, 'config' => ['page' => input("page/d",1), ]]
        ];
        $field  = 'main_id,order_no,cus_id,dept_id,room_id,user_num,appoint_time,cus_type,remarks,';
        $field .= 'total_amount,pay_amount,real_amount,rest_amount';
        $list   = FModel::instance("OrderMain")->getListInfo($where,$field,"main_id desc ",$paginate,[],'',[]);
        $list   = $list ? $list->toArray() : [];
        if(empty($list['data'])) {
            return $list;
        }
        //获取详情
        $order_no = $cus_ids  = $teach_ids= $prod = [];
        foreach ($list['data'] as $key=>$val){
            $order_no[] = $val['order_no'];
            $cus_ids[]  = $val['cus_id'];
        }
        $where   = ['order_no'=>$order_no,'biz_id'=>$login['biz_id']];
        $sublist = FModel::instance("OrderList")->getColumn($where,'list_id,prod_id,teacher_id,unit_price,buy_num,money,status,order_no','list_id');
        if(empty($sublist)){
            return $list;
        }
        foreach ($sublist as $key=>$val){
            $teach_ids[] = $val['teacher_id'];
            $newsub[$val['order_no']][] = $val;
        }
        $cuslist    = FModel::instance('Customer')->getColumn(['cus_id'=>$cus_ids], "cus_id,cus_name,cus_mobile","cus_id");
        $teach_list = FModel::instance("User")->getColumn(['user_id'=>$teach_ids], 'user_id,user_name',"user_id");
        foreach ($list['data'] as $key=>$val){
            foreach ($newsub[$val['order_no']] as $k=>$v){
                $v['teacher_name']  = !empty($teach_list[$v['teacher_id']]) ? $teach_list[$v['teacher_id']] : ' - ';
                $val['teach_ids'][] = $v['teacher_id'];
                $val['teacher_name']= (!empty($val['teacher_name']) ? '/' : '').$v['teacher_name'];
                $val['cus_mobile']  = !empty($cuslist[$val['cus_id']]) ? $cuslist[$val['cus_id']]['cus_mobile'] : '-';
                $val['cus_name']    = !empty($cuslist[$val['cus_id']]) ? $cuslist[$val['cus_id']]['cus_name'] : '-';
                $val['prod_id'][]   = $v['prod_id'];
                $val['appoint_time']= date('m-d H:i',$val['appoint_time']);
            }
            $list['data'][$key] = $val;
        }
        return $list;
    }

    /**
     * 订单详情
     */
    public function orderDetail($info,$login)
    {
        $where   = ['main_id'=>$info['order_id'],'biz_id'=>$login['biz_id']];
        $field   = 'main_id,order_no,cus_id,dept_id,room_id,appoint_time,remarks,total_amount,pay_amount';
        $field  .= ',real_amount,rest_amount,is_rest,status,rec_dept,rec_user';
        $main    = FModel::instance('OrderMain')->getOneInfo($where,$field);
        if(empty($main)){
            EModel::instance()->setError(lang('order not exits'));
            return false;
        }
        $main = $main->toArray();
        //获取项目列表
        $where = ['order_no'=>$main['order_no']];
        $field = 'list_id,prod_id,teacher_id,unit_price,buy_num,money,status';
        $list  = FModel::instance('OrderList')->getListInfo($where,$field)->toArray();
        foreach ($list as $key=>$val){
            $teach_ids[] = $val['teacher_id'];
        }
        $teach_list = FModel::instance("User")->getColumn(['user_id'=>$teach_ids], 'user_id,user_name',"user_id");
        foreach ($list as $key=>$val){
            $val['teacher_name'] = !empty($teach_list[$val['teacher_id']]) ? $teach_list[$val['teacher_id']] : '-';
            $list[$key] = $val;
        }
        $return = [
            'main' => $main,
            'list' => $list,
        ];
        return $return;
    }

    /**
     * 验证使用时间
     * stime_type 1=>购买时间 2=>首次使用时间 3=>指定日期
     * etime_type 1=>不限时长 2=>固定时长 3=>指定日期
     */
    private function checkUseTime($val)
    {
        $btime = (in_array($val['stime_type'],[1,3])) ? $val['stime'] : time();
        if($val['etime_type'] == 1){
            $etime = time() + 86400 * 365 * 99;
        } elseif($val['etime_type'] == 2){ //固定长 etime的单位是天（年月多转成天）
            $etime = $btime + 86400 * $val['etime'];
        } else { //指定日期
            $etime = $val['etime'];
        }
        return [
            'btime' => $btime,
            'etime' => $etime,
        ];
    }

    /**
     * 取消订单
     */
    public function cancelOrder($info,$login)
    {
        $where   = ['main_id'=>$info['order_id'],'biz_id'=>$login['biz_id']];
        $field   = 'main_id,order_no,cus_id,dept_id,room_id,appoint_time,remarks,total_amount,pay_amount';
        $field  .= ',real_amount,rest_amount,is_rest,status,rec_dept,rec_user';
        $main    = FModel::instance('OrderMain')->getOneInfo($where,$field);
        if(empty($main)){
            EModel::instance()->setError(lang('order not exits'));
            return false;
        } elseif($main['status'] != 0){
            EModel::instance()->setError(lang('data status'));
            return false;
        }
        $main   = $main->toArray();
        $update = ['status'=>2];
        $result = FModel::instance("OrderMain")->updateInfo(['main_id'=>$main['main_id']],$update);
        if(!empty($result)){
            return 1;
        } else{
            return false;
        }
    }

    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }
}