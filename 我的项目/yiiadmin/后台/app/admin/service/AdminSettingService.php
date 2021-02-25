<?php

declare(strict_types=1);

namespace app\admin\service;

use app\common\cache\AdminSettingCache;
use think\facade\Db;

class AdminSettingService
{
    // 默认设置id
    private static $admin_setting_id = 1;
    public static function admin_setting()
    {
        $admin_setting_id = self::$admin_setting_id;
        $admin_setting = AdminSettingCache::get($admin_setting_id);

        if (empty($admin_setting)) {
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->find();
            if (empty($admin_setting)) {
                $admin_setting['admin_setting_id'] = $admin_setting_id;
                $admin_setting['admin_verify'] = serialize([]);
                $admin_setting['admin_token'] = serialize([]);
                $admin_setting['create_time'] = date('Y-m-d H:i:s');
                Db::name('admin_setting')
                    ->insert($admin_setting);
            }

            // 验证码
            $admin_verify = unserialize($admin_setting['admin_verify']);
            if (empty($admin_verify)) {
                $admin_verify['switch'] = false;  // 开关
                $admin_verify['curve'] = false;  // 曲线
                $admin_verify['noise'] = false;  //  杂点
                $admin_verify['bgimg'] = false;  // 背景图
                $admin_verify['type'] = 1;       // 类型:1 数字,2 字母, 3 数字字母, 4 算术, 5 中文
                $admin_verify['length'] = 4;     // 位数3-6位
                $admin_verify['expire'] = 180;    // 有效时间(秒)
            }


            // Token
            $admin_token = unserialize($admin_setting['admin_token']);

            if (empty($admin_token)) {
                $admin_token['iss'] = 'yylAdmin';  // 签发者
                $admin_token['exp'] = 12;        // 有效时间(小时)
            }

            $admin_setting['admin_verify'] = serialize($admin_verify);
            $admin_setting['admin_token'] = serialize($admin_token);
            $admin_setting['update_time'] = date('Y-m-d H:i:s');

            Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($admin_setting);
            AdminSettingCache::set($admin_setting_id,$admin_setting);

            $admin_setting['admin_verify'] = $admin_verify;
            $admin_setting['admin_token'] = $admin_token;
        }else{
            $admin_setting['admin_verify'] = unserialize($admin_setting['admin_verify']);
            $admin_setting['admin_token'] = unserialize($admin_setting['admin_token']);
        }

        return $admin_setting;
    }
}
