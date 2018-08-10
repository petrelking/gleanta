<?php
/**
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2017/11/23
 * Time: 17:29
 */

namespace app\weidoo\helper;


class Token {

    protected static $instance;

    private function __construct(){}

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Tree
     */
    public static function instance()
    {
        if (is_null(self::$instance)){
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function make($length = 16)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = self::createNonceStr($length);

        $string = "noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        return sha1($string);
    }

    private static function createNonceStr($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}