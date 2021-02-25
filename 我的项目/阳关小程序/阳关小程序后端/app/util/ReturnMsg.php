<?php
/**
 * 返回字符串统一维护
 * 一些常量维护
 */

namespace app\util;

class ReturnMsg {
    const MISS_NECESSARY_PARA = '缺少必要参数/参数错误';
    const SUCCESS = '成功'; 
    const FAIL = '失败'; 
    const NOT_EMPTY = '不能为空';
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
    const RESOURCES_NO_EXIST = '该资源不存在或已删除';
    const SERVER_ERROR = '服务器错误,稍后请重新尝试';
    static public function getTaskName() {

    }

}