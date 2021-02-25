<?php

declare(strict_types=1);
/**
 * 个人中心
 */

namespace app\admin\service;

use app\common\cache\AdminUserCache;
use think\facade\Db;
use think\facade\Filesystem;

class AdminMyService
{

    /**
     * 我的信息
     *
     * @param [type] $admin_user_id 用户id
     * @return void
     */
    public static function info($admin_user_id)
    {
        // 通过用户id拿到用户的所有信息
        $admin_user  = AdminUserService::info($admin_user_id);

        // 将用户中所需要的信息存入到一个数组中返回
        $data['admin_user_id'] = $admin_user['admin_user_id'];   // 用户id
        $data['avatar'] = $admin_user['avatar'];   // 用户头像
        $data['username'] = $admin_user['username'];  // 用户名
        $data['nickname']  = $admin_user['nickname'];  // 用户昵称
        $data['email'] = $admin_user['email'];   // 用户邮箱
        $data['create_time'] = $admin_user['create_time']; // 创建时间
        $data['update_time'] = $admin_user['update_time'];  // 更新时间
        $data['login_time'] = $admin_user['login_time'];  // 登录时间
        $data['logout_time'] = $admin_user['logout_time'];  // 退出时间
        $data['is_delete'] = $admin_user['is_delete'];  // 是否删除
        $data['roles'] = $admin_user['roles'];   // 权限

        // 返回信息
        return $data;
    }

    /**
     * 修改信息
     *
     * @param [type] $param    用户信息
     * @param string $method
     * @return void
     */
    public static function edit($param, $method = 'get')
    {
        // 将传过来的信息赋值给一个见名知意的变量
        $admin_user_id = $param['admin_user_id'];

        // 如果是get方式 则是刷新的信息  否则是提交修改
        if ($method = 'get') {
            // 通过用户id 拿到我的信息
            $admin_user = self::info($admin_user_id);

            // 将需要返回的信息存入$data变量中
            $data['admin_user_id'] = $admin_user['admin_user_id'];  // 用户id
            $data['username'] = $admin_user['username'];  // 用户名
            $data['nickname'] = $admin_user['nickname'];  // 昵称
            $data['email'] = $admin_user['email'];   // 邮箱
            $data['is_delete'] = $admin_user['is_delete']; // 是否删除
            return $data;
        } else {
            //  修改
            // 去掉数组中admin_user_id 加入 update_time 在更新数据
            unset($param['admin_user_id']);
            $param['update_time'] = date('Y-m-d H:i:s');

            $res = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->update($param);

            // 如果失败 则中断
            if (empty($res)) {
                exception();
            }

            // 再数组中加入admin_user_id
            $param['admin_user_id'] = $admin_user_id;

            // 因为永不数据有了修改 所以需要更新缓存中的数据
            AdminUserCache::upd($admin_user_id);
            return $param;
        }
    }

    /**
     * 修改密码
     *
     * @param [type] $param 用户密码
     * @return void
     */
    public static function pwd($param)
    {
        // 用户id
        $admin_user_id = $param['admin_user_id'];
        // 旧密码
        $password_old = $param['password_old'];
        // 新密码
        $password_new = $param['password_new'];

        // 通过用户id拿到用户所有信息
        $admin_user = AdminUserService::info($admin_user_id);

        // 判断旧密码是否正确
        if (md5($password_old) != $admin_user['password']) {
            exception('旧密码错误');
        }

        // 将需要更新的数据存入数组中进行更新
        $update['password'] = md5($password_new);   // 密码
        $update['update_time'] = date('Y-m-d H:i:s');   // 更新时间

        // 操作数据库
        $res = Db::name('admin_user')
            ->where('admin_user_id',$admin_user_id)
            ->update($update);

        // 如果更新错误则抛出异常
        if (empty($res)) {
            exception();
        }

        // 添加要返回的信息到数组中
        $update['admin_user_id'] = $admin_user_id;  // 用户id
        $update['password'] = $res;


        // 因为信息发生了改变 所以缓存中存入的数据也需要更新
        AdminUserCache::upd($admin_user_id);

        // 返回给前端数据
        return $update;
    }

    /**
     * 更换头像
     *
     * @param [type] $param 头像信息
     * @return void
     */
    public static function avatar($param)
    {
        // 用户id
        $admin_user_id = $param['admin_user_id'];
        // 用户头像
        $avatar = $param['avatar'];


        // 上传图片到服务器 为了能够直接访问 放在public目录下  并且用闭包函数自定义了文件名
        // 配置文件中已经定义好了 对外可见的目录       admin_user/3/3_avatar.jpg
         // 磁盘类型
        //  'type'       => 'local',
         // 磁盘路径
        //  'root'       => app()->getRootPath() . 'public/storage',
        $avatar_name = Filesystem::disk('public')
            ->putFile('admin_user',$avatar,function() use ($admin_user_id){
                return $admin_user_id.'/'.$admin_user_id.'_avatar';
            });

        // 给存储的图片路径带个get参数 通过当前时间拼接 这样在访问这个图片的地址时可以知道这张图片是什么时候上传的
        // 存储的时候就需要手动将对外的完整地址拼接好
        $update['avatar'] = 'storage/'.$avatar_name.'?t='.date('YmdHis');

        // 更新时间
        $update['update_time'] = date('Y-m-d H:i:s');

        // 操作数据库 更新数据
        $res = Db::name('admin_user')
        ->where('admin_user_id',$admin_user_id)
        ->update($update);

        // 数据库操作可能会失败 如果失败了 抛出异常
        if(empty($res)){
            exception();
        }

        // 因为用户信息已经发生了改变 所以需要更新缓存里面的信息
        AdminUserCache::upd($admin_user_id);

        // 重新拿到用户的所有信息 再将需要返回的信息放到一个数组中返回
        $admin_user = AdminUserService::info($admin_user_id);

        $data['admin_user_id'] = $admin_user['admin_user_id'];  // 用户id
        $data['update_time'] = $admin_user['update_time'];   // 更新时间
        $data['avatar'] = $admin_user['avatar'];  // 用户头像
        return $data;
    }


    /**
     * 我的日志
     *
     * @param array $where  条件
     * @param integer $page 页数
     * @param integer $limit    数量
     * @param array $order  排序
     * @param string $field 字段
     * @return void
     */
    public static function log($where=[],$page=1,$limit=10,$order=[],$field='')
    {
        // 将条件传入 专门处理log的服务层 进行处理
        $data = AdminLogService::list($where,$page,$limit,$order,$field);
        return $data;
    }











}
