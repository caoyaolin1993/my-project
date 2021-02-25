<?php
// 这是系统自动生成的公共文件
function diff_time($timediff)
{
    //计算天数
    $days = intval($timediff / 86400);
    //计算小时数
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    //计算分钟数
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    //计算秒数
    $secs = $remain % 60;
    $res = '';
    if (!empty($days)) {
        $res .= $days . '天 ';
    }
    if (!empty($hours)) {
        $res .= $hours . '时 ';
    }
    if (!empty($mins)) {
        $res .= $mins . '分 ';
    }
    if (!empty($secs)) {
        $res .= $secs . '秒';
    }
    return $res;
}

/**
 * 创建token
 * @param $uid
 * @return bool|string
 */
function createToken($uid)
{
    $token = uniqid(md5(time() . $uid));

    return $token;
}


function createNonceStr($length = 5)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}