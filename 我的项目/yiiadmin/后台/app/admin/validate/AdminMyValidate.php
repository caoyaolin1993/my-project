<?php

declare(strict_types=1);

namespace app\admin\validate;

use app\admin\service\AdminUserService;
use think\facade\Db;
use think\Validate;

class AdminMyValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'admin_user_id' => ['require', 'checkAdminUser'],
        'username' => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname' => ['require', 'checkNickname', 'length' => '1,32'],
        'email' => ['require', 'checkEmail'],
        'password_old' => ['require'],
        'password_new' => ['require', 'length' => '6,18'],
        'avatar' => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif', 'fileSize' => '51200'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'admin_user_id.require' => '缺少参数:用户id',
        'username.require'      => '请输入账号',
        'username.length'       => '账号长度为2至32个字符',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password_new.length'   => '新密码长度为6至18个字符',
        'email.email'           => '请输入正确的邮箱地址',
        'password_old.require'  => '请输入旧密码',
        'password_new.require'  => '请输入新密码',
        'avatar.require'        => '请选择图片',
        'avatar.file'           => '请选择图片文件',
        'avatar.image'          => '请选择图片格式文件',
        'avatar.fileExt'        => '请选择jpg、png、gif格式图片',
        'avatar.fileSize'       => '请选择大小小于50kb图片',
    ];


    // 验证场景
    protected $scene = [
        'user_id' => ['admin_user_id'],
        'my_edit' => ['admin_user_id', 'username', 'nickname', 'email'],
        'my_pwd'  => ['admin_user_id', 'password_old', 'password_new'],
        'my_avatar' => ['admin_user_id', 'avatar'],
    ];

    // 自定义验证规则： 用户是否存在
    protected function checkAdminUser($value, $rule, $data = [])
    {
        // 拿到穿过来的id 给穿过来的取个见名知意的变量名
        $admin_user_id = $value;
        // 通过用户id得到用户所有的信息  其中也包括如果这个id数据库不存在 将会抛出用户不存在的异常
        $admin_user = AdminUserService::info($admin_user_id);
        // 用户信息存在 但可能是已经删除的数据 所以进行一层判断
        if ($admin_user['is_delete'] == 1) {
            return '用户已被删除:' . $admin_user_id;
        }
        return true;
    }

    /**
     *  自定义验证规则： 账号是否已存在
     *  
     * @return void
     */
    protected function checkUsername($value, $rule, $data = [])
    {
        // 拿到传进来的值
        $admin_user_id = $data['admin_user_id'];   //用户id
        $username = $data['username'];  // 用户名

        // 查找数据库中是否有相同的账号  本身的这条信息除外
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('admin_user_id', '<>', $admin_user_id)
            ->where('username', '=', $username)
            ->where('is_delete', '=', 0)
            ->find();

        //  如果存在则返回错误
        if ($admin_user) {
            return '账号已存在:' . $username;
        }
        return true;
    }

    /**
     * 自定义验证规则:昵称是否已经存在
     *
     * @param [type] $value
     * @param [type] $rule
     * @param array $data
     * @return void
     */
    protected function checkNickname($value, $rule, $data = [])
    {
        // 用户id
        $admin_user_id = $data['admin_user_id'];
        // 用户昵称
        $nickname = $data['nickname'];
        // 查看数据库除去本条数据之外是否还有其它相同的昵称
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('admin_user_id', '<>', $admin_user_id)
            ->where('nickname', '=', $nickname)
            ->where('is_delete', '=', 0)
            ->find();

        // 如果有 则抛出异常
        if ($admin_user) {
            return '昵称已存在:' . $nickname;
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
        // 用户id
        $admin_user_id = $data['admin_user_id'];
        // 用户邮箱
        $email = $data['email'];

        // 查看数据库中是否存在除去本条数据之外的相同的邮箱
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('admin_user_id', '<>', $admin_user_id)
            ->where('email', '=', $email)
            ->where('is_delete', '=', 0)
            ->find();

        // 如果存在 则返回错误
        if ($admin_user) {
            return '邮箱已存在:' . $email;
        }
        return true;
    }
}
