<?php
/**
 * controller 的公共操作类
 */
namespace app\weidoo\traits;

use think\Request;

trait MasterControl
{
    /**
     * @var string 错误提示
     */
    protected $errMessage = 'Oops! 你好像走丢了';

    /**
     * @var string 成功提示
     */
    protected $sucMessage = 'Oops! 管理员忘记了什么，请联系他吧';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->suc($this->sucMessage);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->suc($this->sucMessage);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        return $this->suc($this->sucMessage);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->suc($this->sucMessage);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        return $this->suc($this->sucMessage);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        return $this->suc($this->sucMessage);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return out_error_json($this->sucMessage);
    }

    /**
     *  空控制器 / 空操作 / 错误路由
     */
    public function _empty()
    {
        return out_error_json($this->errMessage);
    }
}