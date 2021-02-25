<?php

declare(strict_types=1);

namespace app\admin\service;

use Firebase\JWT\JWT;
use think\facade\Config;

class AdminTokenService
{
    /**
     * Token生成
     *
     * @param array $admin_user 用户数据
     * @return void
     */
    public static function create($admin_user = [])
    {
        $admin_user = [
            'admin_user_id' => 1,
            'login_time' => time(),
            'login_ip' => '0.0.0.0',
        ];
        // 拿到默认设置的admin_token
        $admin_setting = AdminSettingService::admin_setting();
        $admin_token = $admin_setting['admin_token'];

        $key = Config::get('admin.token_key');  // 密钥
        $iss = $admin_token['iss'];   // 签发者
        $iat = time();  // 签发时间
        $nbf = time();  // 过效时间
        $exp = time() + $admin_token['exp'] * 3600;  // 过期时间

        // 将id 登录时间 登录ip 放到一个数组中
        $data = [
            'admin_user_id' => $admin_user['admin_user_id'],
            'login_time' => $admin_user['login_time'],
            'login_ip' => $admin_user['login_ip'],
        ];

        //JWT 所需要的参数1
        $payload  = [
            'iss' => $iss,
            'iat' => $iat,
            'nbf' => $nbf,
            'exp' => $exp,
            'data' => $data,
        ];

        // 通过JWT获取token
        $token = JWT::encode($payload, $key);

        return $token;
    }
}
