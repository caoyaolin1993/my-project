<?php
require './vendor/autoload.php';
// 实例化redis对象
$redis = new Redis();

// 连接redis     5秒超时
$redis->connect('127.0.0.1',6379,5);

// 认证
$redis->auth('redis123456');

// $name = $redis->get('a');

// // $redis->set('name','bbbbb',['nx','ex'=>100]);

// $res = $redis->ttl('name');







