<?php

namespace app\index\controller;

use think\Request;
use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Blog extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //实例化Blog模型
        self::$service = FModel::instance('Blog');

        //添加博客
        $success = self::$service->blogAdd(self::$validateData);

        //响应
        return $success === false ? out_error_json(EModel::instance()->getError()) : out_success_json(lang('execute success'), $success);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    public static function getValidate($action){

        switch ($action){
            case 'save':
                return [
                    'title' => input('post.title'),
                    'author' => input('post.author'),
                    'desc' => input('post.desc'),
                ];
            default:
                return [];
        }
    }
}
