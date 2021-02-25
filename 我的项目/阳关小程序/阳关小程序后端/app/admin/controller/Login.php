<?php
declare (strict_types = 1);
namespace app\admin\controller;

use app\BaseController;
use Config;
use think\Request;
use app\util\ReturnCode;
use think\facade\Db;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class Login extends BaseController
{

    //账号登陆 
    public function login()
    {
        $account = input('post.account'); //传过来的昵称
        $password = input('post.password'); //传过来的密码
        if (empty($account) || empty($password)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数', 'data' => []];
            return json($data);
        }
        $row = Db::name('admin')
            ->where('account', $account)
            ->find();  //通过账号在admin表中查找对应的一条数据
        if (empty($row)) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '账户不存在'];
            return json($data);
        }
        if ($row['status'] != 1) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '账户已删除或账户已停用'];
            return json($data);
        }
        $psw = md5(md5($password . $row['salt']));  //密码加密原则
        if ($psw != $row['password']) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '密码错误'];
            return json($data);
        }
        $token = createToken($row['id']); //创建唯一token
        $expire_time = time() + 12 * 60 * 60; //12小时过期
        Db::name('admin')
            ->where('id', $row['id'])
            ->update(['last_login_time' => date('Y-m-d H:i:s', time()), 'token' => $token, 'expire_time' => $expire_time]);  // 当该账号登录时 修改最后一次登录时间 token 过期时间  更新时间自动更新

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'res' => ['token'=>$token,'admin_id' => $row['id'], 'nickname' => $row['account'], 'auth' => $row['role']]];  // 返回token 昵称 和权限名称
        return json($data);
    }

    //退出登陆
    public function logout()
    {
        $token = input('post.token');
        Db::name('admin')->where('token', $token)->update(['token' => '']);

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '退出成功'];
        return json($data);
    }

    public function index()
    {
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '请求地址错误'];
        return json($data);
    }
}
