<?php

declare(strict_types=1);

namespace app\admin\validate;

use app\admin\service\MemberService;
use think\facade\Db;
use think\Validate;

class MemberValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => ['require', 'alphaDash', 'checkUsername', 'length' => '2,32'],
        'nickname' => ['checkNickname', 'length' => '1,32'],
        'password' => ['require', 'length' => '6,18','alphaNum'],
        'email'  => ['email', 'checkEmail'],
        'phone' => ['mobile','checkPhone'],
        'member_id' => ['require', 'checkMember'],
        'avatar_file' => ['file'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '账号不能为空',
        'username.length' => '账号长度在6-32之间',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度在6-32之间',
        'phone.phone' => '手机号不正确',
        'member_id' => '缺少参数',
        'avatar_file.file' => '请上传文件',
    ];

    /**
     * 验证场景
     *
     * @var array
     */
    protected $scene = [
        'member_add' => ['username', 'nickname', 'password', 'email', 'phone'],
        'member_edit' => ['username', 'nickname', 'email', 'phone', 'member_id'],
        'member_id' => ['member_id'],
        'avatar' => ['member_id', 'avatar'],
    ];

    /**
     * 自定义验证码规则：账号是否已经存在
     *
     * @param [type] $value
     * @param [type] $rule
     * @param array $data
     * @return void
     */
    protected function checkUsername($value, $rule, $data = [])
    {
        // 因为新增和编辑都要检查 新增不需要传入member_id而编辑需要 所以要先判断传过来
        // 的数据是否设置了member_id如果没有 给个空值 后续判断是否存在 如果存在就加入where条件

        // 会员id
        // $member_id = $data['member_id'];
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';

        // 会员账号
        $username = $data['username'];

        // 判断$member_id是否存在 存在则加入where条件中
        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }
        // 条件:账号
        $where[] = ['username', '=', $username];
        // 条件:未删除
        $where[] = ['is_delete', '=', 0];

        // 从数据库中查找符合条件的其它账号是否存在相同账号的会员id
        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        // 如果存在 则返回错误信息并带上该账号
        if ($member) {
            return '账号已存在:' . $username;
        }
        return true;
    }

    /**
     * 自定义验证规则: 昵称是否已经存在
     *
     * @param [type] $value
     * @param [type] $rule
     * @param array $data
     * @return void
     */
    protected function checkNickname($value, $rule, $data = [])
    {
        // 通过三元运算符判断有没有传入member_id,没有则给默认值空
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';
        // 昵称
        $nickname = $data['nickname'];

        // 判断$member_id是否存在 存在则加入where条件中
        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }

        // 条件: 昵称
        $where[] = ['nickname', '=', $nickname];
        // 条件: 未删除
        $where[] = ['is_delete', '=', 0];

        // 从数据库中查找符合条件的会员id的会员昵称
        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();
        // 如果有 则返回错误信息
        if ($member) {
            return '昵称已存在' . $nickname;
        }

        return true;
    }

    /**
     * 自定义验证规则:邮箱是否已存在
     *
     * @param [type] $value
     * @param [type] $rule
     * @param array $data
     * @return void
     */
    protected function checkEmail($value, $rule, $data = [])
    {
        // 通过三元运算符判断member_id是否存在 不存在给默认值空
        $member_id = isset($data['member_id']) ? $data['member_id'] : '';

        // 邮箱
        $email = $data['email'];

        // 判断$member_id是否存在 存在则加入where条件中
        if ($member_id) {
            $where[] = ['member_id', '<>', $member_id];
        }

        // 条件:邮箱
        $where[] = ['email', '=', $email];

        // 条件:未删除
        $where[] = ['is_delete', '=', 0];

        // 从数据库中查找符合条件的信息 只需要member_id字段
        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        // 如果存在 则返回错误信息
        if ($member) {
            return '邮箱已存在:' . $email;
        }

        return true;
    }

    /**
     * 自定义验证规则:手机号是否存在
     *
     * @param [type] $value
     * @param [type] $rule
     * @param array $data
     * @return void
     */
    public function checkPhone($value,$rule,$data=[])
    {
        // 通过三元运算符判断$member_id是否存在 不存在给默认值空
        $member_id = isset($data['member_id'])?$data['member_id']:'';

        // 手机号
        $phone = $data['phone'];

        // 判断$member_id是否存在 存在则加入where条件
        if ($member_id) {
            $where[] = ['member_id','<>',$member_id];
        }

        // 条件:手机号
        $where[] = ['phone','=',$phone];
        // 条件:未删除
        $where[] = ['is_delete','=',0];

        // 根据条件从数据库中查找信息
        $member = Db::name('member')
            ->field('member_id')
            ->where($where)
            ->find();

        // 如果存在 则返回错误信息
        if ($member) {
            return '手机号已存在:'.$phone;
        }

        return true;
    }

    /**
     * 自定义验证规则:检测会员id的有效性
     *
     * @param [type] $value
     * @param [type] $rule
     * @param array $data
     * @return void
     */
    public function checkMember($value, $rule, $data = [])
    {
        // 会员id
        $member_id = $value;

        //通过member_id就可以从会员服务层拿到这个会员的全部信息 或是缓存 或是数据库 缓存的目的是减少与mysql数据库的交互
        $member = MemberService::info($member_id);

        // 通过该会员id 查询数据库是否有信息
        // $member = Db::name('member')
        //     ->where('member_id', $member_id)
        //     ->find();
        // 如果不存在 返回错误
        // if (empty($member)) {
        //     return '该会员不存在';
        // }

        // 如果 is_delete = 1 则返回错误
        if ($member['is_delete'] == 1) {
            return '会员已被删除:'.$member_id;
        }
        return true;
    }
}
