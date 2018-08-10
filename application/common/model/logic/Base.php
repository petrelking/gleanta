<?php

namespace app\common\model\logic;

use think\facade\Cache;

class Base {

    private static $cacheType = 'file';

    public static function setCacheType($type)
    {
        if (!in_array($type,['file','redis'])) {
            return;
        }

        self::$cacheType = $type;
    }

    /**
     * 设置缓存数据
     * @param $name
     * @param $value
     * @param null $expire
     * @return bool
     */
    public static function setCache($name, $value, $expire = null)
    {
        return Cache::store(self::$cacheType)->set($name, $value, $expire);
    }

    /**
     * 获取缓存数据
     * @param $name
     * @param bool $default
     * @return mixed
     */
    public static function getCache($name, $default = false)
    {
        return Cache::store(self::$cacheType)->get($name, $default);
    }

    /**
     * 删除缓存数据
     * @param $name
     * @return bool
     */
    public static function delCache($name)
    {
        return Cache::store(self::$cacheType)->rm($name);
    }

    /**
     * redis 高级缓存
     * @return static
     */
    public static function redisCache(){
        return app('cache_wd_redis');
    }

}
