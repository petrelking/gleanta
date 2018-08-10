<?php
/**
 * 模型错误类
 *
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2017/11/21
 * Time: 17:15
 */

namespace app\weidoo\library\model;

class ErrorModel {

    protected static $error;
    protected static $instance;

    private function __construct()
    {
    }

    public static function instance(){

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;

    }

    /**
     * 设置模型的错误信息
     * @access public
     * @return string|array
     */
    public static function setError($mes)
    {
        self::$error = $mes;
    }

    /**
     * 返回模型的错误信息
     * @access public
     * @return string|array
     */
    public static function getError()
    {
        return self::$error;
    }

}