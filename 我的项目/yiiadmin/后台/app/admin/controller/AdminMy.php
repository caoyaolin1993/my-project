<?php

declare(strict_types=1);
/**
 * 个人中心
 */

namespace app\admin\controller;

use app\admin\service\AdminMenuService;
use app\admin\service\AdminMyService;
use app\admin\validate\AdminMyValidate;
use think\facade\Request;

class AdminMy
{
    /**                 
     * 我的信息                     
     *                      
     * @return void                     
     */
    public function myinfo()
    {
        // 获取前端穿过来的参数 并通过/d 强转为整形 给个''为默认值              
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        // 验证穿过来的值是否符合要求 1是否存在  2数据库是否有值 且 未被删除                                
        validate(AdminMyValidate::class)->scene('user_id')->check($param);

        // 通过id拿到我的信息                                                       
        $data = AdminMyService::info($param['admin_user_id']);

        // 判断该账号是否被删除
        if ($data['is_delete'] == 1) {
            exception('账号信息错误,请重新登录!');
        }

        // 返回结果
        return success($data);
    }

    /**
     * 修改信息
     *
     * @return void
     */
    public function myEdit()
    {
        // 拿到前端传过来的id 强转为整型 给个默认值
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        // 如果是get请求  则是用户点击了刷新 否则是修改                                                                        
        if (Request::isGet()) {
            // 对传过来的字段进行验证                                     
            validate(AdminMyValidate::class)->scene('user_id')->check($param);
            // 传入一个参数 获取数据                                  
            $data = AdminMyService::edit($param);
            // 判断该数据是否已删除       
            if ($data['is_delete'] == 1) {
                exception('账号信息错误，请重新登录！');
            }
        } else {
            // 获取前端传过来的值 强转类型并赋默认值 
            $param['username'] = Request::param('username/s', '');   // 用户名
            $param['nickname'] = Request::param('nickname/s', '');  // 昵称
            $param['email'] = Request::param('email/s', '');  // 邮箱  

            // 对传过来的值进行验证 
            validate(AdminMyValidate::class)->scene('my_edit')->check($param);

            // 传入2个参数 表明是修改
            $data = AdminMyService::edit($param, 'post');
        }
        return success($data);
    }

    /**
     * 修改密码
     *
     * @return void
     */
    public function myPwd()
    {
        // 获取前端数据强转对应类型并赋给默认值
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['password_old'] = Request::param('password_old/s', '');
        $param['password_new'] = Request::param('password_new/s', '');

        // 对传过来的字段进行验证
        validate(AdminMyValidate::class)->scene('my_pwd')->check($param);

        // 修改密码
        $data = AdminMyService::pwd($param);

        return success($data);
    }

    /**
     * 更换头像
     *
     * @return void
     */
    public function myAvatar()
    {
        // 拿到前端传过来的数据 强转类型并赋默认值
        // 用户id
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        // 用户头像
        $param['avatar'] = Request::file('avatar_file');

        // 验证前端传过来的字段是否符合要求
        validate(AdminMyValidate::class)->scene('my_avatar')->check($param);

        // 更换头像
        $data = AdminMyService::avatar($param);
        return success($data);
    }

    /**
     * 我的日志
     *
     * @return void
     */
    public function myLog()
    {
        // 拿到前端传过来的数据 强转类型 给默认值
        $page = Request::param('page/d', 1);  // 页数
        $limit = Request::param('limit/d', 10); // 每页条数
        $log_type = Request::param('log_type/d', '');  // 日志类型
        $sort_field = Request::param('sort_field/s', ''); // 排序字段    
        $sort_type = Request::param('sort_type/s', '');  // 排序类型 asc desc
        $request_keyword = Request::param('request_keyword/s', ''); // 请求ip
        $menu_keyword = Request::param('menu_keyword/s', '');  // 菜单链接 或 菜单名称
        $create_time = Request::param('create_time/a', []);  // 请求时间

        // 通过header头拿到登录用户的id
        $admin_user_id = admin_user_id();

        // 验证id有效性
        validate(AdminMyValidate::class)->scene('user_id')->check(['admin_user_id' => $admin_user_id]);

        // mysql where 条件
        $where = [];
        // 用户id
        $where[] = ['admin_user_id', '=', $admin_user_id];
        // 判断类型是否存在 存在则加入where条件
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }

        // 判断请求ip/地区/ISP是否存在 存在则加入where条件
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }

        // 判断是否存在 菜单链接或菜单名称 存在则加入where条件
        if ($menu_keyword) {
            // 从数据库中拿到符合条件的所有的值  
            $admin_menu = AdminMenuService::likeQuery($menu_keyword);
            // 从$admin_menu数组中单独拿出admin_menu_id这个键的所有值的集合 封装到一个新的数组
            $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
            // 加入where条件中
            $where[] = ['admin_user_id', 'in', $admin_menu_ids];
        }

        // 判断请求时间是否存在 如果存在则加入where条件中
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . '00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . '23:59:59'];
        }

        // mysql排序
        // 定义一个排序数组 如果前端有值 就使用传过来的 如果没有则直接为空 后续会判断是否为空 如果为空则会给默认值

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_type => $sort_type];
        }

        // 根据条件从数据库中取出数据
        $data = AdminMyService::log($where, $page, $limit, $order);

        // 返回前端信息
        return success($data);
    }
}
