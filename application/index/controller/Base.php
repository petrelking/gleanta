<?php
/**
 * controller 基类
 */
namespace app\index\controller;

use think\facade\Request;
use think\Controller;
use think\Loader;
class Base extends Controller
{
   use \app\weidoo\traits\MasterControl;

    /**
     * 登陆账号信息
     * 忽略认证的接口，token为空数据，比如登陆，注册等不需要验证token(登陆账号信息)的请求，
     * 否则$user一定不为空
     * @var array
     */
    public $user = [];

    /** 验证成功后的数据
     * @var array
     */
    public static $validateData = [];

    /**
     * 不需要数据验证的方法
     * @var array
     */
    protected static $no_validation_methods = [];

    /**
     * 服务层model
     */
    protected static $service = null;

    public function initialize()
    {
        if (empty($this->user)) {
            $this->user = Request::instance()->user;
        }
        self::$validateData = self::beginValidate();
    }

    /**
     * 开始验证
     */
    public static function beginValidate()
    {
        if (empty(static::$no_validation_methods)) {
            return self::checkValidate(Request::instance()->action());
        }

        if (is_string(static::$no_validation_methods)) {
            static::$no_validation_methods = explode(',', static::$no_validation_methods);
        }

        if (!in_array(Request::instance()->action(), static::$no_validation_methods)) {
            return self::checkValidate(Request::instance()->action());
        }

        return [];
    }

    /**
     * 检查控制器是否定义getValidate方法,并自动调用
     * @param $action 当前要操作的控制器方法
     * @return \RuntimeException|array
     */
    public static function checkValidate($action)
    {
        //Error空控制器跳过
        if (Request::instance()->controller() == 'Error') {
            return;
        }

        if (!method_exists(static::class, 'getValidate')) {
            return out_error_json(static::class . ' getValidate 方法不存在', [], 0, 500);
        }
        //调用控制中的getValidate方法，获取验证数据
        $v_data = call_user_func_array([static::class, 'getValidate'], [$action]);

        //控制中的getValidate没有定义$action，直接报错，不想验证通过 $no_validation_methods 进行设置
        if (is_array($v_data) && empty($v_data)) {
            return out_error_json(43006);
        }

        //调用验证类 进行验证
        $v = Loader::factory(Request::instance()->controller(), substr(__NAMESPACE__, 0, strrpos(__NAMESPACE__, '\\')) . '\\validate\\')
            ->scene($action)
            ->batch(true);

        //未通过验证，报错
        if (!$v->check($v_data)) {
            return out_error_json(43002, $v->getError());
        }

        //返回通过验证后的数据
        return $v_data;
    }

    /**
     * 获取验证数据
     * @param $action 请求操作方法名称
     */
    public static function getValidate($action)
    {

    }

}
