<?php

declare(strict_types=1);

namespace app\admin\service;

use app\common\cache\AdminUserCache;
use think\facade\Config;
use think\facade\Db;

class AdminUserService
{
    /**
     * 用户信息
     *
     * @param [type] $admin_user_id  用户id
     * @return void
     */
    public static function info($admin_user_id)
    {
        // 从缓存中拿到这个admin_user_id 的用户信息
        $admin_user = AdminUserCache::get($admin_user_id);

        // 如果缓存中没有这个id的用户信息时
        if (empty($admin_user)) {
            // 在数据表中拿到该id的用户信息
            $admin_user = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->find();

            // 如果没有查到  则表明这个用户不存在
            if (empty($admin_user)) {
                exception('用户不存在' . $admin_user_id);
            }

            // 对这个表中查到的图像的地址进行进一步的处理
            $admin_user['avatar'] = file_url($admin_user['avatar']);

            // 判断该用户是否为后台配置的管理员
            if (admin_is_admin($admin_user_id)) {
                // 如果是管理员则拿到全部的菜单id和菜单url
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->where('is_disable', 0)
                    ->select()
                    ->toArray();
                // 从记录集中分别取出admin_menu_id列 和 menu_url列
                // 这样就可以拿到这个用户所拥有的菜单id集合  和  菜单名称集合
                $menu_ids = array_column($admin_menu,'admin_menu_id');
                $menu_url = array_column($admin_menu,'menu_url');

                // 因为 菜单id 是唯一递增的 不可能有空的 而 菜单url 是允许存在空值的 所以需要对菜单url集合进行处理 去除掉里面的空值
                $menu_url = array_filter($menu_url);
            }elseif($admin_user['is_admin'] == 1){
                // 判断从数据库中查看的字段is_admin是否=1  如果=1 则说明该用户是管理原 则需拿到全部的菜单id和菜单url
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->where('is_disable', 0)
                    ->select()
                    ->toArray();
                // 从记录集中分别取出admin_menu_id列 和 menu_url列
                // 这样就可以拿到这个用户所拥有的菜单id集合  和  菜单名称集合
                $menu_ids = array_column($admin_menu,'admin_menu_id');
                $menu_url = array_column($admin_menu,'menu_url');

                // 因为 菜单id 是唯一递增的 不可能有空的 而 菜单url 是允许存在空值的 所以需要对菜单url集合进行处理 去除掉里面的空值
                $menu_url = array_filter($menu_url);
            }else{
                // 如果不是管理员则不能拿到全部的权限 这时需要判断该用户是哪一个角色 然后通过角色表中的数据得到该用户所拥有的菜单权限
                // 在角色表中查到该用户所拥有的角色id的 菜单id
                $menu_ids = Db::name('admin_role')
                // ->field('admin_role_id')
                ->where('admin_role_id','in',$admin_user['admin_role_ids'])
                ->where('is_delete',0)
                ->where('is_disable',0)
                ->column('admin_menu_ids');

                //  将该用户自带的菜单id也纳入到 所有菜单id的数组中
                // 因为$menu本身是一个数组 $menu_ids[] 这个是向menu_ids数组中后面追加一个自动生成的数字为键的值
                $menu_ids[]  = $admin_user['menu_ids'];

                // 将menu_ids数组 整合成一个字符串 用逗号拼接  这样就可以把不同角色带的菜单id字符串和角色本身自带的菜单id字符串 拼接在一起
                $menu_ids_str = implode(',',$menu_ids);

                // 再通过explode函数将这一串字符串转换成数组 这样就能拿到全部的菜单id的数组集合
                $menu_ids_arr = explode(',',$menu_ids_str);
                // 因为各个角色和用户本身所拥有的菜单id可能会存在重复的菜单id 所以需要去重
                $menu_ids = array_unique($menu_ids_arr);

                // 为了以防万一 通过array_filter()函数 过滤去除可能存在的空值
                $menu_ids = array_filter($menu_ids);

                // 上面已经找到 该角色用户所拥有的菜单id 
                // 现在通过所拥有的菜单id 在数据表中找到对应的菜单名称和表中 不需要权限就能访问的菜单名称

                // 满足第一个条件
                $where[] = ['admin_menu_id','in',$menu_ids]; //所拥有的菜单名称
                $where[] = ['is_delete','=',0];  // 未被删除
                $where[] = ['is_disable','=',0]; // 未被禁用
                $where[] = ['menu_url','<>',''];  // 值不为空

                // 或者满足第二个条件 未被删除 未被禁用 值不为空  是无需权限就可以访问的
                $where_un[] = ['is_delete','=',0];
                $where_un[] = ['is_disable','=',0];
                $where_un[] = ['menu_url','<>',''];
                $where_un[] = ['is_unauth','=',1];

                // column() 函数  查询某一列的值
                $menu_url = Db::name('admin_menu')
                // ->field('menu_url')
                ->whereOr([$where,$where_un])
                ->column('menu_url');
            }

            // 将用户的角色id 赋值给一个变量
            $admin_user['admin_role_ids'] = '1';
            $admin_role_ids = $admin_user['admin_role_ids'];

            // 如果这个角色所拥有的角色id为空 则将该用户所拥有的角色id设置为[]
            if (empty($admin_role_ids)) {
                $admin_role_ids = [];
            }else{
                // 将admin_role_ids转成数组
                $admin_role_ids = explode(',',$admin_user['admin_role_ids']);

                // 将数组中的值强转成int类型
                foreach ($admin_role_ids as $k => $v) {
                    $admin_role_ids[$k] = (int)$v;
                }
            }

            // 将admin_menu_ids赋值给一个变量  转成数组 如果有值则强转为int类型
            $admin_menu_ids = $admin_user['admin_menu_ids'];
            if(empty($admin_menu_ids)){
                $admin_menu_ids = [];
            }else{
                $admin_menu_ids = explode(',',$admin_user['admin_menu_ids']);

                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int)$v;
                }
            }

            // $menu_ids 如果每值就转成数组  如果有值  将里面的值全部强转成int类型
            if (empty($menu_ids)) {
                $menu_ids = [];
            }else{
                foreach ($menu_ids as $k => $v) {
                    $menu_ids[$k] = (int) $v;
                }
            }

            // 拿到后台配置的接口白名单和权限白名单
            $api_white_list = Config::get('admin.api_white_list');
            $rule_white_list = Config::get('admin.rule_white_list');

            // 将接口白名单和权限白名单合并  再将合并后的和用户所拥有的菜单名称合并
            $white_list = array_merge($api_white_list,$rule_white_list);
            $menu_url = array_merge($menu_url,$white_list);

            // 去除相同的菜单名称
            $menu_url = array_unique($menu_url);

            // 对$menu_url进行升序排列
            sort($menu_url);

            // 获取用户admin_token 
            $admin_user['admin_token'] = AdminTokenService::create();

            // 向该登录的用户的信息中加入该用户所拥有的菜单名称和菜单id 并 存入缓存中
            $admin_user['admin_role_ids'] = $admin_role_ids;
            $admin_user['admin_menu_ids'] = $admin_menu_ids;
            $admin_user['menu_ids'] = $menu_ids;
            $admin_menu['roles'] = $menu_url; 

            AdminUserCache::set($admin_user_id,$admin_user);
        }
        // 返回用户信息
        return $admin_user;
    }
}
