<?php
/**
 * 错误码统一维护
 * 一些常量维护
 */

namespace app\util;

class ReturnCode {
    const SUCCESS = 200; //成功
    const EMPTY_PARAMS = -1; //缺少参数
    const INVALID = -2; //操作无效
    const DB_SAVE_ERROR = -3; //数据库存储失败
    const DB_READ_ERROR = -4; //数据读取错误  （修改时验证传入数据id）
    const TYPE_ERROR = -5; //参数格式错误
    const DATA_EXISTS = -6; //数据已存在
    const HEADER_ERROR = -7; //非合法请求头
    const STAFF_NO_EXISTS = -8; //用户不存在
    const AUTH_NO_EXISTS = -9; //路由未注册权限
    const NO_AUTH = -10; //无权限操作
    const NO_FILE = -11; //文件不存在
    const DATA_ERROR = -12; //数据有误

    const NO_LOGIN = -200; //尚未登陆
    const RESOURCES_NO_EXIST = -13;
    static public function getTaskName() {

    }

}