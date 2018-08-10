<?php
/**
 * 模型工厂类
 *
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2017/11/21
 * Time: 17:15
 */

namespace app\weidoo\library\model;

use think\Container;

class FactoryModel {

    protected static $instances=[];
    protected static $bind=[];
    protected $name;

    private function __construct($name){
        $this->name = $name;
    }
    /**
     * 实例化Model
     * @param string    $name Model名称
     * @param string    $module 模块名称
     * @param string    $layer 业务层名称
     * @return mixed
     */
    public static function instance($name, $module='', $layer='',$switcher='model'){
        // 回溯跟踪
        $backtrace_array = debug_backtrace(false, 1);

        // 调用者目录名称
        $current_directory_name = basename(dirname($backtrace_array[0]['file']));

        $backtrace = static::getBacktrace($current_directory_name, $name, $module, $layer,$switcher);

        if(!isset(static::$bind[$backtrace['key']])){
            // self::$instance[$_key] = \think\Loader::factory($name, '\\app\\'.$current_module.'\\model\\' . $current_layer.'\\');
            static::$bind[$backtrace['key']] = $backtrace['namespace'];
        }

        return new static($backtrace['key']);
    }

    private static function getBacktrace($current_directory_name, $name, $module, $layer,$switcher='model'){
        // 当前调用模型的层级（当前模块 service 模型层，当前模块 logic 模型层，公共模型 model数据层 三层）
        // 加载模型规则
        switch ($current_directory_name) {
            //controller中调用当前模块 service 模型层
            case \think\facade\Env::get('WD_CONTROLLER_LAYER_CONTROLLER_NAME'):
//                $current_module = \think\facade\Request::instance()->module();
//                $current_layer = \think\facade\Env::get('WD_MODEL_LAYER_SERVICE_NAME');
//                break;
            //模型service层 中调用当前模块 logic 模型层
            case \think\facade\Env::get('WD_MODEL_LAYER_SERVICE_NAME'):
                $current_module = \think\facade\Env::get('WD_MODULE_COMMON');
                $current_layer = \think\facade\Env::get('WD_MODEL_LAYER_LOGIC_NAME');
                break;
            //模型logic层 中调用公共模型model层
            case \think\facade\Env::get('WD_MODEL_LAYER_LOGIC_NAME'):
                //模型model层 中调用公共模型model层
            case \think\facade\Env::get('WD_MODEL_LAYER_MODEL_NAME'):
                //其它模型层 中调用公共模型model层
            default:
                $current_module = \think\facade\Env::get('WD_MODULE_COMMON');
                $current_layer = \think\facade\Env::get('WD_MODEL_LAYER_MODEL_NAME');
                break;
        }
        $current_module = !empty($module)?$module:$current_module;
        if($layer===false) {
            $ns = '\\app\\' .$current_module. '\\'.$switcher.'\\' .$name;
            $current_layer = false;
            $key = $current_module. '_'.$name.'_' .$switcher;
        }else{
            $current_layer = !empty($layer) ? $layer : $current_layer;
            $ns = '\\app\\' .$current_module. '\\model\\' . $current_layer. '\\' .$name;
            $key =$current_module.'_'.$name.'_'.$current_layer;
        }
        return [
            'key' => $key,
            'module' => $current_module,
            'layer' => $current_layer,
            'namespace' => $ns,
        ];
    }

    /**
     * 重新创建类的实例
     * @access public
     * @param  string        $class          类名或者标识
     * @param  bool          $newInstance    是否每次创建新的实例
     * @return mixed
     */
    public function make($class='')
    {
        return static::createInstance($class, [], true);
    }

    /**
     * 创建实例
     * @access protected
     * @param  string    $class          类名或标识
     * @param  array     $args           变量
     * @param  bool      $newInstance    是否每次创建新的实例
     * @return object
     */
    protected function createInstance($class = '', $args = [], $newInstance = false)
    {
        $class = $class ?: $this->name;

        if (isset(static::$bind[$class])) {
            $class = static::$bind[$class];
        }

        if (isset(static::$instances[$class]) && !$newInstance) {
            return static::$instances[$class];
        }

        static::$instances[$class] = Container::getInstance()->make($class, $args, $newInstance);

        return static::$instances[$class];
    }

    public function __call($method, $arguments)
    {
         return call_user_func_array([static::createInstance(), $method], $arguments);
    }
}