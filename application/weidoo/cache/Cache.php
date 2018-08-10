<?php
/**
 * 重写 thnkphp Cache驱动，以达到扩展 thinkphp redis类
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/24
 * Time: 11:43
 */

namespace app\weidoo\cache;

use think\Loader;
use think\Cache as tpCache;

class Cache extends tpCache
{

    /**
     * 重写 连接缓存
     * @access public
     * @param  array         $options  配置数组
     * @param  bool|string   $name 缓存连接标识 true 强制重新连接
     * @return Driver
     */
    public function connect(array $options = [], $name = false)
    {

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset($this->instance[$name])) {
            $type = !empty($options['type']) ? $options['type'] : 'File';

            if (true === $name) {
                $name = md5(serialize($options));
            }

            $this->instance[$name] = Loader::factory($type, '\\app\\weidoo\\cache\\driver\\', $options);   //修改了这里
        }

        return $this->instance[$name];
    }
}