<?php

/**
 * admin配置
 */

 return [
  // 系统管理员id
  'admin_ids' => [1],

  // 请求头部token键名
  'admin_token_key'=>'AdminToken',

  // 请求头部user_id键名
  'admin_user_id_key' => 'AdminUserId',

  // 接口白名单
  'api_white_list' => [
    'admin/AdminLogin/verify',
    'admin/AdminLogin/login',
  ],


  // 权限白名单
  'rule_white_list' => [
    'admin/AdminMy/myInfo',
    'admin/AdminIndex/index',
    'admin/AdminLogin/logout',
  ],


  // token 密钥
  'token_key' => '58o6dAEZ4Jbb',



 ];