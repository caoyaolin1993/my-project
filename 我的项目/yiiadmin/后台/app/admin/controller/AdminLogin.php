<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\service\AdminLoginService;
use think\facade\Request;
use app\admin\service\AdminVerifyService;
use app\admin\validate\AdminUserValidate;
use app\admin\validate\AdminVerifyValidate;
use think\facade\Db;

class AdminLogin
{
    /**
     * 验证码
     * 
     * @method GET 
     *
     * @return json
     */
    public function verify()
    {
        $AdminVerifyService = new AdminVerifyService();
        
        $data = $AdminVerifyService->verify();

        return success($data);
    }
    
    /**
     * 登录
     *
     * @return void
     */
    public function login(){
        $param['username'] = Request::param('username/s','');
        $param['password'] = Request::param('password/s','');
        $param['verify_id'] = Request::param('verify_id/s','');
        $param['verify_code'] = Request::param('verify_code/s','');
        // 将后续会用到的信息都存在param数组中
        $param['request_ip'] = Request::ip();
        $param['request_method'] = Request::method();
        // 拿到验证码配置 判断是否需要验证验证码
        $verify_config = AdminVerifyService::config();
        // 如果switch为ture 则验证验证码
        if($verify_config['switch']){
            // validate(AdminVerifyValidate::class)->scene('check')->check($param);
        }
        // 验证前端传过来的字段
        // validate(AdminUserValidate::class)->scene('user_login')->check($param);
        // 对传过来的字段进行逻辑处理
        $data = AdminLoginService::login($param);

        // 返回最后的结果
        return success($data,'登陆成功');
    }

    /**
     * 退出
     *
     * @return void
     */
    public function logout()
    {
        // 拿到前端header头传过来的admin_user_id
        $param['admin_user_id'] = admin_user_id();

        //  验证拿到的admin_user_id是否符合要求  1是否为空   2用户是否在数据表中
        validate(AdminUserValidate::class)->scene('user_id')->check($param);

        $data  = AdminLoginService::logout($param['admin_user_id']);

        return success($data,'退出成功');
    }
}                                                                           

