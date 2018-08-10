<?php
/**
 * 配置系统初始化行为 - 访问认证
 *
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/23
 * Time: 09:27
 */
namespace app\behavior;

use think\facade\Request;
use think\facade\Config;
use think\facade\Session;

class Http
{

    private static $request;
    private static $config;


    /**
     * 行为自动调用方法：行为入口方法
     * @param $params
     */
    public function run($params)
    {

        self::$request = Request::instance();
        self::$config = Config::get('api.');
        self::init();
    }

    /**
     * 认证开始
     * @return mixed
     */
    private static function init()
    {
        self::checkDomain();
        self::checkMethod();
        self::checkToken();
        self::checkCsrf();
    }

    /**
     * 域名认证
     * @return mixed
     */
    private static function checkDomain()
    {
        if (self::$config['allow_origin'] && !in_array(self::$request->domain(), self::$config['allow_origin'])) {
            return out_error_json('非法域名');
        }
    }

    /**
     * 请求类型认证
     * @return mixed
     */
    private static function checkMethod()
    {
        if (self::$config['allow_method'] && !in_array(self::$request->method(), self::$config['allow_method'])) {
            return out_error_json('非法请求');
        }
    }

    /**
     * TOKEN认证
     * @return mixed
     */
    private static function checkToken()
    {

        if (self::$config['token']) {

            if (!self::$config['ignore_token']) {

                if (!self::$request->header(self::$config['token_name'])) {
                    return out_error_json(self::$config['token_name'] . '丢失1');
                }

            } else {
                if (!in_array(self::$request->path(), self::$config['ignore_token'])) {

                    if (!self::$request->header(self::$config['token_name'])) {
                        return out_error_json(self::$config['token_name'] . '丢失2');
                    }

                }

            }

        }
    }

    /**
     * CSRF认证
     * @return mixed
     */
    private static function checkCsrf()
    {

        if (self::$config['csrf']) {

            if (self::$request->isPost()) {

                if (!self::$request->header(self::$config['csrf_name'])) {
                    return out_error_json(self::$config['csrf_name'] . '丢失');
                }

                self::checkCsrfValue(self::$request->header(self::$config['csrf_name']));

            }

        }

    }

    /**
     * CSRF验证
     * @return mixed
     */
    private static function checkCsrfValue($csrf='')
    {
        if (!$csrf) {
            return out_error_json('令牌为空');
        }

        if (!Session::has(self::$config['csrf_name'])) {
            return out_error_json('令牌无效');
        }

        // 令牌验证
        if (Session::get(self::$config['csrf_name']) === $csrf) {
            // 防止重复提交
            Session::delete(self::$config['csrf_name']); // 验证完成销毁session
            return true;
        }

        // 开启TOKEN重置
        Session::delete(self::$config['csrf_name']);
        return false;

    }

}