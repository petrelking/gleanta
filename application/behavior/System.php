<?php
/**
 * 配置系统初始化行为
 *
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/19
 * Time: 17:04
 */
namespace app\behavior;

use think\facade\Env;
use think\facade\Config;

class System
{

    /**
     * 行为自动调用方法：行为入口方法
     * @param $params
     */
    public function run($params)
    {
        self::init();
        self::setEnv();
        self::bind();
    }

    /**
     * 配置常量
     */
    public static function init()
    {
        $time = time();
        $root_path = env::get('ROOT_PATH') . 'public' . DIRECTORY_SEPARATOR;
        $upload_path = $root_path . 'upload' . DIRECTORY_SEPARATOR;

        // 设置路径环境变量
        Env::set([
            // 系统超级管理员ID
            'wd_administrator_id' => 'common',
            // 通用模块名称
            'wd_module_common' => 'common',
            // 逻辑层名称
            'wd_model_layer_logic_name' => 'logic',
            // 系统服务层名称
            'wd_model_layer_service_name' => 'service',
            // 数据模型层名称
            'wd_model_layer_model_name' => 'model',
            // 系统控制器层名称
            'wd_controller_layer_controller_name' => 'controller',
            // 时间戳常量
            'wd_now_time' => $time,
            // 日期常量
            'wd_now_date' => date('Ymd', $time),
            // 日期常量
            'wd_now_date_now' => date('Y-m-d H:i:s', $time),
            // 系统加密KEY
            'wd_encrypt_key' => 'xt_=r23^lsd@#)trios&deux{13-05la39b/a932r:',
            // 静态资源目录路径
            'wd_public_path' => $root_path,
            // 文件上传目录路径
            'wd_upload_path' => $upload_path,
            // 图片上传目录路径
            'wd_upload_images_path' => $upload_path . 'images' . DIRECTORY_SEPARATOR,
            // 文件上传目录路径
            'wd_upload_file_path' => $upload_path . 'files' . DIRECTORY_SEPARATOR,
            // 文件上传目录相对路径
            'wd_upload_relative' => '/public/upload/',

        ]);
    }

    /**
     * 配置环境变量
     */
    public static function setEnv()
    {
        /**
         * 判断是否存在ENV.EXIST，存在表明存在.env文件，并且在应用初始化的时候系统已自动加载
         * 所以直接使用即可
         */

        if (Env::get('ENV.EXIST')==true) {

            //配置config - app
            Config::set('app.default_return_type', Env::get('APP.RETURN_TYPE'));
            Config::set('app.default_filter', Env::get('APP.FILTER'));
            Config::set('app.url_domain_root', Env::get('APP.DOMAIN'));
            // Config::set('app.empty_controller', Env::get('APP.EMPTY_CONTROLLER'));
            Config::set('app.route_check_cache', Env::get('APP.ROUTE_CACHE'));
            Config::set('app.route_check_cache_key', Env::get('APP.ROUTE_CACHE_KEY'));

            //配置config - database
            Config::set('database.type', Env::get('DATABASE.TYPE'));
            Config::set('database.hostname', Env::get('DATABASE.HOSTNAME'));
            Config::set('database.database', Env::get('DATABASE.DATABASE'));
            Config::set('database.username', Env::get('DATABASE.USERNAME'));
            Config::set('database.password', Env::get('DATABASE.PASSWORD'));
            Config::set('database.hostport', Env::get('DATABASE.PORT'));
            Config::set('database.charset', Env::get('DATABASE.CHARSET'));
            Config::set('database.prefix', Env::get('DATABASE.PREFIX'));

            //配置api
            Config::set('api.token', Env::get('API.TOKEN'));
            Config::set('api.csrf', Env::get('API.CSRF'));

            //配置cache
            Config::set('cache.type', Env::get('CACHE.TYPE'));
            Config::set('cache.default.type', Env::get('CACHE.DEFAULT_TYPE'));
            Config::set('cache.default.prefix', Env::get('CACHE.DEFAULT_PREFIX'));
            Config::set('cache.default.expire', Env::get('CACHE.DEFAULT_EXPIRE'));
            Config::set('cache.default.host', Env::get('CACHE.DEFAULT_HOST'));
            Config::set('cache.file.type', Env::get('CACHE.FILE_TYPE'));
            Config::set('cache.file.path', Env::get('CACHE.FILE_PATH'));
            Config::set('cache.file.prefix', Env::get('CACHE.FILE_PREFIX'));
            Config::set('cache.file.expire', Env::get('CACHE.FILE_EXPIRE'));

            //配置log
            Config::set('log.type', Env::get('LOG.TYPE'));
            Config::set('log.host', Env::get('LOG.HOST'));
            Config::set('log.show_included_files', Env::get('LOG.SHOW_INCLUDE_FILES'));
            Config::set('log.force_client_ids.0', Env::get('LOG.FORCE_CLIENT_IDS'));
            Config::set('log.allow_client_ids.0', Env::get('LOG.allow_CLIENT_IDS'));


        }

    }

    /**
     * 绑定服务容器
     */
    public static function bind()
    {
        //绑定扩展Redis容器
        bind('cache_wd_redis','app\weidoo\cache\Cache');

    }

}