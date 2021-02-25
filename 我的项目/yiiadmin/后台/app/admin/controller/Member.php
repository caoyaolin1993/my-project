<?php

declare(strict_types=1);
/**
 * 会员管理
 */

namespace app\admin\controller;

/**
 * 会员控制器层 主要是和前端打交道
 */

use app\admin\service\MemberService;
use app\admin\validate\MemberValidate;
use think\facade\Request;

class Member
{
    /**
     * 会员列表
     * @return void
     */
    public function memberList()
    {
        // 拿到前端传过来的值 强转类型 给默认值
        $page = Request::param('page/d', 1);  // 当前页数
        $limit = Request::param('limit/d', 13); // 每页数量
        $member_id = Request::param('member_id/s', '');  // 会员id
        $username = Request::param('username/s', '');  // 账号
        $phone = Request::param('phone/s', '');  // 手机号
        $email = Request::param('email/s', ''); // 邮箱
        $data_type = Request::param('data_type/s', ''); // 时间类型
        $data_range = Request::param('data_range/a', []); // 时间
        $sort_field = Request::param('sort_field/s', ''); // 排序字段
        $sort_type = Request::param('sort_type/s', '');  // 排序方式

        // 因为下面的条件都可能不存在 所以要将$where赋个默认值
        $where = [];
        // 如果member_id存在 则添加到where条件中 因为member_id不是模糊查询 所以可以用索引
        if ($member_id) {
            $where[] = ['member_id', '=', $member_id];
        }

        // 如果账号存在 则添加到where条件中
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }

        // 如果手机号存在 则添加到条件中
        if ($phone) {
            $where[] = ['phone', 'like', '%' . $phone . '%'];
        }

        // 如果邮箱存在 则添加到条件中
        if ($email) {
            $where[] = ['email', 'like', '%' . $email . '%'];
        }

        // 如果date_type和date_range同时存在 则添加到条件中
        if ($data_range && $data_type) {
            // 在传过来的范围加上时分秒
            $where[] = [$data_type, '>=', $data_range[0] . '00:00:00'];
            $where[] = [$data_type, '<=', $data_range[1] . '23:59:59'];
        }

        // 因为排序字段和方式可能不存在 所以要给order赋个默认值
        $order = [];
        // 如果排序字段和方式存在 则给order条件
        if (!empty($sort_type) && !empty($sort_field)) {
            // order 可以是数据形式
            // $order = $sort_field . ' ' . $sort_type;
            $order = [$sort_field => $sort_type];
        }

        // 定义要查询的字段
        $field = '';

        // 将从前端获取到的信息传入会员服务层
        $data = MemberService::list($where, $page, $limit, $order, $field);

        // 返回前端结果
        return success($data);
    }

    /**
     * 会员添加
     *
     * @return void
     */
    public function memberAdd()
    {
        // 判读是否是get 如果是则从数据库拿到地址返回 如果不是 则是添加
        if (Request::isGet()) {
            // 在一个方法中可以做不同的事 多传一个参数就行 在方法的参数中 尽量都给默认值
            $data = MemberService::add();
        } else {
            // 拿到前端的值 强转类型 给默认值
            $param['username'] = Request::param('username/s', '');  // 账号
            $param['nickname'] = Request::param('nickname/s', '');  // 昵称
            $param['password'] = Request::param('password/s', '');  // 密码
            $param['email'] = Request::param('email/s', '');  // 邮箱
            $param['phone'] = Request::param('phone/s', '');  // 手机号
            $param['region_id'] = Request::param('region_id/d', 0); // 地区
            $param['remark'] = Request::param('remark/s', ''); // 备注
            $param['sort'] = Request::param('sort/d', 10000);  // 排序

            // 对前端传过来的值进行有效性验证  
            validate(MemberValidate::class)->scene('member_add')->check($param);

            // 将前端传过来的值传给会员服务层
            $data = MemberService::add($param,'post');
        }
        // 返回前端数据
        return success($data);
    }

    /**
     * 会员修改
     *
     * @return void
     */
    public function memberEdit()
    {
        // 不管是get请求都需要member_id
        $param['member_id'] = Request::param('member_id/d','');

        // 判断是否为get请求 如果是则是渲染 如果不是 则是修改
        if (Request::isGet()) {
            // 拿到前端传过来的参数
            // $member_id = Request::param('member_id/d', '');  // 会员id
            // 验证参数
            validate(MemberValidate::class)->scene('member_id')->check($param);
            // 将会员id传入会员服务层
            $data = MemberService::edit($param);
            // 向前端返回数据
            return success($data);
        } else {
            // 修改提交
            // 拿到前端传过来的数据 强转类型 给默认值
            $param['username'] = Request::param('username/s', '');  // 账号
            $param['nickname'] = Request::param('nickname/s', '');  // 昵称
            // $param['password'] = Request::param('password/s', '');  // 密码
            $param['email'] = Request::param('email/s', '');  // 邮箱
            $param['phone'] = Request::param('phone/s', '');  // 手机号
            $param['region_id'] = Request::param('region_id/d', 0); // 地区
            $param['remark'] = Request::param('remark/s', ''); // 备注
            $param['sort'] = Request::param('sort/d', 10000);  // 排序
            // $param['avatar'] = Request::param('avatar/s', ''); // 用户头像
            // $param['member_id'] = Request::param('member_id/d', ''); // 用户id

            // 验证参数
            validate(MemberValidate::class)->scene('member_edit')->check($param);

            // 将参数传入会员服务层
            $data = MemberService::edit($param);

            // 向前端返回数据
            return success($data);
        }
    }

    /**
     * 会员删除
     *
     * @return void
     */
    public function memberDele()
    {
        // 拿到前端参数 强转类型 给默认值
        $member_id = Request::param('member_id/d', ''); //会员id
        // 验证参数
        validate(MemberValidate::class)->scene('member_id')->check(['member_id' => $member_id]);
        // 将参数传入会员服务层
        $data = MemberService::del($member_id);
        // 返回数据到前端
        return success($data);
    }

    /**
     * 会员图像
     *
     * @return void
     */
    public function memberAvatar()
    {
        // 拿到前端参数 强转类型 给默认值
        $param['member_id'] = Request::param('member_id/d', '');  // 会员id
        $param['avatar_file'] = Request::file('avatar_file');  // 用户头像

        // 参数验证
        // validate(MemberValidate::class)->scene('avatar')->check($param);

        // 将上传的参数传入到会员服务层中
        $data = MemberService::avatar($param);
        // 返回数据给前端
        return success($data);
    }
}
