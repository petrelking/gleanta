<?php
/**
 * Created by WanDeHua.
 * User: WanDeHua
 * Email:271920545@qq.com
 * Date: 2018/7/20
 * Time: 14:16
 */
////域名路由 - 开启跨域请求
Route::domain('saas.api', function () {
    Route::resource('blog', 'index/Blog');
    Route::resource('index','index/index');
    //员工模块
    Route::rule('/user/searchMobile','index/Business/searchMobile');
    Route::rule('/user/saveLogin','index/business/saveLogin');
    Route::rule('/user/updatePwd','index/business/updatePwd');
    Route::rule('/user/outLogin','index/business/outLogin');
    Route::rule('/user/userList','index/business/userList');
    Route::rule('/user/upUserStatus','index/business/upUserStatus');
    Route::rule('/user/addUser','index/business/addUser');
    //部门
    Route::rule('/dept/lists','index/dept/lists');
    Route::rule('/dept/add','index/dept/addData');
    Route::rule('/dept/edit','index/dept/editData');
    Route::rule('/dept/one','index/dept/one');
    //购物车
    Route::rule('/cart/lists','index/cart/lists');
    Route::rule('/cart/add','index/cart/addData');
    Route::rule('/cart/edit','index/cart/editData');
    Route::rule('/cart/del','index/cart/delData');
    Route::rule('/cart/addFollow','index/cart/addFollow');
    Route::rule('/cart/detail','index/cart/getDetail');

    Route::rule('/Deposit/add','index/Deposit/addData');

    Route::rule('/customer/add','index/customer/addData');
    //添加订单
    Route::rule('/order/add','index/order/saveOrder'); //保存订单
    Route::rule('/order/lists','index/order/lists'); //订单列表
    Route::rule('/order/detail','index/order/detail'); //订单详情
    Route::rule('/order/cancel','index/order/cancel'); //取消订单
    Route::rule('/achie/add','index/UserAchie/addAchie'); //添加业绩分配
    Route::rule('/achie/lists','index/UserAchie/lists'); //业绩分配列表
    //收银
    Route::rule('/payment/getPaymentsList','payment/index/getPaymentsList');
    Route::rule('/payment/payByType','payment/index/payByType');
    Route::rule('/payment/getPayHis','payment/index/getPayHistory');
    Route::rule('/payment/biz/setpay','payment/index/setBizPayType');
    Route::rule('/payment/add','payment/index/addPayment');
    Route::rule('/payment/del','payment/index/delPayment');
    Route::rule('/payment/edt','payment/index/edtPayment');
    Route::rule('/payment/get','payment/index/getPayment');
    Route::rule('/payment/chk','payment/index/chkBizPayType');

    //客户
    Route::rule('/customer/lists',"index/Customer/lists"); //客户列表



    //服务
    //分类
    Route::rule('/servicecategory/lists', 'index/servicecategory/lists');
    Route::rule('/servicecategory/add', 'index/servicecategory/add');
    Route::rule('/servicecategory/edit', 'index/servicecategory/edits');
    Route::rule('/servicecategory/del', 'index/servicecategory/del');
    //项目
    Route::rule('/serviceitem/lists', 'index/serviceitem/lists');
    Route::rule('/serviceitem/servicelists', 'index/serviceitem/serviceLists');
    Route::rule('/serviceitem/cartlists', 'index/serviceitem/cartLists');
    Route::rule('/serviceitem/ctylists', 'index/serviceitem/ctyLists');
    Route::rule('/serviceitem/add', 'index/serviceitem/add');
    Route::rule('/serviceitem/edit', 'index/serviceitem/edits');
    Route::rule('/serviceitem/del', 'index/serviceitem/del');
    //产品
    Route::rule('/serviceproduct/lists', 'index/serviceproduct/lists');
    Route::rule('/serviceproduct/ctylists', 'index/serviceproduct/ctyLists');
    Route::rule('/serviceproduct/add', 'index/serviceproduct/add');
    Route::rule('/serviceproduct/edit', 'index/serviceproduct/edits');
    Route::rule('/serviceproduct/del', 'index/serviceproduct/del');
    //套餐
    Route::rule('/servicepackage/lists', 'index/servicepackage/lists');
    Route::rule('/servicepackage/cartlists', 'index/servicepackage/cartLists');
    Route::rule('/servicepackage/ctylists', 'index/servicepackage/ctyLists');
    Route::rule('/servicepackage/vfycontent', 'index/servicepackage/verifyContent');
    Route::rule('/servicepackage/add', 'index/servicepackage/add');
    Route::rule('/servicepackage/edit', 'index/servicepackage/edits');
    Route::rule('/servicepackage/del', 'index/servicepackage/del');
    //卡
    Route::rule('/servicecard/lists', 'index/servicecard/lists');
    Route::rule('/servicecard/ctylists', 'index/servicecard/ctylists');
    Route::rule('/servicecard/vfycontent', 'index/servicecard/verifyContent');
    Route::rule('/servicecard/vfyhandsel', 'index/servicecard/verifyHandsel');
    Route::rule('/servicecard/vfychandsel', 'index/servicecard/verifyChandsel');
    Route::rule('/servicecard/add', 'index/servicecard/add');
    Route::rule('/servicecard/edit', 'index/servicecard/edits');
    Route::rule('/servicecard/del', 'index/servicecard/del');
    //券
    Route::rule('/serviceticket/lists', 'index/serviceticket/lists');
    Route::rule('/serviceticket/ctylists', 'index/serviceticket/ctylists');
    Route::rule('/serviceticket/vfycontent', 'index/serviceticket/verifyContent');
    Route::rule('/serviceticket/add', 'index/serviceticket/add');
    Route::rule('/serviceticket/edit', 'index/serviceticket/edits');
    Route::rule('/serviceticket/del', 'index/serviceticket/del');

    //权限
    //规则
    Route::rule('/auth/lists', 'index/auth/lists');
    Route::rule('/auth/ctylists', 'index/auth/ctyLists');
    Route::rule('/auth/ownper', 'index/auth/ownPer');
    Route::rule('/auth/add', 'index/auth/add');
    Route::rule('/auth/edit', 'index/auth/edits');
    Route::rule('/auth/del', 'index/auth/del');
    //角色
    Route::rule('/authrole/ownrole', 'index/authrole/ownRole');
    Route::rule('/authrole/perbyrole', 'index/authrole/perByRole');
    Route::rule('/authrole/add', 'index/authrole/add');
    Route::rule('/authrole/edit', 'index/authrole/edits');
    Route::rule('/authrole/del', 'index/authrole/del');
    //用户
    Route::rule('/authuser/ownuser', 'index/authuser/ownUser');
    Route::rule('/authuser/rolebyuser', 'index/authuser/roleByUser');
    Route::rule('/authuser/add', 'index/authuser/add');
    Route::rule('/authuser/edit', 'index/authuser/edits');
    Route::rule('/authuser/del', 'index/authuser/del');

    Route::get('formToken',function (\think\Request $request){
        $config = \think\facade\Config::get('api.');
        $token = $request->token($config['csrf_name']);

        /**
         * 如果是跨域，还得在rul设置参数 _ajax, 为了不设置这个参数，这里直接输出token的值，
         * 不然前端在header中可以获取到token，直接exit，不用输出token
         */
        echo $token;
        exit;
    });     //获取 formToken
    Route::miss('index/Error/miss');    //MISS路由

})->allowCrossDomain();

