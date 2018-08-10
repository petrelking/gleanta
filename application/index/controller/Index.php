<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2018/7/26
 * Time: 13:48
 */

namespace app\index\controller;


use think\App;
use \think\facade\Config;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Index extends Base
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->batchValidate=false;
    }

    public function index()
    {

        $m = FModel::instance('Index','payment',false);
        $biz_id = 1;//$this->user['biz_id']
        $re = $m->getPaymentsList($biz_id);
        $this->result(re,200,'ok');
    }

}