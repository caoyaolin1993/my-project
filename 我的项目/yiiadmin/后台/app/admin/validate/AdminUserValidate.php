<?php
declare (strict_types = 1);

namespace app\admin\validate;

use app\admin\service\AdminUserService;
use think\Validate;

class AdminUserValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'admin_user_id' => ['require','checkAdminUser'],
        'username' => ['require','checkUsername','length'=>'2,32'],
        'nickname' => ['require','checkNickname','length'=>'1,32'],
        'password' => ['require','length'=>'6,18'],
        'email' => ['email','checkEmail'],
        'avatar' => ['require','file','image','fileExt'=>'jpg,png,gif','fileSize'=>'51200'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'admin_user_id.require' => '缺少参数：用户id',
        'username.require'  => '请输入账号',
        'username.length' => '账号长度为2至32个字符',
        'nickname.require' => '请输入昵称',
        'nickname.length'  => '昵称长度为1至32个字符',
        'password.require' => '请输入密码',
        'password.length' => '密码长度为6至18个字符',
        'email.email'     => '请输入正确的邮箱地址',
        'avatar.file'   => '请选择图片',
        'avatar.image'  => '请选择图片文件',
        'avatar.fileExt' => '请选择jpg、png、gif格式图片',
        'avatar.fileSize'  => '请选择大小小于50kb图片',
    ];


    // 验证场景
    protected $scene = [
        'user_id' => ['admin_user_id'],
        'user_login' => ['username','password'],
        'user_add' => ['username','nickname','password','email'],
        'user_edit' => ['admin_user_id','username','nickname','email'],
        'user_dele' => ['admin_user_id'],
        'user_admin' => ['admin_user_id'],
        'user_disable' => ['admin_user_id'],
        'user_rule' => ['admin_user_id'],
        'user_pwd' => ['admin_user_id','password'],
        'user_avatar' => ['admin_user_id','avatar'],
    ];

    // 验证场景定义: 登录
    // 登录时用户名和密码不能为空 别的不做限制
    protected function sceneuser_login(){
        return $this->only(['username','password'])
        ->remove('username',['length','checkUsername'])
        ->remove('password',['length']);
    }

    // 验证场景定义:修改
    protected function sceneuser_edit(){
        return $this->only(['admin_user_id'])
        ->append('admin_user_id',['checkAdminUserIsAdmin']);
    }


    // 自定义验证规则:用户是否存在
    protected function checkAdminUser($value,$rule,$data=[])
    {
        // 将穿过来的$value 赋值给变量
        $admin_user_id = $value;

        // 通过用户id拿到该用户的信息  从缓存中拿 如果缓存中没有则先在数据库中拿到 再存入缓存中
        $admin_user = AdminUserService::info($admin_user_id);

        // 判读用户是否已被删除
        if ($admin_user['is_delete'] == 1) {
            return '用户已被删除'.$admin_user_id;
        }

        return true;
    }



}
