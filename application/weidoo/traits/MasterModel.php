<?php
/**
 * Created by WanDeHua.
 * User: WanDeHua
 * Email:271920545@qq.com
 * Date: 2017/11/21
 * Time: 16:56
 */
namespace app\weidoo\traits;

use think\Cache;

trait MasterModel
{
    /**
     * 设置缓存数据
     * @param $name
     * @param $value
     * @param null $expire
     * @return bool
     */
    public static function setCache($name, $value, $expire = null)
    {
        return Cache::set($name, $value, $expire);
    }

    /**
     * 获取缓存数据
     * @param $name
     * @param bool $default
     * @return mixed
     */
    public static function getCache($name, $default = false)
    {
        return Cache::get($name, $default);
    }

    /**
     * 删除缓存数据
     * @param $name
     * @return bool
     */
    public static function delCache($name)
    {
        return Cache::rm($name);
    }



}
