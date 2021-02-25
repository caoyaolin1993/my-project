<?php

declare(strict_types=1);

namespace app\admin\service;

use app\common\cache\AdminUserCache;
use app\common\cache\AdminVerifyCache;
use app\common\service\IpInfoService;
use think\facade\Db;

class AdminLoginService
{
    /**
     * 登录
     *
     * @param [type] $param
     * @return void
     */
    public static function login($param)
    {
        // 处理用户名和密码
        $username = $param['username'];
        $password = md5($param['password']);

        // 定义要查找的字段
        $field = 'admin_user_id,username,nickname,login_num,is_disable';

        // 从数据库查找数据符合的条件
        $where[] = ['username', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        // 数据库查询操作
        $admin_user = Db::name('admin_user')
            ->field($field)
            ->where($where)
            ->find();
        // 如果没有查看则可判断账号或者密码错误
        if (empty($admin_user)) {
            exception('账号或密码错误');
        }

        // 如果查出来的数据中的is_disable为1 表明该账号为禁用状态
        if ($admin_user['is_disable'] == 1) {
            exception('账号已被禁用,请联系管理员');
        }
        // ip 后面会被使用或多次使用 所以把数组中的一个值重新赋值给一个变量
        $request_ip = $param['request_ip'];

        // 传入ip 进行处理  返回这个ip 的相关信息
        $ip_info = IpInfoService::info($request_ip);

        // admin_user_id 后面会被使用或被多次使用 所以把数组中的一个值重新赋值给一个变量
        $admin_user_id = $admin_user['admin_user_id'];

        // 定义一个update变量  向其中放入数据表需要更新的数据   
        $update['login_ip'] = $request_ip;
        $update['login_region'] = $ip_info['region'];
        $update['login_time'] = date('Y-m-d H:i:s');
        $update['login_num'] = $admin_user['login_num'] + 1;

        // 数据库操作将表中数据进行更新    
        Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        // 因为数据表已被更新  所以之前这个数据表中的信息缓存就需要删除   
        AdminUserCache::del($admin_user_id);

        // 拿到当前请求的pathinfo          
        $menu_url = request_pathinfo();

        // 拿到该请求的pathinfo 当成菜单id传入 再查看缓存中的菜单信息  
        // 这个过程既是一个从数据表中拿到信息的过程 也是一个将菜单信息存入缓存的过程
        $admin_menu = AdminMenuService::info($menu_url);

        // 将username 存入request_param 数组的其中的一个键值 这个数组的含义是用来存入日志当中
        $request_param['username'] = $username;

        // 判断从前端传过来的参数中有没有verify_id 如果有 就将关于验证码的2个信息存入$request_param数组中
        if ($param['verify_id']) {
            $request_param['verify_id'] = $param['verify_id'];
            $request_param['verify_code'] = $param['verify_code'];
        }


        // 定义一个日志数组 里面存入相应的信息
        $admin_log['admin_user_id'] = $admin_user_id; // 用户id
        $admin_log['admin_menu_id'] = $admin_menu['admin_menu_id'];  // 菜单id
        $admin_log['log_type']      = 1;    // 自己定义的log类型 1登录 2操作 3退出
        $admin_log['request_ip'] = $request_ip;
        $admin_log['request_method'] = $param['request_method'];  // 请求方法类型
        $admin_log['request_param'] = serialize($request_param);  // 上面所纳入的信息 通过序列化将数组转成字符串 易于保存

        // 通过用户id 从拿到用户信息  如果缓存中没有  则通过查询数据库拿到信息再存入缓存中
        $admin_user = AdminUserService::info($admin_user_id);
        $data['admin_user_id'] = $admin_user_id;   // 用户id
        $data['admin_token'] = $admin_user['admin_token'];  // token


        // 验证码已经使用过 再最后需要删除缓存中存入的验证码的信息
        AdminVerifyCache::del($param['verify_id']);

        // 返回信息
        return $data;
    }

    /**
     * 退出
     *
     * @param [type] $admin_user_id
     * @return void
     */
    public static function logout($admin_user_id)
    {
        // 更新退出的时间到admin_user表中
        $update['logout_time'] = date('Y-m-d H:i:s');

        Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        // 向update数组中添加admin_user_id 以便后续返回
        $update['admin_user_id'] = $admin_user_id;

        // 因为改动了admin_user表中的数据  所以之前缓存过的信息就得删除 删除之后 里面存入的token信息就会没有 该用户的登录信息也就没有了
        AdminUserCache::del($admin_user_id);

        // 返回结果
        return $update;
    }
}
