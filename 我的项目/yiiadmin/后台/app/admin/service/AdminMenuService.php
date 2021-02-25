<?php
declare (strict_types = 1);

namespace app\admin\service;

use app\common\cache\AdminMenuCache;
use think\facade\Db;

class AdminMenuService
{
    /**
     * 菜单信息
     *
     * @param [type] $admin_menu_id  菜单id 此菜单id可以是admin_menu_id 也可以是admin_menu
     * @return void
     */
    public static function info($admin_menu_id)
    {   
        // 从试着从缓存中拿到缓存的菜单信息
        $admin_menu = AdminMenuCache::get($admin_menu_id);

        // 如果没有拿到,则说明当前缓存不存在菜单缓存信息 这个时候就从数据表中拿到信息再存入缓存中
        if (empty($admin_menu)) {
                // 判断传入的菜单id是不是数字 如果是数字 则说明使用的是id 如果不是 则说明使用的 menu_url
                if (is_numeric($admin_menu_id)) {
                    $where[] = ['admin_menu_id','=',$admin_menu_id];
                }else{
                    $where[] = ['is_delete','=',0];
                    $where[] = ['menu_url','=',$admin_menu_id];
                }

                // 通过查询条件从数据库总查询菜单信息
                $admin_menu = Db::name('admin_menu')
                    ->where($where)
                    ->find();

                // 如果查看的为空 则说明这个菜单不存在
                if (empty($admin_menu)) {
                    exception('菜单不存在:'.$admin_menu_id);
                }

                // 存在就将该条信息存入缓存中
                AdminMenuCache::set($admin_menu_id,$admin_menu);
        }


        // 返回菜单信息
        return $admin_menu;
    }

    /**
     * 菜单模糊查询
     *
     * @param [type] $keyword   关键词
     * @param string $field     字段
     * @return array
     */
    public static function likeQuery($keyword,$field='menu_url|menu_name')
    {
        // 从数据库查到符合条件的信息
        $data = Db::name('admin_menu')
            ->where('is_delete','=',0)
            ->where($field,'like','%'.$keyword.'%')
            ->select()
            ->toArray();
        return $data;
    }









}
