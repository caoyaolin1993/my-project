<?php
/**
 * 需验证的权限接口统一维护
 */

namespace app\util;

class AuthFilter {
    public $AuthFilter = [
        'admin/System/changePsw', //修改密码
        'admin/System/levelInfo',//权限信息
        'admin/System/roleInfo',//权限信息
        'admin/Account/info',//患者编码点击修改信息
    ];
}

