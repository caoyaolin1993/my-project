<?php
// 应用公共文件
use app\util\ReturnCode;
use think\facade\Db;
/**
 *  数据返回
 * @param  [int] $code [结果码 200:正常/4**数据问题/5**服务器问题]
 * @param  [string] $msg  [返回的提示信息]
 * @param  [array]  $data [返回的数据]
 * @return [string]       [最终的json数据]
 */
function return_msg($code, $msg = '', $data = [])
{
    /*********** 组合数据  ***********/
    $return_data['code'] = $code;
    $return_data['msg']  = $msg;
    $return_data['data'] = $data;

    /*********** 返回信息并终止脚本  ***********/
    echo json_encode($return_data, JSON_UNESCAPED_UNICODE);
    die;
}

/**
 * 验证数据是否为空
 *
 * @param array $arr
 * @param string $msg
 * @return void
 */
function validat_data($arr, $msg = '')
{
    if (is_array($arr)) {
        foreach ($arr as $v) {
            if (empty($v)) {
                return_msg(ReturnCode::EMPTY_PARAMS, $msg);
            }
        }
    } else {
        if (empty($arr)) {
            return_msg(ReturnCode::EMPTY_PARAMS, $msg);
        }
    }
}

/**
 * 验证数据是否设置
 *
 * @param [type] $arr
 * @param string $msg
 * @return void
 */
function isset_data($arr, $msg = '')
{
    if(is_array($arr)){
        foreach($arr as $v){
            if(!isset($v)){
                return_msg(ReturnCode::EMPTY_PARAMS, $msg);
            }
        }
    }else{
        if(!isset($v)){
            return_msg(ReturnCode::EMPTY_PARAMS, $msg);
        }
    }
}

function get_data($str = '')
{
    return input('post.' . $str);
}

function get_data_arr($str = '')
{
    return input('post.' . $str . '/a', array());
}

// 开启事务
function  start_Trans()
{
    Db::startTrans();
}

// 提交事务
function  end_Trans()
{
    Db::commit();
}

//回滚事务
function roll_back()
{
    Db::rollback();
}



//计算两个时间的差值
function timediff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    //计算天数
    $timediff = $endtime - $starttime;
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