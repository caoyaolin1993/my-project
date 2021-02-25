<?php
// php脚本永不超时

set_time_limit(0);

//订阅频道
require './vendor/autoload.php';
// 实例化redis对象
$redis = new Redis();

// 连接redis     5秒超时
$redis->connect('127.0.0.1',6379,5);

// 认证
$redis->auth('redis123456');

// 订阅  mail 发邮件 list  1&mail
// 发布与订阅  1.修改数据库  2. 实时统计   3.即时聊天 4. 消息队列
$redis->subscribe(['php'],function($redis,$channel,$msg){
  echo $msg.'<hr>';

  // 发起修改数据库   例如：只要有一个人登录 就让数据库中的一个数据加1

  // websocket=>swoole  发起ws
  // 推送给websocket服务器 websocket一旦有了消息以后，就可以让粉丝们全都有了消息 这样订阅做也可以

  // 消息队列
  

});