<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\util\AuthFilter;
use app\util\ReturnCode;
use think\facade\Db;

class AdminAuth
{
    protected $token;

    public function __construct()
    {
        $token = request()->param('token');
        $this->token = $token;

        //验证是否登录
        $this->checkLogin();

        //路由权限验证
        $this->checkRoute();
    }


    public function checkLogin()
    {
        if (empty($this->token)) {
            $data = ['code' => ReturnCode::NO_LOGIN, 'msg' => '账号未登陆', 'data' => []];
            return json($data);
        }

        //检测用户账号是否存在
        $row = Db::name('admin')->where('token', $this->token)->find();
        if (!$row) {
            return_msg(ReturnCode::STAFF_NO_EXISTS,'用户不存在');
        }
        if ($row['status'] != 1) {
            return_msg(ReturnCode::INVALID,'账户已删除或账户已停用');
        }
        //检测登陆是否过期
        if ($row['expire_time'] < time()) {
            return_msg(ReturnCode::NO_LOGIN,'登陆已过期，请重新登陆！');
        }
        //修改token时间
        $expire_time = time() + 12 * 60 * 60;
        Db::name('admin')->where('token', $this->token)->update(['expire_time' => $expire_time]);
    }

    public function checkRoute()
    {
        $path = 'admin/'.request()->pathinfo();

        $AuthFilter = new AuthFilter();
        $auth_list = $AuthFilter->AuthFilter;
        $auth_list = $AuthFilter->AuthFilter;
        if (in_array($path, $auth_list)) {
            //路由无需认证
            return;
        }

        $admin = Db::name('admin')->where('token', $this->token)->field('id,role_id')->find();

        if (!$this->checkAuth($path, $admin['role_id'])) {
            return_msg(ReturnCode::NO_AUTH,'对不起，您没有权限进行操作！');
        }

    }

     /**
     * 检测用户权限   
     */
    private function checkAuth($route, $role_id)
    {
        //根据路由获取权限id
        $row =  Db::name('auth_rule')->where('rule', $route)->field('id')->find();
        if (!$row) {
            return [];
        }
        $ruleArr = $this->getAuth($role_id);

        return in_array($row['id'], $ruleArr);
    }

    /**
     * 获取用户权限
     */
    private function getAuth($role_id)
    {
        $auth =  Db::name('auth_group')->where('id', $role_id)->field('rules')->find();
        if($auth['rules']){
            $ruleArr = explode(',', $auth['rules']);
            return $ruleArr;
        }else {
            return [];
        }
    }
}
