<?php
session_start();
$redis = include __DIR__.'/conn.php';
$post = $_POST;

$key = 'user:username:'.$post['username'];

// 判断用户key是否存在
$bool = $redis->exists($key);

if (!$bool) { // 用户不存在
  header('location:login.php');
  return;
}
// 得到用户信息   
$user = $redis->hgetall($key);

// 进行密码的比对
if ($post['password'] != $user['password']) {
  header('location:login.php');
  return;
}

//  写session
$_SESSION['user'] = $key ;

// 把发送任务发给队列中   生产与消费
// list key sendmaillist
$listKey = 'sendmaillist';

$redis->lpush($listKey,$user['email']);

// 文章列表
header('location:list.php');