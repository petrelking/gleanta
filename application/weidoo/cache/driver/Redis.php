<?php
/**
 * 扩展 thinkphp redis
 *
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/23
 * Time: 18:04
 */

namespace app\weidoo\cache\driver;

use think\cache\driver\Redis as tpResis;

class Redis extends tpResis
{

    /**
     * 设置过期时间
     * @access public
     * @param $name 缓存变量名
     * @param null $expire 过期时间
     * @return bool
     */
    public function expire($name,$expire = null)
    {
        $this->writeTimes++;
        if(!is_null($expire)) {
            return $this->handler->expire($this->getCacheKey($name), $this->getExpireTime($expire));
        }

        return false;
    }

    /**
     * 将一个或多个值插入到列表头部
     * @param $name 缓存变量名
     * @param $value 缓存数据
     * @return string
     */
    public function lPush($name, $value)
    {
        $this->writeTimes++;
        return $this->handler->lPush($this->getCacheKey($name), $this->serialize($value));
    }

    /**
     * 将一个或多个值插入到列表尾部
     * @param $name 缓存变量名
     * @param $value 缓存数据
     * @return string
     */
    public function rPush($name, $value)
    {
        $this->writeTimes++;
        return $this->handler->rPush($this->getCacheKey($name), $this->serialize($value));
    }

    /**
     * 移出并获取列表的第一个元素
     * @param $name
     * @return bool|mixed
     */
    public function lPop($name)
    {
        $this->readTimes++;
        $value = $this->handler->lPop($this->getCacheKey($name));

        if (is_null($value) || false === $value) {
            return false;
        }

        return $this->unserialize($value);

    }

    /**
     * 移除并获取列表最后一个元素
     * @param $name
     * @return bool|mixed
     */
    public function rPop($name)
    {
        $this->readTimes++;
        $value = $this->handler->rPop($this->getCacheKey($name));

        if (is_null($value) || false === $value) {
            return false;
        }

        return $this->unserialize($value);

    }
}