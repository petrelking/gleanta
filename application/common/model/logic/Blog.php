<?php
/**
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2018/7/25
 * Time: 9:36
 */

namespace app\common\model\logic;

use app\weidoo\library\model\FactoryModel as FModel;
use app\weidoo\library\model\ErrorModel as EModel;

class Blog extends Base
{

    public static $model;

    public static function blogExist($data)
    {
        if (!isset($data['title']) && !isset($data['id'])) {
            EModel::instance()->setError(lang('query not-exist'));
            return false;
        }

        $where = [];

        if (isset($data['title'])) {
            $where[] = ['title', 'like', '%'.$data['title'].'%'];
        }

        if (isset($data['id'])) {
            unset($where);
            $where[] = ['id', '=', $data['id']];
        }

        self::$model = FModel::instance('Blog');
        $res = self::$model->getValue($where,'id');
        return $res > 0 ? 1 : 0;
    }

    public static function blogAdd($data)
    {
        if (!isset($data['title'])) {
            EModel::instance()->setError(lang('blog title empty'));
            return false;
        }

        if (!isset($data['author'])) {
            EModel::instance()->setError(lang('blog author empty'));
            return false;
        }

        if (!isset($data['desc'])) {
            EModel::instance()->setError(lang('blog desc empty'));
            return false;
        }

        self::$model = FModel::instance('Blog');

        $id = self::$model->addInfo($data);

        if (!$id) {
            EModel::instance()->setError(lang('execute error'));
            return false;
        }

        $data['id']=$id;
        return $data;

    }
}