<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

if (!function_exists('get_route_key')) {
    /**
     * 路由缓存的Key自定义设置（闭包），默认为当前URL和请求类型的md5, 参见config/app.php:get_route_key配置项
     * @param \think\Request $request
     * @return string
     */
    function get_route_key($request)
    {
        return md5($request->url(true) . ':' . $request->method() . ':' . ($request->isAjax() ? 1 : 0));
    }
}

if (!function_exists('out_error_json')) {
    /**
     * 直接输出JSON错误信息
     * @param string|int $msg 提示信息 或 信息状态码
     * @param array $data 错误数据
     * @param int $code 错误码
     * @param int $http_code http 状态码
     * @param array $header header信息
     * @return RuntimeException
     */
    function out_error_json($msg = '操作失败', $data = [], $code = 0, $http_code = 400, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => get_code_message($msg),
            'time' => time(),
            'data' => $data,
        ];
        throw new think\exception\HttpResponseException(\think\facade\Response::create($result, 'json', $http_code)->header($header));
    }
}

if (!function_exists('out_success_json')) {
    /**
     * 直接输出JSON成功信息
     * @param string|int $msg 提示信息 或 信息状态码
     * @param array $data 错误数据
     * @param int $code 错误码
     * @param int $http_code http 状态码
     * @param array $header header信息
     * @return RuntimeException
     */
    function out_success_json($msg = '操作成功', $data = [], $code = 1, $http_code = 200, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => get_code_message($msg),
            'time' => time(),
            'data' => $data,
        ];
        throw new think\exception\HttpResponseException(\think\facade\Response::create($result, 'json', $http_code)->header($header));
    }
}

if (!function_exists('get_code_message')) {
    /**
     * 拉取code的值
     * @param  string|int $code 提示信息 或 信息状态码
     * @return string|int
     */
    function get_code_message($code = 0)
    {
        if (is_string($code)) {
            return $code;
        }

        $code_arr = config("api.code");

        if (key_exists($code, $code_arr)) {
            $msg = $code_arr[$code];
        } else {
            $msg = $code;
        }

        return $msg;
    }
}

if (!function_exists('load_model')) {
    /**
     * 实例化Model （作废）
     * @param string $name Model名称
     * @param string $module 模块名称
     * @param string $layer 业务层名称
     * @return mixed
     */
    function factory_model($name, $module = '', $layer = '')
    {
        // 回溯跟踪
        $backtrace_array = debug_backtrace(false, 1);
        // 调用者目录名称
        $current_directory_name = basename(dirname($backtrace_array[0]['file']));
        // 当前调用模型的层级（当前模块 service 模型层，当前模块 logic 模型层，公共模型 model数据层 三层）
        // 加载模型规则
        switch ($current_directory_name) {
            //controller中调用当前模块 service 模型层
            case \think\facade\Env::get('WD_CONTROLLER_LAYER_CONTROLLER_NAME'):
                $current_module = \think\facade\Request::instance()->module();
                $current_layer  = \think\facade\Env::get('WD_MODEL_LAYER_SERVICE_NAME');
                break;
            //模型service层 中调用当前模块 logic 模型层
            case \think\facade\Env::get('WD_MODEL_LAYER_SERVICE_NAME'):
                $current_module = \think\facade\Request::instance()->module();
                $current_layer  = \think\facade\Env::get('WD_MODEL_LAYER_LOGIC_NAME');
                break;
            //模型logic层 中调用公共模型model层
            case \think\facade\Env::get('WD_MODEL_LAYER_LOGIC_NAME'):
                //模型model层 中调用公共模型model层
            case \think\facade\Env::get('WD_MODEL_LAYER_MODEL_NAME'):
                //其它模型层 中调用公共模型model层
            default:
                $current_module = \think\facade\Env::get('WD_MODULE_COMMON');
                $current_layer  = \think\facade\Env::get('WD_MODEL_LAYER_MODEL_NAME');
                break;
        }

        $current_module = !empty($module) ? $module : $current_module;
        $current_layer  = !empty($layer) ? $layer : $current_layer;

        return Loader::factory($name, '\\app\\' . $current_module . '\\model\\' . $current_layer . '\\');
    }
}

if (!function_exists('dd')) {

    function dd($data, $exit = 1)
    {
        echo "<pre>";
        print_r($data);
        echo "<pre/>";
        $exit == 1 && exit();
    }

}

if (!function_exists('encrypt_md5')) {

    function encrypt_md5($str, $key = '')
    {
        $key = $key ?: \think\facade\env::get('WD_ENCRYPT_KEY');
        return '' === $str ? '' : md5(sha1($str) . $key);
    }

}

if (!function_exists("getFirstCharter")) {

    /**
     * 获取中文字符拼音首字母
     */
    function getFirstCharter($str)
    {
        if (empty($str)) {
            return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s  = $s2 == $str ? $s1 : $str;
        if (empty($s{1})) {
            return '';
        }
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return "";
    }

}

if (!function_exists("getArrayColumn")) {

    /**
     * 取二维数值的某列组成新的数值
     * @param $list 数组
     * @param $field 字段名字
     * @param $isstring 是否已字符串形式（逗号分隔）返回
     */
    function getArrayColumn($list, $field, $isstring = 0)
    {
        if (empty($list)) return $isstring == 1 ? '' : [];
        if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
            $newlist = array_column($list, $field);
        } else {
            foreach ($list as $key => $val) {
                $newlist[] = $val["{$field}"];
            }
        }
        $newlist = $isstring == 1 ? implode(',', $newlist) : $newlist;
        return $newlist;
    }

}

if (!function_exists('date_for')) {
    /**
     * 日期格式
     */
    function date_for($time, $type = 'Y-m-d')
    {
        return date($type, $time);
    }

}