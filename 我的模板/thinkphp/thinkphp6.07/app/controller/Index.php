<?php
namespace app\controller;

use think\facade\Db;
use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        $res = Db::name('title')->select();
        halt($res);
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
