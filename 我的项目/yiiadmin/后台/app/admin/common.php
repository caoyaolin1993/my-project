<?php
// 这是系统自动生成的公共文件
// admin公共文件

use think\facade\Config;
use think\facade\Request;

/**
 * 获取请求用户id
 *
 * @return void
 */
function admin_user_id()
{
  // 首先拿到后台配置的请求用户id header头的key
  $admin_user_id_key = Config::get('admin.admin_user_id_key');

  // 再通过这个key拿到前端header头穿过来的$admin_user_id  前端没有传的话就给个默认空值
  $admin_user_id= Request::header($admin_user_id_key,'');

  // 返回结果
  return $admin_user_id;
}
