<?php
// 发布频道
require './vendor/autoload.php';
// 实例化redis对象
$redis = new Redis();

// 连接redis     5秒超时
$redis->connect('127.0.0.1',6379,5);

// 认证
$redis->auth('redis123456');
$msg = '你好,世界';
//  发布
// $redis->publish('php',$_GET['msg']);
$redis->publish('php','hello world');