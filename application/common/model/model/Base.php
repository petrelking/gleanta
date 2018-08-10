<?php

namespace app\common\model\model;

use think\Model;

class Base extends Model
{

    // 查询对象
    private static $ob_query = null;

    //软删除
    private static $status_name = 'status';
    private static $status_value = 3;

    /**
     * 新增数据
     * @param array $data
     * @return false|int
     */
    public function addInfo($data = [])
    {
        return $this->allowField(true)->save($data, [], true) ? $this->id : 0;

    }

    /**
     * 修改数据
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function editInfo($data = [], $where = [])
    {
        return $this->isUpdate(true)->save($data, $where);
    }

    /**
     * 更新数据
     * @param array $where
     * @param array $data
     * @return false|int
     */
    public function updateInfo($where = [], $data = [])
    {
        if (isset($data['_method'])) {
            unset($data['_method']);
        }
        return $this->allowField(true)->update($data, $where);
    }

    /**
     * 统计数据
     * @param array $where
     * @param string $stat_type
     * @param string $field
     * @param array $join
     * @param string $alias
     * @return mixed
     */
    public function statInfo($where = [], $stat_type = 'count', $field = 'id', $join = [], $alias = '')
    {
        self::$ob_query = $this->where($where);

        $has_join = (count($join) > 0) ? true : false;

        $has_join && self::$ob_query = self::$ob_query->alias($alias)->join($join);

        return self::$ob_query->$stat_type($field);
    }

    /**
     * 批量设置或更新数据列表
     * @param array $data_list
     * @param bool $replace
     * @return array|false
     */
    public function setAllInfo($data_list = [], $replace = false)
    {
        return $this->saveAll($data_list, $replace);
    }

    /**
     * 设置某个字段值
     * @param array $where
     * @param string $field 要更新字段名称
     * @param string $value 理更新字段的值
     * @return false|int
     */
    public function setFieldValue($where = [], $field = '', $value = '')
    {
        return $this->updateInfo($where, [$field => $value]);
    }

    /**
     * 删除数据
     * @param array $where 条件
     * @param bool $is_true 是否真删除
     * @return false|int
     */
    public function deleteInfo($where = [], $is_true = false)
    {
        return $is_true ? $this->where($where)->delete() : $this->setFieldValue($where, self::$status_name, self::$status_value);
    }

    /**
     * 以某一字段为key取值
     *
     * @param array $where
     * @param string $field 查询字段名称
     * @param string $key 字段名称 指定某字段的值作为key
     * @return array
     * $field 1位 返回结果为1维 [0或$key的值=>$field的值]
     * $field 2位 返回结果为1维 [$field_1的值=>$field_2的值]
     * $field 大于2位 返回结果为2位 [$key的值或$field_1的值=>$field_1的值, $field_2的值, $field_3的值, $field_n的值]
     */
    public function getColumn($where = [], $field = '', $key = '')
    {
        return $this->where($where)->column($field, $key);
    }

    /**
     * 获取某个字段的值
     * @param array $where
     * @param string $field 字段名称,只能传一个字段
     * @param null $default 没有找到数据时默认值
     * @param bool $force 强制转为数字类型
     * @return mixed
     */
    public function getValue($where = [], $field = '', $default = null, $force = false)
    {
        return $this->where($where)->value($field, $default, $force);
    }

    /**
     * 获取单条数据
     * @param array $where
     * @param bool $field
     * @return array|false|\PDOStatement|string|Model
     */
    public function getOneInfo($where = [], $field = true)
    {
        //(!isset($where[self::$status_name])) && $where[self::$status_name] = ['neq', self::$status_value];
        return $this->where($where)->field($field)->find();
    }

    /**
     * 获取多条数据
     * @param array $where 条件
     * @param bool $field 字段
     * @param string $order 排序
     * @param array $paginate 分页
     * @param array $join 多表
     * @param string $alias 当前表别名
     * @param array $group 分组
     * @param null $limit 数量
     * @param bool $fetchSql 生成sql
     * @param null $data
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     */
    public function getListInfo($where = [], $field = true, $order = '', $paginate = ['classic' => ['page' => null, 'limit' => null], 'recent' => ['rows' => null, 'simple' => false, 'config' => []]], $join = [], $alias = '', $group = ['group' => '', 'having' => ''], $limit = null, $fetchSql = false, $data = null)
    {
        //是否有join查询
        $has_join = (count($join) > 0) ? true : false;
        //默认查询条件 $where中没有self::$status_name 或 不是join 查询
        //(!isset($where[self::$status_name]) && !$has_join) && $where[self::$status_name] = ['neq', self::$status_value];
        //where
        self::$ob_query = $this->where($where);
        //join
        $has_join && self::$ob_query = self::$ob_query->alias($alias)->join($join);
        //field
        self::$ob_query = self::$ob_query->field($field);
        //group
        !empty($group['group']) && self::$ob_query = self::$ob_query->group($group['group']);
        //having
        !empty($group['having']) && self::$ob_query = self::$ob_query->having($group['having']);
        //order
        !empty($order) && self::$ob_query = self::$ob_query->order($order);
        //分页

        if (count($paginate) > 0) {
            //老版本分页 page
            if (array_key_exists('classic', $paginate) && isset($paginate['classic']['page'])) {
                //支持生成sql语句
                return self::$ob_query->fetchSql($fetchSql)->page($paginate['classic']['page'], $paginate['classic']['limit'])->select($data);
                //新版本分页 paginate
            } else if (array_key_exists('recent', $paginate) && isset($paginate['recent']['rows'])) {
                //不支持生成sql语句
                $paginate['recent']['simple'] = isset($paginate['recent']['simple']) ? $paginate['recent']['simple'] : false;
                $paginate['recent']['config'] = (isset($paginate['recent']['config']) && is_array($paginate['recent']['config'])) ? $paginate['recent']['config'] : [];
                return self::$ob_query->paginate($paginate['recent']['rows'], $paginate['recent']['simple'], $paginate['recent']['config']);
            }
        }
        //limit
        !empty($limit) && self::$ob_query = self::$ob_query->limit($limit);
        //支持生成sql语句

        return self::$ob_query->fetchSql($fetchSql)->select($data);
    }

    /**
     * 子查询
     * @param $sub_query        SQL语句
     * @param array $where 条件
     * @param bool $field 字段
     * @param string $order 排序
     * @param array $paginate 分页
     * @param array $join 多表
     * @param string $alias 当前表别名
     * @param array $group 分组
     * @param null $limit 数量
     * @param bool $fetchSql 生成sql
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     */
    public function subQuery($sub_query, $where = [], $field = true, $order = '', $paginate = ['classic' => ['page' => null, 'limit' => null], 'recent' => ['rows' => null, 'simple' => false, 'config' => []]], $join = [], $alias = '', $group = array('group' => '', 'having' => ''), $limit = null, $fetchSql = false, $data = null)
    {

        //是否有join查询
        $has_join = (count($join) > 0) ? true : false;
        //where
        self::$ob_query = $this->table('(' . $sub_query . ')')->where($where);
        //join
        $has_join && self::$ob_query = self::$ob_query->alias($alias)->join($join);
        //field
        self::$ob_query = self::$ob_query->field($field);
        //group
        !empty($group['group']) && self::$ob_query = self::$ob_query->group($group['group']);
        //having
        !empty($group['having']) && self::$ob_query = self::$ob_query->having($group['having']);
        //order
        !empty($order) && self::$ob_query = self::$ob_query->order($order);
        //分页

        if (count($paginate) > 0) {
            //老版本分页 page
            if (array_key_exists('classic', $paginate) && isset($paginate['classic']['page'])) {
                //支持生成sql语句
                return self::$ob_query->fetchSql($fetchSql)->page($paginate['classic']['page'], $paginate['classic']['limit'])->select($data);
                //新版本分页 paginate
            } else if (array_key_exists('recent', $paginate)) {
                //不支持生成sql语句
                $paginate['recent']['simple'] = isset($paginate['recent']['simple']) ? $paginate['recent']['simple'] : false;
                $paginate['recent']['config'] = (isset($paginate['recent']['config']) && is_array($paginate['recent']['config'])) ? $paginate['recent']['config'] : [];
                return self::$ob_query->paginate($paginate['recent']['rows'], $paginate['recent']['simple'], $paginate['recent']['config']);
            }
        }
        //limit
        !empty($limit) && self::$ob_query = self::$ob_query->limit($limit);
        //支持生成sql语句
        return self::$ob_query->fetchSql($fetchSql)->select($data);

    }

}
