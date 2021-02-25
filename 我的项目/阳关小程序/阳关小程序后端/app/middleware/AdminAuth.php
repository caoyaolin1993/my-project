<?php

declare(strict_types=1);

namespace app\middleware;

use app\util\ReturnCode;
use think\facade\Db;

class AdminAuth
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

        if (empty($token)) {
            $data = ['code' => ReturnCode::NO_LOGIN, 'msg' => '账号未登陆', 'data' => []];

            return json($data);
        }
        //检测用户账号是否存在
        $row = Db::name('admin')->where('token', $token)->find();
        if (!$row) {
            $data = ['code' => ReturnCode::STAFF_NO_EXISTS, 'msg' => '用户不存在', 'data' => []];
            return json($data);
        }
        if ($row['status'] != 1) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '账户已删除或账户已停用'];
            return json($data);
        }
        //检测登陆是否过期
        if ($row['expire_time'] < time()) {
            $data = ['code' => ReturnCode::NO_LOGIN, 'msg' => '登陆已过期，请重新登陆！', 'data' => []];
            return json($data);
        }
        //修改token时间
        $expire_time = time() + 12 * 60 * 60;
        Db::name('admin')->where('token', $token)->update(['expire_time' => $expire_time]);

        return $next($request);
    }
}
