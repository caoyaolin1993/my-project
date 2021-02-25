<?php
declare (strict_types = 1);
/**
 * 日志管理(专门处理日志的服务层)
 */
namespace app\admin\service;

use app\common\service\IpInfoService;
use think\facade\Db;

class AdminLogService
{   

    /**
     * 日志列表
     *
     * @param array $where  条件
     * @param integer $page 分页
     * @param integer $limit 数量
     * @param array $order 排序
     * @param string $field 字段
     * @return void
     */
    public static function list($where=[],$page=1,$limit=10,$order=[],$field='')
    {
        // 先判断前端是否传过来了字段 没有的话给默认值
        if (empty($field)) {
             $field = 'admin_log_id,admin_user_id,admin_menu_id,request_method,request_ip,request_region,request_isp,create_time';   
        }

        // 在where条件中加入 未删除这个条件
        $where[] = ['is_delete','=',0];

        // 判断$order是否为空 为空则给默认值
        if (empty($order)) {
            $order = ['admin_log_id'=>'desc'];
        }

        // 拿到该条件下的总条数
        $count = Db::name('admin_log')
            ->where($where)
            ->count('admin_log_id');

        // 拿到该条件下日志的信息
        $list = Db::name('admin_log')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        // 对$list 进行处理 通过admin_user_id 和admin_menu_id 从另外两张表拿到其它的信息
        foreach ($list as $k => $v) {
            // 先不管有没有通过admin_user_id从其它表拿到信息 给每条信息加入 username 和nickname 赋值为空
            $list[$k]['username'] = '';   // 用户名
            $list[$k]['nickname'] = '';   // 昵称

            // 通过admin_user_id 拿到用户的所有的信息
            $admin_user = AdminUserService::info($v['admin_user_id']);

            // 如果用户的信息的存在 则更新这条信息的username和nickname
            if ($admin_user) {
                $list[$k]['username'] = $admin_user['username'];
                $list[$k]['nickname'] = $admin_user['nickname'];
            }

            // 先不管有没有通过admin_menu_id从其它表中拿到信息 给每条信息加入 menu_name 和 menu_url 赋值为空
            $list[$k]['menu_name'] = '';  // 菜单名称
            $list[$k]['menu_url'] = ''; // 菜单地址

            // 通过admin_menu_id 拿到菜单相关信息
            $admin_menu = AdminMenuService::info($v['admin_menu_id']);  
            // 如果菜单信息存在 则更新这条信息的menu_name 和 menu_url
            if ($admin_menu) {
                $list[$k]['menu_name'] = $admin_menu['menu_name'];
                $list[$k]['menu_url'] = $admin_menu['menu_url'];
            }
        }

        // 算出总页数  总条数除以每页数量 向上取整
        $pages = ceil($count/$limit);

        // 封装需要返回的数据
        $data['count'] = $count;  // 总条数
        $data['pages'] = $pages;  // 总页数
        $data['page'] = $page;   // 当前页数
        $data['limit'] = $limit; // 当前每页条数
        $data['list'] = $list;  // 当前页日志信息

        return $data; 
    }
    

    public static function add($param = [])
    {
        //判断传过来的参数中是否有ip信息 如果有 则通过穿过来的ip 拿到ip的相关信息
        if ($param['request_ip']) {
            $ip_info = IpInfoService::info($param['request_ip']);

            $param['request_country'] = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city'] = $ip_info['city'];
            $param['request_area'] = $ip_info['area'];
            $param['request_region'] = $ip_info['region'];
            $param['request_isp'] = $ip_info['isp'];
        }

        // 存入创建时间 
        $param['create_time'] = date('Y-m-d H:i:s');

        // 将日志信息存入数据库中   strict(false) 设置不严格检查数据表字段名
        Db::name('admin_log')->strict(false)->insert($param);
    }



}
