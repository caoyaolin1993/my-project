<?php

declare(strict_types=1);

namespace app\middleware;

use app\util\AuthFilter;
use app\util\ReturnCode;
use think\facade\Db;

class AdminRoleAuth 
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //
        $token = $request->param('token');
        $pa =  $request->pathinfo();
        $path = 'admin/' . $pa;

        $AuthFilter = new AuthFilter();
        $auth_list = $AuthFilter->AuthFilter;
        if (in_array($path, $auth_list)) {
            //路由无需认证
            return;
        }

        $admin = Db::name('admin')->where('token', $token)->field('id,role_id')->find();

        if (!$this->checkAuth($path, $admin['role_id'])) {
            $data = ['code' => ReturnCode::NO_AUTH, 'msg' => '对不起，您没有权限进行操作！', 'data' => []];
            return json($data);
        }

        return $next($request);
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
