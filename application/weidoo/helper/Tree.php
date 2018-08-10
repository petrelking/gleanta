<?php
namespace app\weidoo\helper;

/**
 * 通用的树型类
 */
class Tree
{

    protected static $instance;

    private function __construct()
    {
    }

    /**
     * 生成树型结构所需要的2维数组
     * @var array
     */
    public $arr = [];

    /**
     * 生成树型结构所需修饰符号，可以换成图片
     * @var array
     */
    public $icon = ['│', '├', '└'];
    public $nbsp = "&nbsp;";
    public $pk = 'id';
    public $pidname = 'pid';

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Tree
     */
//    public static function instance()
//    {
//        if (is_null(self::$instance)) {
//            self::$instance = new static();
//        }
//
//        return self::$instance;
//    }
    /**
     * @return Tree
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 初始化方法
     * @param array 2维数组，例如：
     * [
     * 1 => ['id'=>'1','pid'=>0,'name'=>'一级栏目一'],
     * 2 => ['id'=>'2','pid'=>0,'name'=>'一级栏目二'],
     * 3 => ['id'=>'3','pid'=>1,'name'=>'二级栏目一'],
     * 4 => ['id'=>'4','pid'=>1,'name'=>'二级栏目二'],
     * 5 => ['id'=>'5','pid'=>2,'name'=>'二级栏目三'],
     * 6 => ['id'=>'6','pid'=>3,'name'=>'三级栏目一'],
     * 7 => ['id'=>'7','pid'=>3,'name'=>'三级栏目二'],
     * ]
     * @param $pk
     * @param $pidname
     * @param $nbsp
     * @return $this
     */
    public function init($arr = [], $pk = NULL, $pidname = NULL, $nbsp = NULL)
    {
        $this->arr = $arr;
        if (!is_null($pk))
            $this->pk = $pk;
        if (!is_null($pidname))
            $this->pidname = $pidname;
        if (!is_null($nbsp))
            $this->nbsp = $nbsp;
        return $this;
    }

    /**
     * 得到子级数组
     * @param int
     * @return array
     */
    public function getChild($myid)
    {
        $newarr = [];
        foreach ($this->arr as $value) {
            if (!isset($value[$this->pk]))
                continue;
            if ($value[$this->pidname] == $myid)
                $newarr[$value[$this->pk]] = $value;
        }
        return $newarr;
    }

    /**
     * 读取指定节点的所有孩子节点
     * @param int $myid 节点ID
     * @param boolean $withself 是否包含自身
     * @return array
     */
    public function getChildren($myid, $withself = FALSE)
    {
        $newarr = [];
        foreach ($this->arr as $value) {
            if (!isset($value[$this->pk]))
                continue;
            if ($value[$this->pidname] == $myid) {
                $newarr[] = $value;
                $newarr   = array_merge($newarr, $this->getChildren($value[$this->pk]));
            } else if ($withself && $value[$this->pk] == $myid) {
                $newarr[] = $value;
            }
        }
        return $newarr;
    }

    /**
     * 读取指定节点的所有孩子节点ID
     * @param int $myid 节点ID
     * @param boolean $withself 是否包含自身
     * @return array
     */
    public function getChildrenIds($myid, $withself = FALSE)
    {
        $childrenlist = $this->getChildren($myid, $withself);
        $childrenids  = [];
        foreach ($childrenlist as $k => $v) {
            $childrenids[] = $v[$this->pk];
        }
        return $childrenids;
    }

    /**
     * 得到当前位置父辈数组
     * @param int
     * @return array
     */
    public function getParent($myid)
    {
        $pid    = 0;
        $newarr = [];
        foreach ($this->arr as $value) {
            if (!isset($value[$this->pk]))
                continue;
            if ($value[$this->pk] == $myid) {
                $pid = $value[$this->pidname];
                break;
            }
        }
        if ($pid) {
            foreach ($this->arr as $value) {
                if ($value[$this->pk] == $pid) {
                    $newarr[] = $value;
                    break;
                }
            }
        }
        return $newarr;
    }

    /**
     * 得到当前位置所有父辈数组
     * @param int
     * @return array
     */
    public function getParents($myid, $withself = FALSE)
    {
        $pid    = 0;
        $newarr = [];
        foreach ($this->arr as $value) {
            if (!isset($value[$this->pk]))
                continue;
            if ($value[$this->pk] == $myid) {
                if ($withself) {
                    $newarr[] = $value;
                }
                $pid = $value[$this->pidname];
                break;
            }
        }
        if ($pid) {
            $arr    = $this->getParents($pid, TRUE);
            $newarr = array_merge($arr, $newarr);
        }
        return $newarr;
    }

    /**
     * 读取指定节点所有父类节点ID
     * @param int $myid
     * @param boolean $withself
     * @return array
     */
    public function getParentsIds($myid, $withself = FALSE)
    {
        $parentlist = $this->getParents($myid, $withself);
        $parentsids = [];
        foreach ($parentlist as $k => $v) {
            $parentsids[] = $v[$this->pk];
        }
        return $parentsids;
    }

    /**
     *
     * 获取树状数组
     * @param string $myid 要查询的ID
     * @param string $itemprefix 前缀
     * @return string
     */
    public function getTreeArray($myid, $itemprefix = '')
    {
        $childs = $this->getChild($myid);
        $n      = 0;
        $data   = [];
        $number = 1;
        if ($childs) {
            $total = count($childs);
            foreach ($childs as $id => $value) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                    $k = $itemprefix ? $this->nbsp : '';
                } else {
                    $j .= $this->icon[1];
                    $k = $itemprefix ? $this->icon[0] : '';
                }
                $spacer                = $itemprefix ? $itemprefix . $j : '';
                $value['spacer']       = $spacer;
                $data[$n]              = $value;
                $data[$n]['childlist'] = $this->getTreeArray($id, $itemprefix . $k . $this->nbsp);
                $n++;
                $number++;
            }
        }
        return $data;
    }

    /**
     * 将getTreeArray的结果返回为二维数组
     * @param array $data
     * @return array
     */
    public function getTreeList($data = [], $field = 'name')
    {
        $arr = [];
        foreach ($data as $k => $v) {
            $childlist = isset($v['childlist']) ? $v['childlist'] : [];
            unset($v['childlist']);
            $v[$field]     = $v['spacer'] . ' ' . $v[$field];
            $v['haschild'] = $childlist ? 1 : 0;
            if ($v[$this->pk])
                $arr[] = $v;
            if ($childlist) {
                $arr = array_merge($arr, $this->getTreeList($childlist, $field));
            }
        }
        return $arr;
    }

}
