<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use app\util\ReturnCode;
use PHPExcel;
use PHPExcel_IOFactory;
use think\facade\Db;
use think\facade\Env;
use think\facade\Filesystem;
use think\facade\Request;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class Account extends AdminAuth
{
    //患者编码列表
    public function index()
    {
        $where[] = ['id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());  //接收数组需要使用这种形式
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['type', 'in', $type];
        }

        $page = input('post.page', 1);  //第二个参数为默认值
        $limit = input('post.limit', 10);


        $list = Db::name('user_code')
            ->where($where)
            ->page($page, $limit)
            ->order('id')
            ->select()->toArray();
        foreach ($list as $key => $value) {

            $list[$key]['updatetime'] =   $value['updatetime'] ? date('Y-m-d H:i', $value['updatetime']) : '';

            if ($value['type'] == '1') {
                $list[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '7') {
                $list[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list[$key]['type_name'] = 'N-普通人群';
            }
        }

        $total = Db::name('user_code')->where($where)->count();
        $page_total = ceil($total / $limit); //向上舍入为最接近的整数

        $return = [
            'list' => $list,
            'page_total' => $page_total,
            'page' => $page,
            'total' => $total
        ];
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //添加患者编码
    public function add()
    {
        $number = input('post.number');  //患者编号
        $name   = input('post.name');   //患者名称
        $phone  = input('post.phone');   //患者手机号
        $type   = input('post.type');  //患者分类
        $admin_id = input('post.admin_id');  //操作账号id
        $admin = input('post.admin');
        if (empty($number)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写患者编码', 'data' => []];
            return json($data);
        }
        if (empty($name)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写姓名', 'data' => []];
            return json($data);
        }
        if (empty($phone)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写手机号', 'data' => []];
            return json($data);
        }
        if (empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请选择患者分类', 'data' => []];
            return json($data);
        }
        if (!preg_match('/^1[3456789]\d{9}$/', $phone)) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '请填写正确的手机号', 'data' => []];
            return json($data);
        }
        //验证患者编码是否存在
        $check_number = Db::name('user_code')
            ->where('number', $number)
            ->find();

        if ($check_number) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '该患者编码已存在', 'data' => []];
            return json($data);
        }
        //验证手机号是否存在
        $check_phone = Db::name('user_code')
            ->where('phone', $phone)
            ->find();
        if ($check_phone) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '该手机号已存在', 'data' => []];
            return json($data);
        }
        $insert_data = [
            'number' => $number,
            'name'   => $name,
            'phone'  => $phone,
            'type'   => $type,
            'admin'   => $admin,
            'admin_id'   => $admin_id,
            'createtime' => time()
        ];
        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //先添加患者信息
            $id = Db::name('user_code')->insertGetId($insert_data);
            //判断是否患者需要更改同步信息
            $check_info = Db::name('user')->where(['phone' => $phone, 'name' => $name])->field('id')->find();
            if ($check_info) {
                Db::name('user')->where(['id' => $check_info['id']])->update([
                    'number'  => $number,
                    'name'    => $name,
                    'phone'   => $phone,
                    'type'    => $type,
                    'type_way' => '2',
                    'user_code_id' => $id,
                ]);
            }
            // 提交事务
            Db::commit();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        return json($data);
    }

    //删除账号
    public function del()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误，请重新尝试！', 'data' => []];
            return json($data);
        }
        $row = Db::name('user_code')
            ->where('id', $id)
            ->find();
        if (empty($row)) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '参数有误，请重新尝试！'];
            return json($data);
        }

        //事务操作，保证数据一致性 
        Db::startTrans();
        try {
            //先删除患者信息
            Db::name('user_code')->where('id', $id)->delete();
            //判断是否患者需要更改同步信息
            $where_a[] = ['type', 'in', ['1', '2']];
            $check_info = Db::name('user')->where(['user_code_id' => $id])->where($where_a)->field('id,type_way,name,phone,open_id')->find();
            if ($check_info) {
                if ($check_info['type_way'] == '1') { //邀请码删除之后判断是否资料匹配
                    $res = Db::name('user_code')->where(['name' => $check_info['name'], 'phone' => $check_info['phone']])->find();
                    if ($res) { //资料匹配
                        //查询是否该患者编码已经被匹配
                        $check_used = Db::name('user')->where(['user_code_id' => $res['id']])->field('id')->find();
                        if (!$check_used) { //没有被使用
                            Db::name('user')->where('id', $id)->update([
                                'user_code_id' => $res['id'],
                                'type'         => $res['type'],
                                'number'       => $res['number'],
                                'code'         => '',
                                'type_way'     => '2'
                            ]);
                        } else {
                            //查询用户是否已经填写健康信息
                            $health = Db::name('user_health_info')->where('openid', $check_info['open_id'])->field('depression')->find();
                            if ($health) { //已经填写了健康信息
                                if ($health['depression'] == '是') { // 是，【升级】为患者-B1，分类标记方式填“问卷标记”
                                    Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '7', 'type_way' => '5', 'code' => '', 'number' => '', 'user_code_id' => '']);
                                } elseif ($health['depression'] == '过去患病但已痊愈') { //过去患病已经痊愈，【升级】为缓解期-B2，分类标记方式填“问卷标记
                                    Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '8', 'type_way' => '5', 'code' => '', 'number' => '', 'user_code_id' => '']);
                                } else { //否
                                    //查询该用户是否填写过抑郁自评
                                    $depression = Db::name('depression_info')->where('openid', $check_info['open_id'])->field('CP')->find();
                                    if ($depression['CP'] > 5 || $depression['CP'] == 5) { // 抑郁自评总分≥5，【升级】至“高危-分数”分类，分类标记方式填“PHQ9分数”
                                        Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '5', 'type_way' => '4', 'code' => '', 'number' => '', 'user_code_id' => '']);
                                    }
                                }
                            } else { //更改为游客
                                Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '0', 'type_way' => '0', 'code' => '', 'number' => '', 'user_code_id' => '']);
                            }
                        }
                    } else {
                        //查询用户是否已经填写健康信息
                        $health = Db::name('user_health_info')->where('openid', $check_info['open_id'])->field('depression')->find();
                        if ($health) { //已经填写了健康信息
                            if ($health['depression'] == '是') { // 是，【升级】为患者-B1，分类标记方式填“问卷标记”
                                Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '7', 'type_way' => '5', 'code' => '', 'number' => '', 'user_code_id' => '']);
                            } elseif ($health['depression'] == '过去患病但已痊愈') { //过去患病已经痊愈，【升级】为缓解期-B2，分类标记方式填“问卷标记
                                Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '8', 'type_way' => '5', 'code' => '', 'number' => '', 'user_code_id' => '']);
                            } else { //否
                                //查询该用户是否填写过抑郁自评
                                $depression = Db::name('depression_info')->where('openid', $check_info['open_id'])->field('CP')->find();
                                if ($depression['CP'] > 5 || $depression['CP'] == 5) { // 抑郁自评总分≥5，【升级】至“高危-分数”分类，分类标记方式填“PHQ9分数”
                                    Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '5', 'type_way' => '4', 'code' => '', 'number' => '', 'user_code_id' => '']);
                                }
                            }
                        }
                    }
                } else { //资料匹配的用户更改判断
                    //查询用户是否已经填写健康信息
                    $health = Db::name('user_health_info')->where('openid', $check_info['open_id'])->field('depression')->find();
                    if ($health) { //已经填写了健康信息
                        if ($health['depression'] == '是') { // 是，【升级】为患者-B1，分类标记方式填“问卷标记”
                            Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '7', 'type_way' => '5', 'number' => '', 'user_code_id' => '']);
                        } elseif ($health['depression'] == '过去患病但已痊愈') { //过去患病已经痊愈，【升级】为缓解期-B2，分类标记方式填“问卷标记
                            Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '8', 'type_way' => '5', 'number' => '', 'user_code_id' => '']);
                        } else { //否
                            //查询该用户是否填写过抑郁自评
                            $depression = Db::name('depression_info')->where('openid', $check_info['open_id'])->field('CP')->find();
                            if ($depression['CP'] > 5 || $depression['CP'] == 5) { // 抑郁自评总分≥5，【升级】至“高危-分数”分类，分类标记方式填“PHQ9分数”
                                Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '5', 'type_way' => '4', 'number' => '', 'user_code_id' => '']);
                            }
                        }
                    } else { //更改为游客
                        Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '0', 'type_way' => '0', 'number' => '', 'user_code_id' => '']);
                    }
                }
            }
            // 提交事务
            Db::commit();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        return json($data);
    }

    /*患者编码信息*/
    public function info()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        //验证患者编码是否存在
        $info = Db::name('user_code')->where('id', $id)->field('id,number,name,phone,type')->find();
        if (!$info) {
            $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        if ($info['type'] == '1') {
            $info['type_name'] = '患者';
        } elseif ($info['type'] == '2') {
            $info['type_name'] = '高危';
        } else {
            $info['type_name'] = '缓解期';
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $info];
        return json($data);
    }

    /*患者编码修改*/
    public function edit()
    {
        $id = input('post.id');
        $number = trim(input('post.number'));
        $name = trim(input('post.name'));
        $phone = trim(input('post.phone'));
        $type = input('post.type');
        $admin_id = input('post.admin_id');
        $admin = input('post.admin');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        if (empty($number)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写患者编码', 'data' => []];
            return json($data);
        }
        if (empty($name)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写姓名', 'data' => []];
            return json($data);
        }
        if (empty($phone)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写手机号', 'data' => []];
            return json($data);
        }
        if (empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请选择患者分类', 'data' => []];
            return json($data);
        }
        if (!preg_match('/^1[3456789]\d{9}$/', $phone)) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '请填写正确的手机号', 'data' => []];
            return json($data);
        }
        //验证患者编码是否存在
        $check_number = Db::name('user_code')
            ->where('number', $number)
            ->where('id', '<>', $id)
            ->find();
        if ($check_number) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '该患者编码已存在', 'data' => []];
            return json($data);
        }
        //验证手机号是否存在
        $check_phone = Db::name('user_code')
            ->where('phone', $phone)
            ->where('id', '<>', $id)
            ->find();
        if ($check_phone) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '该手机号已存在', 'data' => []];
            return json($data);
        }

        $update = [
            'number' => $number,
            'name'   => $name,
            'phone'  => $phone,
            'type'   => $type,
            'admin'  => $admin,
            'admin_id'   => $admin_id,
            'updatetime' => time()
        ];

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //先修改患者信息
            Db::name('user_code')->where('id', $id)->update($update);
            //再判断是否需要更改之前同步的患者的信息
            // $check_info = Db::name('user')->where(['user_code_id' => $id, 'type_way' => ['in', ['1', '2']]])->field('id,name,phone,type_way')->find();
            $check_info = Db::name('user')->where('user_code_id', $id)->where('type_way', 'in', ['1', '2'])->field('id,name,phone,type_way')->find();

            if ($check_info) {
                if ($check_info['type_way'] == '1') { //存在邀请用户
                    //修改信息
                    Db::name('user')->where(['id' => $check_info['id']])->update([
                        'number' => $number,
                        'name'   => $name,
                        'phone'  => $phone,
                        'type'   => $type
                    ]);
                } else { //存在匹配资料用户
                    if ($check_info['name'] == $name && $check_info['phone'] == $phone) { //还是资料匹配不变
                        Db::name('user')->where(['id' => $check_info['id']])->update([
                            'number' => $number,
                            'type'   => $type,
                        ]);
                    } else {
                        //1、修改之前匹配人信息
                        //将之前资料匹配的用户降维
                        $res = Db::name('user_code')->where(['name' => $check_info['name'], 'phone' => $check_info['phone']])->find();
                        if ($res) { //资料匹配
                            //查询是否该患者编码已经被匹配
                            $check_used = Db::name('user')->where(['user_code_id' => $res['id']])->field('id')->find();
                            if (!$check_used) { //没有被使用
                                Db::name('user')->where('id', $id)->update([
                                    'user_code_id' => $res['id'],
                                    'type'         => $res['type'],
                                    'number'       => $res['number'],
                                ]);
                            } else {
                                //查询用户是否已经填写健康信息
                                $health = Db::name('user_health_info')->where('openid', $check_info['open_id'])->field('depression')->find();
                                if ($health) { //已经填写了健康信息
                                    if ($health['depression'] == '是') { // 是，【升级】为患者-B1，分类标记方式填“问卷标记”
                                        Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '7', 'type_way' => '5', 'number' => '', 'user_code_id' => '']);
                                    } elseif ($health['depression'] == '过去患病但已痊愈') { //过去患病已经痊愈，【升级】为缓解期-B2，分类标记方式填“问卷标记
                                        Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '8', 'type_way' => '5', 'number' => '', 'user_code_id' => '']);
                                    } else { //否
                                        //查询该用户是否填写过抑郁自评
                                        $depression = Db::name('depression_info')->where('openid', $check_info['open_id'])->field('CP')->find();
                                        if ($depression['CP'] > 5 || $depression['CP'] == 5) { // 抑郁自评总分≥5，【升级】至“高危-分数”分类，分类标记方式填“PHQ9分数”
                                            Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '5', 'type_way' => '4', 'number' => '', 'user_code_id' => '']);
                                        }
                                    }
                                } else { //更改为游客
                                    Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '0', 'type_way' => '0', 'number' => '', 'user_code_id' => '']);
                                }
                            }
                        } else {
                            //查询用户是否已经填写健康信息
                            $health = Db::name('user_health_info')->where('openid', $check_info['open_id'])->field('depression')->find();
                            if ($health) { //已经填写了健康信息
                                if ($health['depression'] == '是') { // 是，【升级】为患者-B1，分类标记方式填“问卷标记”
                                    Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '7', 'type_way' => '5', 'number' => '', 'user_code_id' => '']);
                                } elseif ($health['depression'] == '过去患病但已痊愈') { //过去患病已经痊愈，【升级】为缓解期-B2，分类标记方式填“问卷标记
                                    Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '8', 'type_way' => '5', 'number' => '', 'user_code_id' => '']);
                                } else { //否
                                    //查询该用户是否填写过抑郁自评
                                    $depression = Db::name('depression_info')->where('openid', $check_info['open_id'])->field('CP')->find();
                                    if ($depression['CP'] > 5 || $depression['CP'] == 5) { // 抑郁自评总分≥5，【升级】至“高危-分数”分类，分类标记方式填“PHQ9分数”
                                        Db::name('user')->where('open_id', $check_info['open_id'])->update(['type' => '5', 'type_way' => '4', 'number' => '', 'user_code_id' => '']);
                                    }
                                }
                            }
                        }
                        //2、判断修改后的信息是否存在资料匹配
                        $get_info = Db::name('user')->where(['name' => $name, 'phone' => $phone, 'user_code_id' => ''])->find();
                        if ($get_info) {
                            Db::name('user')->where(['id' => $get_info['id']])->update([
                                'number'  => $number,
                                'type'    => $type,
                                'type_way' => '2',
                                'user_code_id' => $id,
                            ]);
                        }
                    }
                }
            }

            // 提交事务
            Db::commit();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    function inviteCode()
    {
        set_time_limit(0);
        $admin_id = input('post.admin_id');
        $admin = input('post.admin');
        //查询没有生成邀请码的数据
        $result = Db::name('user_code')->where(['code' => ''])->field('id')->select()->toArray();
        $update = [];
        if ($result) {
            foreach ($result as $key => $value) {
                $code = self::createNonceStr();
                $update['code'] = $code;
                $update['admin'] = $admin;
                $update['admin_id'] = $admin_id;
                $update['updatetime'] = time();
                Db::name('user_code')->where('id', $value['id'])->update($update);
            }
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
            return json($data);
        }
    }

    private function createNonceStr($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        //验证是否已经存在该邀请码
        $check = Db::name('user_code')->where('code', $str)->find();
        if ($check) {
            self::createNonceStr(6);
        }
        return $str;
    }

    //批量导出
    function excel()
    {
        set_time_limit(0);

        $where[] = ['id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());  //接收数组需要使用这种形式
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['type', 'in', $type];
        }

        $data = Db::name('user_code')
            ->where($where)
            ->order('id asc')
            ->select()->toArray();
        foreach ($data as $key => $value) {
            if ($value['type'] == '1') {
                $data[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $data[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $data[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '7') {
                $data[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $data[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $data[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $data[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $data[$key]['type_name'] = 'N-普通人群';
            }
        }

        $PHPExcel = new \PHPExcel(); //实例化phpexcel

        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F');
        $sheet_title = array('序号', '编码', '患者分类', '姓名', '手机号', '邀请码');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(15);
        $PHPSheet->getColumnDimension('B')->setWidth(30);
        $PHPSheet->getColumnDimension('C')->setWidth(25);
        $PHPSheet->getColumnDimension('D')->setWidth(20);
        $PHPSheet->getColumnDimension('E')->setWidth(20);
        $PHPSheet->getColumnDimension('F')->setWidth(20);
        //数据
        foreach ($data as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                $PHPSheet->setCellValue('A' . $row, ' ' . $num);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['number']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['type_name']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['name']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['phone']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['code']);
            }
            ob_flush();
            flush();
        }

        $filename = '患者编码' . date('Ymd', time());
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    public function importExecl()
    {
        // ini_set('memory_limit', '1024M'); //修改PHP的内存限制
        if (Request::isPost()) {   //只有POST请求才允许
            $file = request()->file('file');  //获取上传的文件对象
            // 上传到本地服务器
            $ext = $file->getOriginalExtension();   //获取文件后缀
            if (!in_array($ext, ['xls', 'xlsx'])) {
                return_msg(-1, '请上传xls或者xlsx格式');
            }
            $savename = Filesystem::putFile('topic', $file); //获取保存的文件名
            $path = app()->getRuntimePath() . '/storage/' . $savename;  //获取文件路径

            if ($ext == 'xlsx') {
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            } else {
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            $obj_PHPExcel = $objReader->load($path, $encode = 'utf-8');  //加载文件内容,编码utf-8
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            unset($excel_array[0]);
            $datas = [];
            foreach ($excel_array as $key => $value) {
                if (empty($value[0]) || empty($value[1]) || empty($value[2]) || empty($value[3])) {
                    unset($excel_array[$key]);
                    continue;
                }
                //验证编号是否已经存在
                $checks = Db::name('user_code')->where('number', $value[0])->field('id')->find();
                if (!$checks) {
                    $datas[$key]['number'] = $value[0];
                }
            }
            
            if (empty($datas)) {
                $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '该文件已上传过，请勿重复上传'];
                return json($data);
            }

            //事务操作，保证数据一致性
            Db::startTrans();
            try {
                foreach ($excel_array as $k => $v) {
                    //验证编号是否已经存在
                    $insert_data = [];
                    $check = Db::name('user_code')->where('number', $v[0])->field('id')->find();
                    if (!$check) {
                        $insert_data['number'] = $v[0];
                        if ($v[1] == 'P-患者') {
                            $insert_data['type']   = '1';
                        } elseif ($v[1] == 'H-高危人群') {
                            $insert_data['type']   = '2';
                        } elseif ($v[1] == 'R-缓解期患者') {
                            $insert_data['type']   = '3';
                        } elseif ($v[1] == 'P2-患者轻度') {
                            $insert_data['type']   = '7';
                        } elseif ($v[1] == 'P3-患者中度') {
                            $insert_data['type']   = '8';
                        } elseif ($v[1] == 'P4-患者重度') {
                            $insert_data['type']   = '9';
                        } elseif ($v[1] == 'P5-自曝患者') {
                            $insert_data['type']   = '12';
                        } elseif ($v[1] == 'N-普通人群') {
                            $insert_data['type']   = '11';
                        }
                        $insert_data['name']   = trim($v[2]);
                        $insert_data['phone']  = $v[3];
                       
                        //先添加患者信息
                        $id = Db::name('user_code')->insertGetId($insert_data);
                        
                        //判断是否患者需要更改同步信息
                        $check_info = Db::name('user')->where(['phone' => $v[3], 'name' => trim($v[2])])->field('id')->find();
                        if ($check_info) {
                            Db::name('user')->where(['id' => $check_info['id']])->update([
                                'number'   => $insert_data['number'],
                                'name'     => $insert_data['name'],
                                'phone'    => $insert_data['name'],
                                'type'     => $insert_data['phone'],
                                'type_way' => '2',
                                'user_code_id' => $id,
                            ]);
                        }
                    }
                }
                // 提交事务
                Db::commit();
                $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
                return json($data);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }
}
