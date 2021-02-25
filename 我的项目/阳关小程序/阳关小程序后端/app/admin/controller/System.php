<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use app\common\controller\Base;
use Config;
use app\util\Tools;
use app\util\ReturnCode;
use think\facade\Db;

class System extends AdminAuth
{

    /* 设置-角色权限-列表 */
    public function authorList()
    {
        $list = Db::name('auth_group')->where('id', '>', '1')->field('id,title,menu')->select()->toArray();
        foreach ($list as $key => $value) {
            $menu = Db::name('menu')->whereIn('id', $value['menu'])->where('level', 1)->field('id,name')->order('id')->select()->toArray();
            $menu_info = [];
            foreach ($menu as $k => $v) {
                $info = Db::name('menu')->whereIn('id', $value['menu'])->where('pid', $v['id'])->field('id,name')->order('sort')->select()->toArray();
                $menu_name = [];
                foreach ($info as $ka => $va) {
                    $menu_name[] = $va['name'];
                }
                if (empty($menu_name)) {
                    $menu_info[$k] = $v['name'];
                } else {
                    $menu_info[$k] = $v['name'] . '-' . implode(',', $menu_name);
                }
            }
            $list[$key]['menu_info'] = $menu_info;
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功', 'data' => $list];
        return json($data);
    }

    /* 权限信息 */
    public function levelInfo()
    {
        $token = input('post.token');
        //$token = 'd8d26b4cb8c6dfba386d143505ddacb25ed8a080e693a';

        $user_menu = Db::name('admin')
            ->alias('a')
            ->where(['a.token' => $token])
            ->join('cm_auth_group b', 'a.role_id = b.id')
            ->field('b.menu')
            ->find();  //通过token 找到admin表中的role_id 等于 cm_auth_group 中的 menu 字段

        $menu = Db::name('menu')->where(['pid' => '0', 'state' => 1])->field('id,name')->order('id')->select()->toArray(); //找到父id为0 的  id 和 name

        // dump($menu);die;
        foreach ($menu as $key => $value) {  //第一层 遍历父id为0的 id 和name  中的id 是否存在于 总的menu字段中 如果存在 则在该数据中多加一个exist 的key  值为1 不存在为0 
            if (in_array($value['id'], explode(',', $user_menu['menu']))) {
                $menu[$key]['exist'] = '1'; //存在
            } else {
                if ($value['id'] == 20) {
                    $menu[$key]['exist'] = '1'; //存在
                } else {
                    $menu[$key]['exist'] = '0'; //存在
                }
            }
            // $menu[$key]['exist'] = '1'; //存在

            $info = Db::name('menu')->where(['pid' => $value['id'], 'state' => 1])->field('id,name')->order('id')->select()->toArray();
            foreach ($info as $k => $v) {  //第二层  遍历父id为上层循环的id 的id和name  存在 1  不存 0 
                if (in_array($v['id'], explode(',', $user_menu['menu']))) {
                    $info[$k]['exist'] = '1'; //存在
                    $menu[$key]['exist'] = '1';
                } else {
                    $info[$k]['exist'] = '0'; //不存在
                }
                $level_3 = Db::name('menu')->where(['pid' => $v['id'], 'state' => 1])->field('id,name')->order('id')->select()->toArray();
                foreach ($level_3 as $ke => $va) {  //第三层  遍历父id为上层循环的id 的id和name  存在 1  不存 0 
                    if (in_array($va['id'], explode(',', $user_menu['menu']))) {
                        $level_3[$ke]['exist'] = '1'; //存在
                        $menu[$key]['exist'] = '1';
                    } else {
                        $level_3[$ke]['exist'] = '0'; //不存在
                    }
                }
                $info[$k]['info'] = $level_3;
            }
            $menu[$key]['info'] = $info;
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功', 'data' => $menu];
        return json($data);
    }

    /* 新增角色 */
    public function authorAdd()
    {
        $title = input('post.title');
        $menu  = input('post.menu/a', array());
        if (empty($title) || empty($menu)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/或参数错误', 'data' => []];
            return json($data);
        }
        //判断该名称是否存在
        $check_name = Db::name('auth_group')->where('title', $title)->find();
        if ($check_name) {
            $data = ['code' => ReturnCode::DATA_EXISTS, 'msg' => '该角色名称已存在', 'data' => []];
            return json($data);
        }
        //根据menu计算rules
        $rule = Db::name('auth_rule')->where([['type', 'in', $menu]])->field('id')->order('id')->select()->toArray();
        $rules_arr = [];
        foreach ($rule as $ka => $va) {
            $rules_arr[] = $va['id'];
        }
        $rules = implode(',', $rules_arr);

        $arr = [
            'title' => $title,
            'rules' => $rules,
            'menu'  => implode(',', $menu),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $res = Db::name('auth_group')->insert($arr);

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '操作失败'];
        }
        return json($data);
    }

    /*角色信息*/
    public function authorInfo()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/或参数错误', 'data' => []];
            return json($data);
        }
        //获取角色名称
        $auth_group = Db::name('auth_group')->where(['id' => $id])->field('id,title,menu')->order('id')->find();
        //获取权限
        $menu = Db::name('menu')->where(['pid' => '0', 'state' => 1])->field('id,name')->order('id')->select()->toArray();
        foreach ($menu as $key => $value) {
            $menu[$key]['exist'] = '1'; //存在
            $info = Db::name('menu')->where(['pid' => $value['id'], 'state' => 1])->field('id,name')->order('id')->select()->toArray();
            foreach ($info as $k => $v) {
                if (in_array($v['id'], explode(',', $auth_group['menu']))) {
                    $info[$k]['exist'] = '1'; //存在
                } else {
                    $info[$k]['exist'] = '0'; //不存在
                }
                $level_3 = Db::name('menu')->where(['pid' => $v['id'], 'state' => 1])->field('id,name')->order('id')->select()->toArray();
                foreach ($level_3 as $ke => $va) {
                    if (in_array($va['id'], explode(',', $auth_group['menu']))) {
                        $level_3[$ke]['exist'] = '1'; //存在
                    } else {
                        $level_3[$ke]['exist'] = '0'; //不存在
                    }
                }
                $info[$k]['info'] = $level_3;
            }
            $menu[$key]['info'] = $info;
        }

        $list = [
            'title' => $auth_group['title'],
            'menu' => $menu
        ];

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功', 'data' => $list];
        return json($data);
    }

    /*修改角色*/
    public function authorEdit()
    {
        $id = input('post.id');
        $title = input('post.title');
        $menu  = input('post.menu/a', array());
        if (empty($id) || empty($title) || empty($menu)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/或参数错误', 'data' => []];
            return json($data);
        }
        //判断该名称是否存在
        $check_name = Db::name('auth_group')->where('title', $title)->find();
        if ($check_name && $check_name['id'] != $id) {
            $data = ['code' => ReturnCode::DATA_EXISTS, 'msg' => '该角色名称已存在', 'data' => []];
            return json($data);
        }
        //根据menu计算rules
        $rule = Db::name('auth_rule')->where([['type', 'in', $menu]])->field('id')->order('id')->select()->toArray();
        $rules_arr = [];
        foreach ($rule as $ka => $va) {
            $rules_arr[] = $va['id'];
        }
        $rules = implode(',', $rules_arr);

        $update = [
            'title' => $title,
            'rules' => $rules,
            'menu'  => implode(',', $menu),
            'updated_at' => date('Y-m-d H:i:s', time())
        ];
        $a = 1;
        $res = Db::name('auth_group')->where('id', $id)->update($update);
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '操作失败'];
        }
        return json($data);
    }

    /*删除角色 */
    public function authorDel()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数', 'data' => []];
            return json($data);
        }
        //判断该角色下是否存在管理员
        $row = Db::name('admin')->where('role_id', $id)->select()->toArray();
        if (!empty($row)) { //修改该角色下账号的分组
            Db::name('admin')->where('role_id', $id)->update(['role_id' => '0', 'role' => '']);
        }
        $res = Db::name('auth_group')->where('id', $id)->delete();
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    /*账号管理-列表*/
    public function categoryList()
    {
        $list = Db::name('admin')->where([['role_id', '>', '1']])->field('id,account,name,phone,role')->select()->toArray();

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $list];
        return json($data);
    }

    /*账号管理-新增账号*/
    public function categoryAdd()
    {
        $account  = input('post.account');
        $password  = input('post.password');
        $name     = input('post.name');
        $phone    = input('post.phone');
        $role_id  = input('post.role_id');
        if (empty($account) || empty($password) || empty($name) || empty($phone) || empty($role_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/或参数错误', 'data' => []];
            return json($data);
        }
        //根据role_id查询role名称
        $role = Db::name('auth_group')->where('id', $role_id)->value('title');
        if (!isset($role)) {
            $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '参数错误', 'data' => []];
            return json($data);
        }
        //判断手机号是否存在
        $check_phone = Db::name('admin')->where('phone', $phone)->find();
        if ($check_phone) {
            $data = ['code' => ReturnCode::DATA_EXISTS, 'msg' => '手机号已存在', 'data' => []];
            return json($data);
        }
        $salt = createNonceStr(5);
        $add = [
            'account' => $account,
            'password' => md5(md5($password . $salt)),
            'salt'    => $salt,
            'name'    => $name,
            'phone'   => $phone,
            'role_id' => $role_id,
            'role'    => $role,
            'create_time' => date('Y-m-d H:i:s', time())
        ];

        $res = Db::name('admin')->insertGetId($add);
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '操作失败'];
        }
        return json($data);
    }

    /*账号管理-账号信息*/
    public function categoryInfo()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数', 'data' => []];
            return json($data);
        }
        if ($id == '1') {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '操作有误', 'data' => []];
            return json($data);
        }
        //查询信息
        $res = Db::name('admin')->where('id', $id)->field('id,account,name,phone,role,role_id')->find();
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $res];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    /*账号管理-角色信息*/
    public function roleInfo()
    {
        $res = Db::name('auth_group')->where('id', '>', '1')->field('id,title')->select()->toArray();
        if ($res || $res === []) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $res];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }


    /*账号管理-修改账号*/
    public function categoryEdit()
    {
        $id       = input('post.id');
        $account  = input('post.account');
        $name     = input('post.name');
        $phone    = input('post.phone');
        $role_id  = input('post.role_id');

        $token = input('post.token');
        $dataRoleId = Db::name('admin')->where('token', $token)->find();

        if (empty($id) || empty($account) || empty($name) || empty($phone) || empty($role_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/或参数错误', 'data' => []];
            return json($data);
        }
        //判断手机号是否存在
        $check_phone = Db::name('admin')->where('phone', $phone)->find();
        if ($check_phone && $check_phone['id'] != $id) {
            $data = ['code' => ReturnCode::DATA_EXISTS, 'msg' => '手机号已存在', 'data' => []];
            return json($data);
        }
        //根据role_id查询role名称
        $role = Db::name('auth_group')->where('id', $role_id)->value('title');
        if (!isset($role)) {
            $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '参数错误', 'data' => []];
            return json($data);
        }
        $update = [
            'account' => $account,
            'name'    => $name,
            'phone'   => $phone,
            'role_id' => $role_id,
            'role'    => $role
        ];

        if ($dataRoleId['role_id'] == 1) {
            $password = input('post.password');
            $update['password'] = md5(md5($password . $check_phone['salt']));
        }

        $res = Db::name('admin')->where('id', $id)->update($update);
        if ($res >= 0) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功', 'data' => $dataRoleId];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '操作失败'];
        }
        return json($data);
    }

    /*账号管理-删除账号*/
    public function categoryDel()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数', 'data' => []];
            return json($data);
        }
        if ($id == '1') {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '操作有误', 'data' => []];
            return json($data);
        }
        //删除账号
        $res = Db::name('admin')->where('id', $id)->delete();
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }


    /*修改密码*/
    public function changePsw()
    {
        $token = input('post.token');
        $password = input('post.password');
        $new_password = input('post.new_password');
        $re_new_password = input('post.re_new_password');
        if (empty($password) || empty($new_password)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数', 'data' => []];
            return json($data);
        }
        if ($new_password != $re_new_password) {
            $data = ['code' => ReturnCode::TYPE_ERROR, 'msg' => '确认密码与新密码不一致', 'data' => []];
            return json($data);
        }
        if ($password == $new_password) {
            $data = ['code' => ReturnCode::TYPE_ERROR, 'msg' => '新密码与原密码一致', 'data' => []];
            return json($data);
        }
        $row = Db::name('admin')
            ->where('token', $token)
            ->find();
        if (empty($row)) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '账户不存在'];
            return json($data);
        }
        $psw = md5(md5($password . $row['salt']));
        if ($psw != $row['password']) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '原密码错误'];
            return json($data);
        }
        $psw = md5(md5($new_password . $row['salt']));
        //修改密码并且清除token
        $res = Db::name('admin')->where('token', $token)->update(['password' => $psw, 'token' => '']);
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }
}
