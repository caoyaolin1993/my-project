<?php

declare(strict_types=1);
/**
 *  会员服务层 与会员控制器打交道 处理逻辑和操作数据库
 */

namespace app\admin\service;

use app\common\cache\MemberCache;
use think\facade\Db;
use think\facade\Filesystem;

class MemberService
{
    /**
     * 会员列表
     *
     * @param [type] $page 当前页
     * @param [type] $limit 每页数量
     * @param [type] $where 条件
     * @param [type] $order 排序
     * @param [type] $field 字段
     * @return void
     */
    public static function list($where = [], $page = 1, $limit = 13, $order = [], $field = '')
    {
        // 判断$field是否为空,为空则给定默认值
        if (empty($field)) {
            $field = 'member_id,create_time,email,is_disable,login_time,nickname,phone,remark,sort,username';
        }

        // 判断$order是否存在 不存在则给定默认值
        if (empty($order)) {
            // $order = 'member_id desc';
            // 可以定义为数组 先根据sort字段进行排序 再根据id进行排序
            $order = ['sort' => 'desc', 'member_id' => 'desc'];
        }

        // 信息是未被删除的
        $where[] = ['is_delete', '=', 0];

        // 通过条件拿到member_id的总条数 
        $count = Db::name('member')
            ->where($where)
            ->count('member_id');
        // 通过条件从数据库中拿到数据
        $list = Db::name('member')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();
        // 通过总条数 和每页条数 算出总页数  ceil() 向上取整
        $pages = ceil($count / $limit);

        // 将需要返回的信息封装到数组中返回
        $data['page'] = $page;  // 当前页
        $data['pages'] = $pages; // 总页数
        $data['count'] = $count; // 总条数
        $data['limit'] = $limit; // 每页数量
        $data['list'] = $list;  // 会员信息

        return $data;
    }

    /**
     * 会员信息
     *
     * @param [type] $member_id 会员id
     * @return void
     */
    public static function info($member_id)
    {
        // 缓存不仅是存入数据库中的信息 还可以存有用的信息

        // 通过会员id从缓存中拿到会员信息
        $member = MemberCache::get($member_id);

        // 如果缓存中没有 则从数据库拿再存入缓存中  
        if (empty($member)) {
            // 通过会员id拿到数据库信息
            $member = Db::name('member')
                ->where('member_id', $member_id)
                ->find();

            // 将数据存入缓存中
            MemberCache::set($member_id, $member);
        }

        // 返回数据
        return $member;
    }



    /**
     * 会员添加
     *
     * @param [type] $param 会员信息
     * @return void
     */
    public static function add($param = [], $method = 'get')
    {
        // 如果method为get 则拿到地址信息 否则是会员信息保存数据库
        if ($method == 'get') {
            // 将参数提交到地区服务层,拿到需要的信息  传如tree 是拿到全部的地区信息
            $region_id = 'tree';
            $data['region_tree'] = RegionService::info($region_id);

            return $data;
        } else {
            // 加入创建时间
            $param['create_time'] = date('Y-m-d H:i:s');

            // 将密码加密
            $param['password'] = md5($param['password']);

            // 操作数据库  添加数据
            $member_id = Db::name('member')
                ->insertGetId($param);

            // 如果操作数据库失败 抛出异常
            if (empty($member_id)) {
                exception();
            }

            # 添加的会员信息不加入缓存
            // $data = $param;
            // // 加入相关信息 存入缓存
            // $data['is_disable'] = 0;
            // $data['is_delete'] = 0;
            // $data['login_num'] = 0;
            // $data['member_id'] = $member_id;

            // MemberCache::set($member_id, $data);

            // 向返回的数据加入member_id
            $param['member_id'] = $member_id;

            // 除去数组中的password
            unset($param['password']);
            // 返回数据 
            return $param;
        }
    }

    /**
     * 会员编辑
     *
     * @param [type] $param 会员信息
     * @return void
     */
    public static function edit($param,$method='get')
    {
        // 会员id
        $member_id = $param['member_id'];




        // 加入更新时间
        $param['update_time'] = date('Y-m-d H:i:s');

        // 除去 $param 中的member_id
        unset($param['member_id']);

        // 通过会员id 更新数据中的值
        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($param);

        // 如果数据库操作失败 返回错误
        if (empty($res)) {
            exception();
        }

        // 因为会员表发生了改变 所以之前的缓存就没有用 需要更新
        MemberCache::upd($member_id);

        // 加入member_id以便返回
        $param['member_id'] = $member_id;
        // 返回信息
        return $param;
    }


    /**
     * 会员删除
     *
     * @param [type] $member_id 会员id
     * @return void
     */
    public static function del($member_id)
    {
        // 通过会员id操作数据库 将is_delete更新为1 
        $update['is_delete'] = 1;
        // 更新删除操作时间
        $update['delete_time'] = date('Y-m-d H:i:s');
        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        // 操作数据库失败 返回错误    
        if (empty($res)) {
            exception();
        }

        // 加入会员id返回
        $update['member_id'] = $member_id;

        return $update;
    }


    /**
     * 会员头像
     *
     * @param [type] $param 头像信息
     * @return void
     */
    public static function avatar($param)
    {
        // 会员id
        $member_id = $param['member_id'];
        // 头像
        $avatar_file = $param['avatar_file'];

        // 通过下面的操作 将头像上传到服务器的public的member文件夹下
        $avatar_name = Filesystem::disk('public')
            ->putFile('member', $avatar_file, function () use ($member_id) {
                return $member_id . '/' . $member_id . '_avatar';
            });
        // 拼接上设置好的storage文件夹
        $avatar_name = 'storage/' . $avatar_name;
        // 现在需要拿到访问这个public的协议和域名
        // http://www.adminone.com/storage/member/5/5_avatar.jpg
        $avatar_url = file_url($avatar_name);

        // 将新上传的头像更新到数据库中
        $res = Db::name('member')
            ->where('member_id', $member_id)
            ->update(['avatar' => $avatar_url]);

        if ($res != 0 && empty($res)) {
            exception();
        }

        // 因为该条会员信息发生了改变,所以需要更新缓存
        MemberCache::upd($member_id);

        // 封装数据 返回需要的信息
        $data['member_id'] = $member_id;
        $data['avatar'] = $avatar_url;
        return $data;
    }
}
