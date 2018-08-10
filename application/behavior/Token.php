<?php
/**
 * 配置系统初始化行为 - token认证
 *
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/23
 * Time: 09:27
 */
namespace app\behavior;

use think\facade\Env;
use think\facade\Request;
use think\facade\Cache;

class Token
{

    private static $request;

    /**
     * 行为自动调用方法：行为入口方法
     * @param $params
     */
    public function run($params)
    {
        self::$request = Request::instance();
        self::init();
    }

    /**
     * 认证开始
     * @return mixed
     */
    private static function init()
    {
        //$redis_prefix = 'saas';
        //app('cache_wd_redis')->set('token_13',['user'=>'wandehua','auth'=>'admin']);
        //app('cache_wd_redis')->expire('token_13',3600);
        if ($token = self::$request->header('token')) {

            $user = app('cache_wd_redis')->get('token_'.$token);
            if(!$user){
                return out_error_json('身份已过期，请重新登陆');
            }

            self::$request->user = $user;

        }

    }

}