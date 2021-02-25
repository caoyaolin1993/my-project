<?php
// 应用公共文件

use think\facade\Config;
use think\facade\Request;

/**
 * 成功返回
 *
 * @param array $data 成功数据
 * @param string $msg 成功提示
 * @param integer $code 成功码
 * @return json
 * 
 */
function success($data = [], $msg = '操作成功', $code = 200)
{
  $res['code'] = $code;
  $res['msg'] = $msg;
  $res['data'] = $data;

  return json($res);
}

/**
 * 错误返回
 *
 * @param string $msg 错误提示
 * @param array $err 错误数据
 * @param integer $code 错误码
 * @return void
 */
function error($msg = '操作失败', $err = [], $code = 400)
{
  $res['code'] = $code;
  $res['msg'] = $msg;
  $res['err'] = $err;

  print_r(json_encode($res, JSON_UNESCAPED_UNICODE));

  exit;
}

/**
 * 抛出异常
 *
 * @param string $msg  异常提示
 * @param integer $code  错误码
 * @return void
 */
function exception($msg = '操作失败', $code = 400)
{
  throw new \think\Exception($msg, $code);
}


/**
 * http get 请求
 *
 * @param string $url    请求地址
 * @param array  $header 请求头部
 *
 * @return array
 */
function http_get($url, $header = [])
{
  if (empty($header)) {
    $header = [
      "Content-type:application/json;",
      "Accept:application/json"
    ];
  }

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
  $response = curl_exec($curl);
  curl_close($curl);
  $response = json_decode($response, true);

  return $response;
}

/**
 * 服务器地址
 * 协议和域名
 *
 * @return void
 */
function server_url()
{

  // 判断服务器的地址使用的域名 前的协议时https 还是http 
  if (isset($_SERVER['HTTPS']) && (('1' == $_SERVER['HTTPS']) || 'on' == strtolower($_SERVER['HTTPS']))) {
    // 如果超全局标量$_SERVER 中存在 HTTPS 这个键 并且这个键的值为 1 或者转为小写时为 on  则协议为https
    $http = 'https://';
  } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
    //如果超全局标量$_SERVER 中存在 SERVER_PORT 这个键 并且这个键的值为 443  则协议为https
    $http = 'https://';
  } else {
    // 否则 协议为http
    $http = 'http://';
  }

  // 从超全局变量中拿到服务器的域名
  $host = $_SERVER['HTTP_HOST'];

  // 将获取到的协议和域名进行拼接
  $res = $http . $host;

  return $res;
}

/**
 * 文件地址
 * 协议,域名,文件路径
 * @param string $file_path  文件路径  
 * @return void
 */
function file_url($file_path = '')
{
  // 如果文件路径为空,则返回空
  if (empty($file_path)) {
    return '';
  }

  // 在穿过来的文件路径中查看是否有 http strpos(区分大小写) 如果有则直接返回这个文件路径
  if (strpos($file_path, 'http') !== false) {
    return $file_path;
  }

  // 如果没有  说明不包含协议  需要再做处理
  // 首席拿到服务器的协议和域名
  $server_url = server_url();

  // 使用 stripos(不区分大小写)判断/是否存在第一个位置 如果 === 0说名/在锴头  如果不是 在和域名拼接时就需要加上/
  if (stripos($file_path, '/') === 0) {
    $res = $server_url . $file_path;
  } else {
    $res = $server_url . '/' . $file_path;
  }

  return $res;
}

/**
 * 判断用户是否系统管理员
 *
 * @param integer $admin_user_id 用户id
 * @return void
 */
function admin_is_admin($admin_user_id = 0)
{
  // 如果传入的admin_user_id 为空 则返回false
  if (empty($admin_user_id)) {
    return false;
  }

  // 拿到从后台直接配置的管理员id
  $admin_ids = Config::get('admin.admin_ids', []);

  // 判断当前id是否在配置的管理员id数组中
  if (in_array($admin_user_id, $admin_ids)) {
    // 如果在  返回true
    return true;
  } else {
    // 不在返回false
    return false;
  }
}

/**
 * 获取请求pathinfo
 *  应用/控制器/操作
 * @return void
 */
function request_pathinfo()
{

  $request_pathinfo = app('http')->getName().'/'.Request::pathinfo();

  return $request_pathinfo;
}
