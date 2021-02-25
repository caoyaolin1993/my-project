<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\facade\Request;

header("Access-Control-Allow-Origin:*");
class Index  extends cmPrefix
{
    //关闭公众号时间提醒
    public function close_dingyue()
    {
        $open_id = input('post.open_id');

        $type = input('post.type');  //练习1 课程2
        if (empty($open_id) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        if ($type == 1) {
            Db::name('ds')->where('open_id', $open_id)->update([
                'lx_time' => 0
            ]);
        } else {
            Db::name('ds')->where('open_id', $open_id)->update([
                'kc_time_1' => 0,
                'kc_time_2' => 0,
                'kc_time_3' => 0,
                'kc_time_4' => 0,
                'kc_time_5' => 0,
                'kc_time_6' => 0,
                'kc_time_7' => 0,
                'kc_time_str' => '',
            ]);
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
        return json($data);
    }

    //最后一个返回下节课开始时间
    public function next_class_time()
    {
        $open_id = input('post.open_id');
        $course = input('post.course');
        if (empty($open_id) || empty($course)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $find = Db::name('lock')->where('open_id', $open_id)->find();

        switch ($course) {
            case 1:
                $date = strtotime(date('Y-m-d', $find['one_etime'])) + 7 * 24 * 60 * 60;
                $result = [
                    'date' => date('Y-m-d', $date)
                ];
                break;
            case 2:
                $date = strtotime(date('Y-m-d', $find['two_etime'])) + 7 * 24 * 60 * 60;
                $result = [
                    'date' => date('Y-m-d', $date)
                ];
                break;
            case 3:
                $date = strtotime(date('Y-m-d', $find['three_etime'])) + 7 * 24 * 60 * 60;
                $result = [
                    'date' => date('Y-m-d', $date)
                ];
                break;
            case 4:
                $date = strtotime(date('Y-m-d', $find['four_etime'])) + 7 * 24 * 60 * 60;
                $result = [
                    'date' => date('Y-m-d', $date)
                ];
                break;
            case 5:
                $date = strtotime(date('Y-m-d', $find['five_etime'])) + 7 * 24 * 60 * 60;
                $result = [
                    'date' => date('Y-m-d', $date)
                ];
                break;
            case 6:
                $date = strtotime(date('Y-m-d', $find['six_etime'])) + 7 * 24 * 60 * 60;
                $result = [
                    'date' => date('Y-m-d', $date)
                ];
                break;
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $result];
        return json($data);
    }

    //判断是否需要获取公众号open_id
    public function is_get_public_open_id()
    {
        $open_id = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $find = Db::name('ds')->where('open_id', $open_id)->field('public_open_id')->find();

        if ($find['public_open_id']) {
            $res = 1;    // 1表示是有 公众号的open_id
        } else {
            $res = 2;   // 2表示是没有
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $res];

        return json($data);
    }

    //定时提醒
    public function dingyue()
    {

        $open_id = input('post.open_id');
        $template_id = input('post.template_id');
        $lx_page = input('post.lx_page');
        $kc_page = input('post.kc_page');
        $type = input('post.type');   // 练习 1 课程 2  
        $lx_time = input('post.lx_time');
        $kc_time = input('post.kc_time');

        if (empty($open_id) || empty($template_id) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        if ($type == 1) {  // type为1时 则练习时间不能为空
            if (empty($lx_time)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '练习时间不能为空'];
                return json($data);
            }
            $lxt = date('Y-m-d', time()) . ' ' . $lx_time;
            $lxtz = strtotime($lxt);
            $find = Db::name('ds')->where('open_id', $open_id)->find();

            if ($find) {
                Db::name('ds')->where('id', $find['id'])->update([
                    'template_id1' => $template_id,
                    'lx_page' => $lx_page,
                    'lx_time' => $lxtz,
                ]);
            } else {
                Db::name('ds')->insertGetId([
                    'open_id' => $open_id,
                    'template_id1' => $template_id,
                    'lx_page' => $lx_page,
                    'lx_time' => $lxtz,
                ]);
            }
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
            return json($data);
        } elseif ($type == 2) {      //type为2时  则课程时间不能为空
            if (empty($kc_time)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '课程时间不能为空'];
                return json($data);
            }

            $res = Db::name('lock')->where('open_id', $open_id)->withoutField('id,open_id')->find();

            if ($res['one_etime']) {
                $strt = date('Y-m-d', ($res['one_etime'] + 7 * 24 * 3600)) . ' ' . $kc_time;
                $kc_time_2 = strtotime($strt);
            } else {
                $kc_time_2 = 0;
            }
            if ($res['two_etime']) {
                $strt = date('Y-m-d', ($res['two_etime'] + 7 * 24 * 3600)) . ' ' . $kc_time;
                $kc_time_3 = strtotime($strt);
            } else {
                $kc_time_3 = 0;
            }
            if ($res['three_etime']) {
                $strt = date('Y-m-d', ($res['three_etime'] + 7 * 24 * 3600)) . ' ' . $kc_time;
                $kc_time_4 = strtotime($strt);
            } else {
                $kc_time_4 = 0;
            }
            if ($res['four_etime']) {
                $strt = date('Y-m-d', ($res['four_etime'] + 7 * 24 * 3600)) . ' ' . $kc_time;
                $kc_time_5 = strtotime($strt);
            } else {
                $kc_time_5 = 0;
            }
            if ($res['five_etime']) {
                $strt = date('Y-m-d', ($res['five_etime'] + 7 * 24 * 3600)) . ' ' . $kc_time;
                $kc_time_6 = strtotime($strt);
            } else {
                $kc_time_6 = 0;
            }
            if ($res['six_etime']) {
                $strt = date('Y-m-d', ($res['six_etime'] + 7 * 24 * 3600)) . ' ' . $kc_time;
                $kc_time_7 = strtotime($strt);
            } else {
                $kc_time_7 = 0;
            }

            $find = Db::name('ds')->where('open_id', $open_id)->find();

            if ($find) {
                Db::name('ds')->where('id', $find['id'])->update([
                    'template_id2' => $template_id,
                    'kc_page' => $kc_page,
                    'kc_time_str' => $kc_time,
                    'kc_time_2' => $kc_time_2,
                    'kc_time_3' => $kc_time_3,
                    'kc_time_4' => $kc_time_4,
                    'kc_time_5' => $kc_time_5,
                    'kc_time_6' => $kc_time_6,
                    'kc_time_7' => $kc_time_7,
                ]);
            } else {
                Db::name('ds')->insertGetId([
                    'open_id' => $open_id,
                    'template_id2' => $template_id,
                    'kc_page' => $kc_page,
                    'kc_time_str' => $kc_time,
                    'kc_time_2' => $kc_time_2,
                    'kc_time_3' => $kc_time_3,
                    'kc_time_4' => $kc_time_4,
                    'kc_time_5' => $kc_time_5,
                    'kc_time_6' => $kc_time_6,
                    'kc_time_7' => $kc_time_7,
                ]);
            }

            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
            return json($data);
        }
    }

    //知识宝库解锁状态
    public function kn_tre()
    {
        $openid = input('post.open_id');
        if (empty($openid)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        $info = Db::name('course_record')
            ->where('open_id', $openid)
            ->field('one_status,two_status,three_status,four_status,five_status,six_status,seven_status')
            ->find();
        if (empty($info)) {
            $info = [
                'one_status'   => '0', //学习状态:0=未学习,1=学习，2=复习
                'two_status'   => '0',
                'three_status' => '0',
                'four_status'  => '0',
                'five_status'  => '0',
                'six_status'   => '0',
                'seven_status' => '0'
            ];
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $info];
        return json($data);
    }

    //判断是否学完8套问卷
    public function isLearnS1()
    {
        //必传参数
        $openId = input('post.open_id');
        //验证数据有效性
        if (empty($openId)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $res = Db::name("depressive_effects")->where('openid', $openId)->find();

        if ($res) {
            $arr['is_learn'] = 1;
        } else {
            $arr['is_learn'] = 2;
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $arr];
        return json($data);
    }

    //放松训练
    public function relax_end()
    {
        $openId = input('post.open_id');
        $stime = input('post.stime');
        $relax_type = input('post.relax_type');
        $is_forw = input('post.is_forw');
        $play_etime = input('post.play_etime');

        if (empty($openId) || empty($relax_type) || empty($stime)  || empty($is_forw)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        //事务操作，保证数据一致性
        Db::startTrans();

        try {
            Db::name('relax_tra')->insertGetId([
                'open_id' => $openId,
                'relax_type' => $relax_type,
                'stime' => $stime,
                'etime' => time(),
                'ltime' => (time() - $stime),
                'play_etime' => $play_etime
            ]);


            $rs = Db::name('relax_click')->where(['open_id' => $openId, 'relax_type' => $relax_type])->find();


            if ($rs) {
                if ($is_forw == 1) {
                    Db::name('relax_click')->where('open_id', $openId)->update([
                        'click_num' => ($rs['click_num'] + 1),
                        'forw_num' => ($rs['forw_num'] + 1)
                    ]);
                } else {
                    Db::name('relax_click')->where('open_id', $openId)->update([
                        'click_num' => ($rs['click_num'] + 1),
                    ]);
                }
            } else {
                if ($is_forw == 1) {
                    Db::name('relax_click')->insertGetId([
                        'open_id' => $openId,
                        'relax_type' => $relax_type,
                        'click_num' => 1,
                        'forw_num' => 1
                    ]);
                } else {
                    Db::name('relax_click')->insertGetId([
                        'open_id' => $openId,
                        'relax_type' => $relax_type,
                        'click_num' => 1,
                    ]);
                }
            }

            // 提交事务
            Db::commit();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        return json($data);
    }

    //解锁状态上传
    public function upLock()
    {
        $openId = input('post.open_id');
        $course = input('post.course');
        $new = input('post.new');  //1=学习 2=复习
        $etime = input('post.etime');

        if (empty($openId) || empty($course)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        //事务操作，保证数据一致性
        Db::startTrans();
        $find = Db::name('lock')->where('open_id', $openId)->find();
        switch ($course) {
            case 1:
                $field = 'one_etime';
                break;
            case 2:
                $field = 'two_etime';
                break;
            case 3:
                $field = 'three_etime';
                break;
            case 4:
                $field = 'four_etime';
                break;
            case 5:
                $field = 'five_etime';
                break;
            case 6:
                $field = 'six_etime';
                break;
        };

        try {
        if ($new == 1) {
            if ($find) {
                Db::name('lock')->where('open_id', $openId)->update([
                    $field => $etime
                ]);
            } else {
                Db::name('lock')->insert([
                    'open_id' => $openId,
                    $field => $etime
                ]);
            }

            $dsfind = Db::name('ds')->where('open_id', $openId)->find();
            if ($dsfind) {
                if ($dsfind['kc_time_str']) {
                    $lockfind = Db::name('lock')->where('open_id', $openId)->field($field)->find();

                    if ($lockfind[$field]) {
                        switch ($field) {
                            case 'one_etime':
                                $strt = date('Y-m-d', $lockfind['one_etime']) . ' ' . $dsfind['kc_time_str'];
                                $kc_time_2 = strtotime($strt);

                                Db::name('ds')->where('open_id', $openId)->update([
                                    'kc_time_2' => $kc_time_2
                                ]);

                                break;
                            case 'two_etime':
                                $strt = date('Y-m-d', $lockfind['two_etime']) . ' ' . $dsfind['kc_time_str'];
                                $kc_time_3 = strtotime($strt);

                                Db::name('ds')->where('open_id', $openId)->update([
                                    'kc_time_3' => $kc_time_3
                                ]);
                                break;
                            case 'three_etime':
                                $strt = date('Y-m-d', $lockfind['three_etime']) . ' ' . $dsfind['kc_time_str'];

                                $kc_time_4 = strtotime($strt);

                                Db::name('ds')->where('open_id', $openId)->update([
                                    'kc_time_4' => $kc_time_4
                                ]);
                                break;
                            case 'four_etime':
                                $strt = date('Y-m-d', $lockfind['four_etime']) . ' ' . $dsfind['kc_time_str'];
                                $kc_time_5 = strtotime($strt);

                                Db::name('ds')->where('open_id', $openId)->update([
                                    'kc_time_5' => $kc_time_5
                                ]);
                                break;
                            case 'five_etime':
                                $strt = date('Y-m-d', $lockfind['five_etime']) . ' ' . $dsfind['kc_time_str'];
                                $kc_time_6 = strtotime($strt);

                                Db::name('ds')->where('open_id', $openId)->update([
                                    'kc_time_6' => $kc_time_6
                                ]);
                                break;
                            case 'six_etime':
                                $strt = date('Y-m-d', $lockfind['six_etime']) . ' ' . $dsfind['kc_time_str'];
                                $kc_time_7 = strtotime($strt);

                                Db::name('ds')->where('open_id', $openId)->update([
                                    'kc_time_7' => $kc_time_7
                                ]);
                                break;
                        }
                    }
                }
            }
        } else {
            if ($new == 2 && empty($find[$field])) {
                if ($find) {
                    Db::name('lock')->where('open_id',$openId)->update([
                        $field => $etime
                    ]);
                } else {
                    Db::name('lock')->insert([
                        'open_id' => $openId,
                        $field => $etime
                    ]);
                }

                $dsfind = Db::name('ds')->where('open_id', $openId)->find();
                if ($dsfind) {
                    if ($dsfind['kc_time_str']) {

                        $lockfind = Db::name('lock')->where('open_id', $openId)->field($field)->find();
                        if ($lockfind[$field]) {
                            switch ($field) {
                                case 'one_etime':
                                    $strt = date('Y-m-d', $lockfind['one_etime']) . ' ' . $dsfind['kc_time_str'];
                                    $kc_time_2 = strtotime($strt);

                                    Db::name('ds')->where('open_id', $openId)->update([
                                        'kc_time_2' => $kc_time_2
                                    ]);

                                    break;
                                case 'two_etime':
                                    $strt = date('Y-m-d', $lockfind['two_etime']) . ' ' . $dsfind['kc_time_str'];
                                    $kc_time_3 = strtotime($strt);

                                    Db::name('ds')->where('open_id', $openId)->update([
                                        'kc_time_3' => $kc_time_3
                                    ]);
                                    break;
                                case 'three_etime':

                                    $strt = date('Y-m-d', $lockfind['three_etime']) . ' ' . $dsfind['kc_time_str'];
                                    $kc_time_4 = strtotime($strt);

                                    Db::name('ds')->where('open_id', $openId)->update([
                                        'kc_time_4' => $kc_time_4
                                    ]);

                                    break;
                                case 'four_etime':
                                    $strt = date('Y-m-d', $lockfind['four_etime']) . ' ' . $dsfind['kc_time_str'];
                                    $kc_time_5 = strtotime($strt);

                                    Db::name('ds')->where('open_id', $openId)->update([
                                        'kc_time_5' => $kc_time_5
                                    ]);
                                    break;
                                case 'five_etime':
                                    $strt = date('Y-m-d', $lockfind['five_etime']) . ' ' . $dsfind['kc_time_str'];
                                    $kc_time_6 = strtotime($strt);

                                    Db::name('ds')->where('open_id', $openId)->update([
                                        'kc_time_6' => $kc_time_6
                                    ]);
                                    break;
                                case 'six_etime':
                                    $strt = date('Y-m-d', $lockfind['six_etime']) . ' ' . $dsfind['kc_time_str'];
                                    $kc_time_7 = strtotime($strt);

                                    Db::name('ds')->where('open_id', $openId)->update([
                                        'kc_time_7' => $kc_time_7
                                    ]);
                                    break;
                            }
                        }
                    }
                }
            }
        }


        // 提交事务
        Db::commit();
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
        return json($data);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败', 'data' => []];
        return json($data);
    }

    //每个用户学习情况
    public function index()
    {
        $openid = input('post.openid');

        $allowOpenid = [
            'ox1pL5EdZqenLyYMg6LQ7gqUVVdw', //开发者
            'ox1pL5DLvnktU6r2BL43ZQHNQjb0', //华依林
            'ox1pL5NQr1Rt2XNs1N996n38B9TI', //李秀雯
            'ox1pL5NtGdJONtfs1xA1tHrSFSuc', //林莉
            'ox1pL5BKdfzpX_5lX898UKHgRx88', //王婉馨
            'ox1pL5JjOWS16kSrTCecttcxh4-w', //秦康
            'ox1pL5Eby8bDE5pfxTIo5ivbjIPQ', //龚美倩
            'ox1pL5KeMxmOFxgr9kXD7aUmtkuw', //赖文健
            'ox1pL5N-2USpeF6usoSe66Ub6IR4', //窦秋芬
            'ox1pL5CLqvBzmq0EHLG76WbD9Q8g', //陈德钟
            'ox1pL5DFRJWRD9ywm_4q06LkdUYo', //孙慧敏
            'ox1pL5MpRaS1nAetsaFbej0Ya2sA', //王洪琼
            'ox1pL5MlXqg8NQFlCSo3kpj2emLo', //时菁蔓
            'ox1pL5G873JGVTQk5aOItO4KyNiE', //时光多吉
            'ox1pL5FCKV17vj1csOlO3sNWWfK0', //刘瑜
            'ox1pL5NRfVUsu2QLriAD_wj1iKic', //林虹
            'ox1pL5ESngO3lo6mQrEttQZmDf6Q', //徐可
            'ox1pL5FHKuXk_hWMWgTF1RJmXGBs', //毋瑞朋
            'ox1pL5If_GU05yKvGciYzo34MsgY', //潘青
            'ox1pL5Lrbq0RGq1AEHOGHP8v_2_s', //陈小晾
            'ox1pL5G_neDBfhknF8xmvZPMrtu0', //张晟
            'ox1pL5HxqBwEATm_8X4Ndow8nNIw', //李倩
            'ox1pL5KRU-9f3JmdAw8gyRnjhnXM', //朱立婉
            'ox1pL5PpdP3X1CYgprPjG0nkQ4v8' //张佳宇
        ];

        if (empty($openid)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }

        if (in_array($openid, $allowOpenid)) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => ['openallcourse' => '1']];
            return json($data);
        }

        $info = Db::name('course_record')
            ->where('open_id', $openid)
            ->field('one_status,two_status,three_status,four_status,five_status,six_status,seven_status')
            ->find();

        if (empty($info)) {
            $info = [
                'one_status'   => '0', //学习状态:0=未学习,1=学习，2=复习
                'two_status'   => '0',
                'three_status' => '0',
                'four_status'  => '0',
                'five_status'  => '0',
                'six_status'   => '0',
                'seven_status' => '0'
            ];
        }
        //查询用户是否是邀请码
        $invite = Db::name('user')->where('open_id', $openid)->field('type_way')->find();
        $info['type_way'] = $invite['type_way'];

        $find = Db::name('lock')->where('open_id', $openid)->find();
        if (!$find) {
            $loArr = [
                'one_lock' => 1,  //1是解锁  2是加锁
                'two_lock' => 2,
                'three_lock' => 2,
                'four_lock' => 2,
                'five_lock' => 2,
                'six_lock' => 2,
                'seven_lock' => 2,
            ];
        } else {
            $loArr['one_lock'] = 1;
            if ($find['one_etime']) {
                if (($find['one_etime'] + 7 * 24 * 3600) > time()) {
                    $loArr['two_lock'] = 2;
                } else {
                    $loArr['two_lock'] = 1;
                }
            } else {
                $loArr['two_lock'] = 2;
            }
            if ($find['two_etime']) {
                if (($find['two_etime'] + 7 * 24 * 3600) > time()) {
                    $loArr['three_lock'] = 2;
                } else {
                    $loArr['three_lock'] = 1;
                }
            } else {
                $loArr['three_lock'] = 2;
            }

            if ($find['three_etime']) {
                if (($find['three_etime'] + 7 * 24 * 3600) > time()) {
                    $loArr['four_lock'] = 2;
                } else {
                    $loArr['four_lock'] = 1;
                }
            } else {
                $loArr['four_lock'] = 2;
            }

            if ($find['four_etime']) {
                if (($find['four_etime'] + 7 * 24 * 3600) > time()) {
                    $loArr['five_lock'] = 2;
                } else {
                    $loArr['five_lock'] = 1;
                }
            } else {
                $loArr['five_lock'] = 2;
            }

            if ($find['five_etime']) {
                if (($find['five_etime'] + 7 * 24 * 3600) > time()) {
                    $loArr['six_lock'] = 2;
                } else {
                    $loArr['six_lock'] = 1;
                }
            } else {
                $loArr['six_lock'] = 2;
            }

            if ($find['six_etime']) {
                if (($find['six_etime'] + 7 * 24 * 3600) > time()) {
                    $loArr['seven_lock'] = 2;
                } else {
                    $loArr['seven_lock'] = 1;
                }
            } else {
                $loArr['seven_lock'] = 2;
            }
        }

        $info['lock'] = $loArr;

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $info];
        return json($data);
    }

    //返回之前记录的访问页面信息
    public function page_info()
    {
        $open_id = input('post.open_id');
        $course  = input('post.course');
        if (empty($open_id) || empty($course)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        //断点类型：cut_type 1=课前自评，2=课程部分，3=课程反馈，4=课前信息
        $info = Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->field('sex,self_time,cut_type,cut_info,status')->find();

        $infoanxi = Db::name('anxiety_info')->where('openid', $open_id)->order('etime', 'desc')->field('etime')->find();

        //默认为游客类型
        $list = [
            'invite' => '0'
        ];
        //判断该用户是否是邀请码
        $check_code = Db::name('user')->where(['open_id' => $open_id])->field('code')->find();
        if (!empty($check_code['code'])) {
            $list['invite'] = '1';
        }

        if (empty($info)) {
            $list['self_type'] = ''; //是否需要直接跳转到自评：1=不需要，2=需要
            $list['cut_type']  = ''; //断点类型：1=课前自评，2=课程部分，3=课程反馈，4=课前信息
            $list['cut_info']  = '';
            $list['sex'] = '0'; //性别：1=男，2=女，0=未知
            $list['type'] = '0';
        } else {
            if ($infoanxi['etime'] > 0) {
                if ($info['status'] == '1') {
                    //判断两周内是否做过抑郁和焦虑自评
                    if ($infoanxi['etime'] + 14 * 24 * 3600 < time()) {
                        $list['self_type'] = '2'; //是否需要直接跳转到自评：1=不需要，2=需要
                        $list['cut_type']  = '2';
                        $list['cut_info']  = '1-2';
                        $list['sex']       = $info['sex']; //性别：1=男，2=女，0=未知
                        $list['type'] = $info['status'];
                    } else {
                        $list['self_type'] = '1'; //是否需要直接跳转到自评：1=不需要，2=需要
                        $list['cut_type']  = $info['cut_type'];
                        $list['cut_info']  = $info['cut_info'];
                        $list['sex']       = $info['sex']; //性别：1=男，2=女，0=未知
                        $list['type'] = $info['status'];
                    }
                } else {
                    //判断两周内是否做过抑郁和焦虑自评
                    if ($infoanxi['etime'] + 14 * 24 * 3600 < time()) {
                        $list['self_type'] = '2'; //是否需要直接跳转到自评：1=不需要，2=需要
                        $list['cut_type']  = $info['cut_type'];
                        $list['cut_info']  = $info['cut_info'];
                        $list['sex']       = $info['sex']; //性别：1=男，2=女，0=未知
                        $list['type'] = $info['status'];
                    } else {
                        $list['self_type'] = '1'; //是否需要直接跳转到自评：1=不需要，2=需要
                        $list['cut_type']  = $info['cut_type'];
                        $list['cut_info']  = $info['cut_info'];
                        $list['sex']       = $info['sex']; //性别：1=男，2=女，0=未知
                        $list['type'] = $info['status'];
                    }
                }
            } else {
                $list['self_type'] = '1'; //是否需要直接跳转到自评：1=不需要，2=需要
                $list['cut_type']  = $info['cut_type'];
                $list['cut_info']  = $info['cut_info'];
                $list['sex']       = $info['sex']; //性别：1=男，2=女，0=未知
                $list['type'] = $info['status'];
            }
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $list];
        return json($data);
    }

    /*
     * 课前问卷-信息
     * */
    public function information()
    {
        $invite   = input('post.invite');
        $open_id  = input('post.open_id');
        $name     = input('post.name');
        $sex      = input('post.sex');
        $phone    = input('post.phone');
        $birthday = input('post.birthday');
        $age      = input('post.age');
        $nation   = input('post.nation');
        $census   = input('post.census');
        $stime    = input('post.stime');
        if (!isset($invite) || empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (empty($name) || empty($sex) || empty($phone) || empty($birthday) || !isset($age) || empty($nation) || empty($census) || empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (strpos($census, '省') !== false && strpos($census, '市') !== false) {
            $check_census = explode('省', $census);
            if (empty($check_census[0])) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                return json($data);
            }
            if (mb_strlen($check_census[1], 'utf8') == 1) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                return json($data);
            }
        }
        $info = [
            'openid'  => $open_id,
            'name'     => $name,
            'sex'      => $sex,
            'phone'    => $phone,
            'birthday' => strtotime($birthday),
            'age'      => $age,
            'nation'   => $nation,
            'census'   => $census,
            'stime'    => $stime,
            'etime'    => time(),
            'ltime'    => timediff(time(), $stime)
        ];

        if ($invite != '1') { //不是邀请码用户
            $education         = input('post.education');
            $job               = input('post.job');
            $birthplace        = input('post.birthplace');
            $live_time         = input('post.live_time');
            $housing_situation = input('post.housing_situation');
            $living_situation  = input('post.living_situation');
            $parent_live       = input('post.parent_live');
            $contacts          = input('post.contacts');
            $marriage          = input('post.marriage');
            $income            = input('post.income');
            $marriage_status   = input('post.marriage_status');
            $reason            = input('post.reason');
            if (empty($education) || empty($job) || empty($birthplace) || empty($live_time) || empty($housing_situation) || empty($living_situation) || empty($parent_live) || empty($marriage) || empty($income)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                return json($data);
            }

            if ($live_time != '土生土长') {
                if (empty($reason)) {
                    $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                    return json($data);
                }
            }
            if ($marriage != '未婚') {
                if (empty($marriage_status)) {
                    $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                    return json($data);
                }
            }

            $info['education'] = $education;
            $info['job'] = $job;
            $info['birthplace'] = $birthplace;
            $info['live_time'] = $live_time;
            $info['housing_situation'] = $housing_situation;
            $info['living_situation'] = $living_situation;
            $info['parent_live'] = $parent_live;
            $info['contacts'] = $contacts;
            $info['marriage'] = $marriage;
            $info['income'] = $income;
            $info['marriage_status'] = $marriage_status;
            $info['reason'] = $reason;
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //课前问卷-添加用户信息
            Db::name('user_info')->insertGetId($info);

            //判断该用户是否是游客
            $res = Db::name('user')->where('open_id', $open_id)->field('type,type_way')->find();


            if ($res['type'] == '0') { //游客用户
                //判断是否资料匹配
                $check = Db::name('user_code')->where(['name' => $name, 'phone' => $phone])->field('id,number,type')->find();
                if ($check) { //匹配则分类标记方式为资料匹配用户
                    $res =  Db::name('user')->where('open_id', $open_id)->update([
                        'name'         => $name,
                        'phone'        => $phone,
                        'user_code_id' => $check['id'],
                        'type'         => $check['type'],
                        'number'       => $check['number'],
                        'age'          => $age,
                        'sex'          => $sex,
                        'type_way'     => '2'
                    ]);
                } else {
                    Db::name('user')->where('open_id', $open_id)->update([
                        'name'         => $name,
                        'phone'        => $phone,
                        'age'          => $age,
                        'sex'          => $sex,
                    ]);
                }
            } else {
                Db::name('user')->where('open_id', $open_id)->update([
                    'age' => $age, 'sex' => $sex
                ]);
            }
            //判断是否存在
            $findRes = Db::name('course_break')->where('open_id', $open_id)->find();
            if (empty($findRes)) {
                switch ($sex) {
                    case '男':
                        $sex1 = 1;
                        break;
                    case '女':
                        $sex1 = 2;
                        break;
                }

                Db::name('course_break')->insertGetId([
                    'open_id' => $open_id,
                    'course' => 1,
                    'sex' => $sex1
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

    /*
    * 课前问卷-健康
    * */
    public function health()
    {
        $sex = input('post.sex'); //1=男，2=女
        $open_id = input('post.open_id');
        $height = input('post.height');
        $weight = input('post.weight');
        $smoke = input('post.smoke');
        $smoke_age = input('post.smoke_age');
        $smoke_date = input('post.smoke_date');
        $smoke_amount = input('post.smoke_amount');
        $drink = input('post.drink');
        $drink_age = input('post.drink_age');
        $drink_day = input('post.drink_day');
        $drink_bad_time = input('post.drink_bad_time');
        $drink_more = input('post.drink_more');
        $confirmed_disease = input('post.confirmed_disease');
        $sleep_time = input('post.sleep_time');
        $sleep_quality = input('post.sleep_quality');
        $sleeping_pills = input('post.sleeping_pills');
        $used_drug = input('post.used_drug');
        $want_suicide = input('post.want_suicide');
        $attempt_suicide = input('post.attempt_suicide');
        $suicide_plan = input('post.suicide_plan');
        $one_attempt_suicide = input('post.one_attempt_suicide');
        $depression = input('post.depression');
        $exercise_count = input('post.exercise_count');
        $exercise_duration = input('post.exercise_duration');
        $stime = input('post.stime');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (empty($sex) || empty($height) || empty($weight) || empty($smoke) || empty($drink) || empty($confirmed_disease) || empty($sleep_time) || empty($sleep_quality) || empty($sleeping_pills) || empty($want_suicide) || empty($attempt_suicide) || empty($suicide_plan) || empty($one_attempt_suicide) || empty($depression) || empty($exercise_count) || empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if ($smoke == '是') {
            if (empty($smoke_age) || empty($smoke_date) || empty($smoke_amount)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                return json($data);
            }
        } else {
            $smoke_age = '';
            $smoke_date = '';
            $smoke_amount = '';
        }
        if ($drink == '有') {
            if (empty($drink_age) || empty($drink_day) || empty($drink_bad_time) || empty($drink_more)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                return json($data);
            }
        }
        if ($sleeping_pills == '有') {
            if (empty($used_drug)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
                return json($data);
            }
        }
        if ($exercise_count != '无') {
            // if (empty($exercise_duration)) {
            //     $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            //     return json($data);
            // }
        }
        $info = [
            'openid'              => $open_id,
            'height'              => $height,
            'weight'              => $weight,
            'smoke'               => $smoke,
            'smoke_age'           => $smoke_age,
            'smoke_date'          => $smoke_date,
            'smoke_amount'        => $smoke_amount,
            'drink'               => $drink,
            'drink_age'           => $drink_age,
            'drink_day'           => $drink_day,
            'drink_bad_time'      => $drink_bad_time,
            'drink_more'          => $drink_more,
            'confirmed_disease'   => $confirmed_disease,
            'sleep_time'          => $sleep_time,
            'sleep_quality'       => $sleep_quality,
            'sleeping_pills'      => $sleeping_pills,
            'used_drug'           => $used_drug,
            'want_suicide'        => $want_suicide,
            'attempt_suicide'     => $attempt_suicide,
            'suicide_plan'        => $suicide_plan,
            'one_attempt_suicide' => $one_attempt_suicide,
            'depression'          => $depression,
            'exercise_count'      => $exercise_count,
            'exercise_duration'   => $exercise_duration,
            'stime'               => $stime,
            'etime'               => time(),
            'time'               => timediff(time(), $stime)
        ];

        if ($sex == '2') { //性别为女的用户
            $pregnancy_amount = input('post.pregnancy_amount');
            $childbirth_amount = input('post.childbirth_amount');
            // if ( empty($childbirth_amount)) {
            //     $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            //     return json($data);
            // }
            $info['pregnancy_amount'] = $pregnancy_amount;
            $info['childbirth_amount'] = $childbirth_amount;
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //课前问卷-添加用户健康
            Db::name('user_health_info')->insertGetId($info);
            //判断该用户是否是游客
            $res = Db::name('user')->where('open_id', $open_id)->field('type,type_way')->find();
            if ($res['type'] == '0') { //游客用户
                //判断是否资料匹配
                if ($depression == '是') { // 是，【升级】为患者-B1，分类标记方式填“问卷标记”
                    Db::name('user')->where('open_id', $open_id)->update(['type' => '5', 'type_way' => '5']);
                } elseif ($depression == '过去患病但已痊愈') { //过去患病已经痊愈，【升级】为缓解期-B2，分类标记方式填“问卷标记
                    Db::name('user')->where('open_id', $open_id)->update(['type' => '6', 'type_way' => '5']);
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

    /*
     * 课前问卷-自评-抑郁自评和焦虑自评提交
     * */
    public function self_depression()
    {
        $open_id  = input('post.open_id');
        $course   = input('post.course');
        $new      = input('post.new'); //学习状态：1=学习，2=复习
        //抑郁自评数据
        $C01      = input('post.C01');
        $C02      = input('post.C02');
        $C03      = input('post.C03');
        $C04      = input('post.C04');
        $C05      = input('post.C05');
        $C06      = input('post.C06');
        $C07      = input('post.C07');
        $C08      = input('post.C08');
        $C09      = input('post.C09');
        $c_stime  = input('post.c_stime');
        $c_etime  = input('post.c_etime');
        //焦虑自评数据
        $D01      = input('post.D01');
        $D02      = input('post.D02');
        $D03      = input('post.D03');
        $D04      = input('post.D04');
        $D05      = input('post.D05');
        $D06      = input('post.D06');
        $D07      = input('post.D07');
        if (empty($open_id) || empty($course) || empty($c_stime) || empty($c_etime) || empty($new)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (empty($C01) && $C01 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C02) && $C02 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C03) && $C03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C04) && $C04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C05) && $C05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C06) && $C06 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C07) && $C07 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C08) && $C08 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C09) && $C09 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }

        if (empty($D01) && $D01 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D02) && $D02 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D03) && $D03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D04) && $D04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D05) && $D05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D06) && $D06 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D07) && $D07 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        $CP = $C01 + $C02 + $C03 + $C04 + $C05 + $C06 + $C07 + $C08 + $C09;
        $DP = $D01 + $D02 + $D03 + $D04 + $D05 + $D06 + $D07;
        $depression = [
            'openid'  => $open_id,
            'course'  => $course,
            'C01'     => $C01,
            'C02'     => $C02,
            'C03'     => $C03,
            'C04'     => $C04,
            'C05'     => $C05,
            'C06'     => $C06,
            'C07'     => $C07,
            'C08'     => $C08,
            'C09'     => $C09,
            'CP'      => $CP,
            'stime'   => $c_stime,
            'etime'   => $c_etime,
            'date'    => strtotime(date('Y-m-d', $c_etime)),
            'ltime'   => timediff($c_etime, $c_stime)
        ];

        $anxiety = [
            'openid'   => $open_id,
            'course'   => $course,
            'D01'      => $D01,
            'D02'      => $D02,
            'D03'      => $D03,
            'D04'      => $D04,
            'D05'      => $D05,
            'D06'      => $D06,
            'D06'      => $D07,
            'DP'       => $DP,
            'stime'    => $c_etime,
            'etime'    => time(),
            'date'    => strtotime(date('Y-m-d', time())),
            'time'    => timediff(time(), $c_etime)
        ];
        if ($new == '1') { //如果是首次学习，进行标记
            $depression['state'] = $new;
            $anxiety['state']    = $new;
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交抑郁自评数据
            $dep_id = Db::name('depression_info')->insertGetId($depression);
            //提交焦虑自评数据
            $anxiety['dep_id'] = $dep_id;
            Db::name('anxiety_info')->insertGetId($anxiety);
            //查询该用户断点信息是否存在
            $info = Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->find();
            //将提交的时间同步到断点记录表
            if ($info) {
                //记录断点信息
                Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->update([
                    'self_time' => time()
                ]);
            } else {
                //查询该用户填写的性别信息
                $sex = Db::name('user')->where('open_id', $open_id)->value('sex');
                //记录断点信息
                Db::name('course_break')->insertGetId([
                    'open_id'  => $open_id,
                    'course'   => $course,
                    'sex'      => $sex,
                    'self_time' => time()
                ]);
            }
            //判断该用户是否是游客
            $res = Db::name('user')->where('open_id', $open_id)->field('type,type_way')->find();
            if ($res['type'] == '0') { //游客用户
                //判断抑郁测评总分
                if ($CP > 5 || $CP == 5) { // 抑郁自评总分≥5，【升级】至“高危-分数”分类，分类标记方式填“PHQ9分数”
                    Db::name('user')->where('open_id', $open_id)->update(['type' => '4', 'type_way' => '4']);
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

    /*
     * 课前问卷-自评-睡眠质量自评提交
     * */
    public function insomnia()
    {
        $open_id  = input('post.open_id');
        $course   = input('post.course');
        //睡眠质量自评数据
        $E01a     = input('post.E01a');
        $E01b     = input('post.E01b');
        $E01c     = input('post.E01c');
        $E02      = input('post.E02');
        $E03      = input('post.E03');
        $E04      = input('post.E04');
        $E05      = input('post.E05');
        $stime    = input('post.stime');

        if (empty($E01a) && $E01a != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($E01b) && $E01b != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($E01c) && $E01c != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($E02) && $E02 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($E03) && $E03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($E04) && $E04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($E05) && $E05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($open_id) || empty($course) || empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $EP = $E01a + $E01b + $E01c + $E02 + $E03 + $E04 + $E05;
        $insomnia = [
            'openid'  => $open_id,
            'course'  => $course,
            'E01a'    => $E01a,
            'E01b'    => $E01b,
            'E01c'    => $E01c,
            'E02'     => $E02,
            'E03'     => $E03,
            'E04'     => $E04,
            'E05'     => $E05,
            'EP'      => $EP,
            'stime'   => $stime,
            'etime'   => time(),
            'ltime'    => timediff(time(), $stime)
        ];
        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交睡眠质量自评数据
            Db::name('insomnia_info')->insertGetId($insomnia);
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

    /*
     * 课前问卷-自评-生活质量自评提交
     * */
    public function life_quality()
    {
        $open_id  = input('post.open_id');
        $course   = input('post.course');
        //生活质量自评数据
        $F01      = input('post.F01');
        $F02      = input('post.F02');
        $F03      = input('post.F03');
        $F04      = input('post.F04');
        $F05      = input('post.F05');
        $stime    = input('post.stime');

        if (empty($F01) && $F01 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($F02) && $F02 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($F03) && $F03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($F04) && $F04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($F05) && $F05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($open_id) || empty($course) || empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $FP = $F01 + $F02 + $F03 + $F04 + $F05;
        $life = [
            'openid'  => $open_id,
            'course'  => $course,
            'F01'     => $F01,
            'F02'     => $F02,
            'F03'     => $F03,
            'F04'     => $F04,
            'F05'     => $F05,
            'FP'      => $FP,
            'stime'   => $stime,
            'etime'   => time(),
            'ltime'    => timediff(time(), $stime)
        ];
        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交生活质量自评数据
            Db::name('life_quality_info')->insertGetId($life);
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

    /*
     * 课前问卷-自评-席汉功能损害自评提交
     * */
    public function depressive_effects()
    {
        $open_id  = input('post.open_id');
        $course   = input('post.course');
        //席汉功能损害自评数据
        $G01      = input('post.G01');
        $G02      = input('post.G02');
        $G03      = input('post.G03');
        $G04      = input('post.G04');
        $G05      = input('post.G05');
        $G06      = input('post.G06');
        $stime    = input('post.stime');
        //$etime    = input('post.etime');

        if (empty($G01) && $G01 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($G03) && $G03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($G04) && $G04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($G04) && $G05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($G04) && $G06 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }


        if (empty($G02)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($open_id) || empty($course) || empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $GP = $G01 + $G03 + $G04;
        $depressive_e = [
            'openid'  => $open_id,
            'course'  => $course,
            'G01'     => $G01,
            'G02'     => $G02,
            'G03'     => $G03,
            'G04'     => $G04,
            'G05'     => $G05,
            'G06'     => $G06,
            'GP'      => $GP,
            'stime'   => $stime,
            'etime'   => time(),
            'ltime'    => timediff(time(), $stime)
        ];

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交席汉功能损害自评数据
            Db::name('depressive_effects')->insertGetId($depressive_e);
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

    /*
     * 课前问卷-自评-躯体症状自评提交
     * */
    public function somatic_symptoms()
    {
        $open_id  = input('post.open_id');
        $course   = input('post.course');
        //躯体症状自评数据
        $H01      = input('post.H01');
        $H02      = input('post.H02');
        $H03      = input('post.H03');
        $H04      = input('post.H04');
        $H05      = input('post.H05');
        $H06      = input('post.H06');
        $H07      = input('post.H07');
        $H08      = input('post.H08');
        $H09      = input('post.H09');
        $H10      = input('post.H10');
        $H11      = input('post.H11');
        $H12      = input('post.H12');
        $H13      = input('post.H13');
        $H14      = input('post.H14');
        $H15      = input('post.H15');
        $H16      = input('post.H16');
        $H17      = input('post.H17');
        $H18      = input('post.H18');
        $H19      = input('post.H19');
        $H20      = input('post.H20');
        $H21      = input('post.H21');
        $H22      = input('post.H22');
        $H23      = input('post.H23');
        $H24      = input('post.H24');
        $H25      = input('post.H25');
        $H26      = input('post.H26');
        $H27      = input('post.H27');
        $H28      = input('post.H28');
        $stime    = input('post.stime');

        if (empty($open_id) || empty($course) || !isset($H01) || !isset($H02) || !isset($H03) || !isset($H04) || !isset($H05) || !isset($H06) || !isset($H07) || !isset($H08) || !isset($H09) || !isset($H10)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (!isset($H11) || !isset($H12) || !isset($H13) || !isset($H14) || !isset($H15) || !isset($H16) || !isset($H17) || !isset($H18) || !isset($H19) || !isset($H20) || !isset($H21) || !isset($H22) || !isset($H23) || !isset($H24)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (!isset($H25) || !isset($H26) || !isset($H27) || !isset($H28) || empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $HP = $H01 + $H02 + $H03 + $H04 + $H05 + $H06 + $H07 + $H08 + $H09 + $H10 + $H11 + $H12 + $H13 + $H14 + $H15 + $H16 + $H17 + $H18 + $H19 + $H20 + $H21 + $H22 + $H23 + $H24 + $H25 + $H26 + $H27 + $H28;
        $somatic = [
            'openid' => $open_id,
            'course'  => $course,
            'H01'     => $H01,
            'H02'     => $H02,
            'H03'     => $H03,
            'H04'     => $H04,
            'H05'     => $H05,
            'H06'     => $H06,
            'H07'     => $H07,
            'H08'     => $H08,
            'H09'     => $H09,
            'H10'     => $H10,
            'H11'     => $H11,
            'H12'     => $H12,
            'H13'     => $H13,
            'H14'     => $H14,
            'H15'     => $H15,
            'H16'     => $H16,
            'H17'     => $H17,
            'H18'     => $H18,
            'H19'     => $H19,
            'H20'     => $H20,
            'H21'     => $H21,
            'H22'     => $H22,
            'H23'     => $H23,
            'H24'     => $H24,
            'H25'     => $H25,
            'H26'     => $H26,
            'H27'     => $H27,
            'H28'     => $H28,
            'HP'      => $HP,
            'stime'   => $stime,
            'etime'   => time(),
            'ltime'    => timediff(time(), $stime)
        ];

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交躯体症状自评数据
            Db::name('somatic_symptoms')->insertGetId($somatic);


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

    /**
     * 课前问卷-自评-心理弹性自评提交
     *
     * @return void
     */
    public function psychological_elastic()
    {
        $open_id = get_data('open_id');
        $course = get_data('course');

        $I01 = get_data('I01');
        $I02 = get_data('I02');
        $I03 = get_data('I03');
        $I04 = get_data('I04');
        $I05 = get_data('I05');
        $I06 = get_data('I06');
        $I07 = get_data('I07');
        $I08 = get_data('I08');
        $I09 = get_data('I09');
        $I10 = get_data('I10');
        $I11 = get_data('I11');
        $I12 = get_data('I12');
        $I13 = get_data('I13');
        $I14 = get_data('I14');
        $I15 = get_data('I15');
        $I16 = get_data('I16');
        $I17 = get_data('I17');
        $I18 = get_data('I18');
        $I19 = get_data('I19');
        $I20 = get_data('I20');
        $I21 = get_data('I21');
        $I22 = get_data('I22');
        $I23 = get_data('I23');
        $I24 = get_data('I24');
        $I25 = get_data('I25');
        $stime = get_data('stime');

        $iArr = [$I01, $I02, $I03, $I04, $I05, $I06, $I07, $I08, $I09, $I10, $I11, $I12, $I13, $I14, $I15, $I16, $I17, $I18, $I19, $I20, $I21, $I22, $I23, $I24, $I25];

        validat_data([$open_id, $course, $stime], ReturnMsg::MISS_NECESSARY_PARA);

        isset_data($iArr, ReturnMsg::MISS_NECESSARY_PARA);

        $IP = array_sum($iArr);

        $toughArr = [$I11, $I12, $I13, $I14, $I15, $I16, $I17, $I18, $I19, $I20, $I21, $I22, $I23];
        $powerArr = [$I01, $I05, $I07, $I08, $I09, $I10, $I24, $I25];
        $optimisticArr = [$I02, $I03, $I04, $I06];

        $tough = array_sum($toughArr);
        $power = array_sum($powerArr);
        $optimistic = array_sum($optimisticArr);

        $inserArr = [
            'open_id' => $open_id,
            'course' => $course,
            'I01' => $I01,
            'I02' => $I02,
            'I03' => $I03,
            'I04' => $I04,
            'I05' => $I05,
            'I06' => $I06,
            'I07' => $I07,
            'I08' => $I08,
            'I09' => $I09,
            'I10' => $I10,
            'I11' => $I11,
            'I12' => $I12,
            'I13' => $I13,
            'I14' => $I14,
            'I15' => $I15,
            'I16' => $I16,
            'I17' => $I17,
            'I18' => $I18,
            'I19' => $I19,
            'I20' => $I20,
            'I21' => $I21,
            'I22' => $I22,
            'I22' => $I22,
            'I23' => $I23,
            'I24' => $I24,
            'I25' => $I25,
            'IP' => $IP,
            'tough' => $tough,
            'power' => $power,
            'optimistic' => $optimistic,
            'stime' => $stime,
            'etime' => time(),
            'ltime' => timediff(time(), $stime)
        ];

        $res = Db::name('psychological_elastic')->insertGetId($inserArr);

        if ($res) {
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } else {
            return_msg(ReturnCode::DB_SAVE_ERROR, ReturnMsg::FAIL);
        }
    }

    /**
     * 课前问卷-自评-抑郁思维模式提交
     *
     * @return void
     */
    public function thinking_pattern()
    {
        $open_id = get_data('open_id');
        $course = get_data('course');

        $J01 = get_data('J01');
        $J02 = get_data('J02');
        $J03 = get_data('J03');
        $J04 = get_data('J04');
        $J05 = get_data('J05');
        $J06 = get_data('J06');
        $J07 = get_data('J07');
        $J08 = get_data('J08');
        $J09 = get_data('J09');
        $J10 = get_data('J10');
        $J11 = get_data('J11');
        $J12 = get_data('J12');
        $J13 = get_data('J13');
        $J14 = get_data('J14');
        $J15 = get_data('J15');
        $J16 = get_data('J16');
        $J17 = get_data('J17');
        $J18 = get_data('J18');
        $J19 = get_data('J19');
        $J20 = get_data('J20');
        $J21 = get_data('J21');
        $J22 = get_data('J22');
        $J23 = get_data('J23');
        $J24 = get_data('J24');
        $J25 = get_data('J25');
        $J26 = get_data('J26');
        $J27 = get_data('J27');
        $J28 = get_data('J28');
        $J29 = get_data('J29');
        $J30 = get_data('J30');
        $stime = get_data('stime');

        $jArr = [$J01, $J02, $J03, $J04, $J05, $J06, $J07, $J08, $J09, $J10, $J11, $J12, $J13, $J14, $J15, $J16, $J17, $J18, $J19, $J20, $J21, $J22, $J23, $J24, $J25, $J26, $J27, $J28, $J29, $J30];

        validat_data([$open_id, $course, $stime], ReturnMsg::MISS_NECESSARY_PARA);
        isset_data($jArr, ReturnMsg::MISS_NECESSARY_PARA);

        $JP = array_sum($jArr);

        $individualArr = [$J26, $J20, $J07, $J14, $J10];
        $negativeArr = [$J28, $J23, $J24, $J09, $J21, $J03, $J02];
        $self_confidenceArr = [$J18, $J17];
        $HelplessArr = [$J30, $J29];

        $individual = array_sum($individualArr);
        $negative = array_sum($negativeArr);
        $self_confidence = array_sum($self_confidenceArr);
        $Helpless = array_sum($HelplessArr);

        $inserArr = [
            'open_id' => $open_id,
            'course' => $course,
            'J01' => $J01,
            'J02' => $J02,
            'J03' => $J03,
            'J04' => $J04,
            'J05' => $J05,
            'J06' => $J06,
            'J07' => $J07,
            'J08' => $J08,
            'J09' => $J09,
            'J10' => $J10,
            'J11' => $J11,
            'J12' => $J12,
            'J13' => $J13,
            'J14' => $J14,
            'J15' => $J15,
            'J16' => $J16,
            'J17' => $J17,
            'J18' => $J18,
            'J19' => $J19,
            'J20' => $J20,
            'J21' => $J21,
            'J22' => $J22,
            'J23' => $J23,
            'J24' => $J24,
            'J25' => $J25,
            'J26' => $J26,
            'J27' => $J27,
            'J28' => $J28,
            'J29' => $J29,
            'J30' => $J30,
            'JP' => $JP,
            'individual' => $individual,
            'negative' => $negative,
            'self_confidence' => $self_confidence,
            'Helpless' => $Helpless,
            'stime' => $stime,
            'etime' => time(),
            'ltime' => timediff(time(), $stime)
        ];

        $res = Db::name('thinking_pattern')->insertGetId($inserArr);
        if ($res) {
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } else {
            return_msg(ReturnCode::DB_SAVE_ERROR, ReturnMsg::FAIL);
        }
    }

    /*课程开始*/
    public function course_start()
    {
        $open_id = input('post.open_id');
        $content_start = input('post.content_start');
        $course = input('post.course');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        if (empty($open_id) || empty($course) || empty($new) || empty($content_start)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //添加学习记录表开始信息
            Db::name('course_info')->insert([
                'open_id' => $open_id,
                'course'  => $course,
                'content_start' => $content_start,
                'new' => $new,
                'stime'   => time()
            ]);
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

    /*S1-问题清单*/
    public function problem_list()
    {
        $open_id = input('post.open_id');
        $milieu = input('post.milieu');
        $mood = input('post.mood');
        $phy_per = input('post.phy_per');
        $action = input('post.action');
        $thinking = input('post.thinking');
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($milieu)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写环境/生活变化/情境', 'data' => []];
                return json($data);
            }
            if (empty($mood)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写情绪', 'data' => []];
                return json($data);
            }
            if (empty($phy_per)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写生理表现', 'data' => []];
                return json($data);
            }
            if (empty($action)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写行动', 'data' => []];
                return json($data);
            }
            if (empty($thinking)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写思维', 'data' => []];
                return json($data);
            }
        }
        $list  = [
            'open_id'  => $open_id,
            'milieu'   => $milieu,
            'mood'     => $mood,
            'phy_per'  => $phy_per,
            'action'   => $action,
            'thinking' => $thinking,
            'new'      => $new,
            'type'     => $type,
            'stime'    => $stime,
            'etime'    => time(),
            'ltime'    => timediff($stime, time())
        ];

        $arr_find_a = Db::name('one_course')->where(['open_id' => $open_id, 'new' => 1])->field('id')->find();

        if ($arr_find_a) {
            $info = Db::name('one_course')->where('id', $arr_find_a['id'])->update($list);
        } else {
            $info = Db::name('one_course')->insertGetId($list);
        }

        if ($info) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    /*S2-S1问题清单展示*/
    public function problem_info()
    {
        $open_id = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $infos = [];
        $info = Db::name('one_course')->where(['open_id' => $open_id, 'new' => '1'])->withoutField(['id', 'open_id', 'new', 'stime', 'etime', 'ltime'])->find();
        if (!empty($info['milieu']) && $info['milieu'] !== '无') {
            $infos[] = $info['milieu'];
        }
        if (!empty($info['mood']) && $info['mood'] !== '无') {
            $infos[] = $info['mood'];
        }
        if (!empty($info['phy_per']) && $info['phy_per'] !== '无') {
            $infos[] = $info['phy_per'];
        }
        if (!empty($info['action']) && $info['action'] !== '无') {
            $infos[] = $info['action'];
        }
        if (!empty($info['thinking']) && $info['thinking'] !== '无') {
            $infos[] = $info['thinking'];
        }
        if ($info) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $info];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    /*S2-S1目标清单提交*/
    public function target_list()
    {
        $open_id = input('post.open_id');
        $problem = input('post.problem');
        $main_target = input('post.main_target');
        $specific_goals = input('post.specific_goals');
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($problem)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写问题清单', 'data' => []];
                return json($data);
            }
            if (empty($main_target)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写总目标', 'data' => []];
                return json($data);
            }
            if (empty($specific_goals)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写具体目标', 'data' => []];
                return json($data);
            }
        }

        $list  = [
            'open_id'        => $open_id,
            'problem'        => $problem,
            'main_target'    => $main_target,
            'specific_goals' => $specific_goals,
            'new'      => $new,
            'type'     => $type,
            'stime'    => $stime,
            'etime'    => time(),
            'ltime'    => timediff($stime, time())
        ];
        $arr_find_a = Db::name('target_list')->where(['open_id' => $open_id, 'new' => 1])->field('id')->find();

        if ($arr_find_a) {
            $info = DB::name('target_list')->where('id', $arr_find_a['id'])->update($list);
        } else {
            $info = Db::name('target_list')->insertGetId($list);
        }
        if ($info) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    /*S2-活动记录提交*/
    public function activity_record()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $source = input('post.source/a', array());
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($source) || !is_array($source)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写活动记录', 'data' => []];
                return json($data);
            }
        }
        if (!empty($source)) {
            $list = [];
            foreach ($source as $key => $value) {
                for ($i = 0; $i < 24; $i++) {
                    if ($i == $value['time']) {
                        $list[$i]  = [
                            'open_id'     => $open_id,
                            'date'        => $value['date'],
                            'time'        => $value['time'],
                            'activity'    => $value['activity'],
                            'pleasure'    => $value['pleasure'],
                            'achievement' => $value['achievement'],
                            'week'        => getTimeWeek($value['date']),
                            'new'         => $new,
                            'type'        => $type,
                            'stime'       => $stime,
                            'etime'       => time(),
                            'ltime'       => timediff($stime, time())
                        ];
                    } else {
                        if (empty($list[$i]['activity'])) {
                            $list[$i] = [
                                'open_id'     => $open_id,
                                'date'        => $value['date'],
                                'time'        => $i,
                                'activity'    => '',
                                'pleasure'    => 0,
                                'achievement' => 0,
                                'week'        => getTimeWeek($value['date']),
                                'new'         => $new,
                                'type'        => $type,
                                'stime'       => $stime,
                                'etime'       => time(),
                                'ltime'       => timediff($stime, time())
                            ];
                        }
                    }
                }
            }
        } else {
            for ($i = 0; $i < 24; $i++) {
                $list[]  = [
                    'open_id'     => $open_id,
                    'time'        => $i,
                    'activity'    => '',
                    'pleasure'    => 0,
                    'achievement' => 0,
                    'new'         => $new,
                    'type'        => $type,
                    'stime'       => $stime,
                    'etime'       => time(),
                    'ltime'       => timediff($stime, time())
                ];
            }
        }

        $arr_find_a = Db::name('activity_record')->where(['open_id' => $open_id, 'new' => 1])->field('id')->select()->toArray();

        try {
            Db::transaction(function () use ($arr_find_a, $list) {
                if ($arr_find_a) {
                    foreach ($arr_find_a as $key => $value) {
                        Db::name('activity_record')->delete($value['id']);
                    }
                }
                Db::name('activity_record')->insertAll($list);
            });

            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } catch (\Exception $e) {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    /*S2-自动思维提交*/
    public function auto_think()
    {
        $open_id = input('post.open_id');
        $situation = input('post.situation') ?: '';
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $mood = input('post.mood/a', array());
        $think = input('post.think/a', array());
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($situation)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写情境', 'data' => []];
                return json($data);
            }
            if (empty($mood)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写情绪', 'data' => []];
                return json($data);
            }
            if (empty($think)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写自动思维', 'data' => []];
                return json($data);
            }
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交自动思维主表
            $list = [
                'open_id'     => $open_id,
                'situation'   => $situation,
                'new'         => $new,
                'type'        => $type,
                'stime'       => $stime,
                'etime'       => time(),
                'ltime'       => timediff($stime, time())
            ];

            $arr_find_a = Db::name('auto_thinking')->where(['open_id' => $open_id, 'new' => 1])->field('id')->find();
            if ($arr_find_a && $new == 1) {
                Db::name('think_mood')->where('at_id', $arr_find_a['id'])->delete();
                Db::name('think_think')->where('at_id', $arr_find_a['id'])->delete();
                Db::name('auto_thinking')->delete($arr_find_a['id']);
            }
            $id = Db::name('auto_thinking')->insertGetId($list);
            //提交情绪记录表数据
            if (!empty($mood)) {
                foreach ($mood as $key => $value) {
                    $mood[$key]['at_id'] = $id;
                }
            } else {
                $mood = [
                    [
                        'mood'     => '',
                        'fraction' => 0,
                        'at_id'    => $id,
                    ]
                ];
            }
            Db::name('think_mood')->insertAll($mood);
            //提交思维记录表数据
            if (!empty($think)) {
                foreach ($think as $k => $v) {
                    $think[$k]['at_id'] = $id;
                }
            } else {
                $think = [
                    [
                        'think'    => '',
                        'fraction' => 0,
                        'at_id'    => $id,
                    ]
                ];
            }
            Db::name('think_think')->insertAll($think);
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

    /*S3-S2活动记录展示*/
    public function activity_record_info()
    {
        $open_id = input('post.open_id');
        //$open_id = 'ox1pL5F7O0_pnV-bFumEHQLBm7vY';
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $infos = [];
        $time = strtotime(date('Y-m-d', time())) - 7 * 24 * 3600;
        $info = Db::name('activity_record')->where(['open_id' => $open_id,  'date' => ['>=', $time], 'type' => '2'])->group('date')->field('date')->select()->toArray();

        foreach ($info as $key => $value) {
            $activity_record_info = Db::name('activity_record')->where(['open_id' => $open_id, 'date' => $value['date'], 'type' => '2', 'activity' => ['<>', '']])->field('date,time,activity,pleasure,achievement')->select()->toArray();
            foreach ($activity_record_info as $k => $v) {
                $activity_record_info[$k]['date'] = date('Y.m.d', $v['date']) . ' ' . $v['time'] . '-' . ($v['time'] + 1) . '点';
            }
            for ($i = 0; $i < 7; $i++) {
                $dates = $time + 24 * 3600 * $i;
                $week = getTimeWeek($dates);
                if ($dates == $value['date']) {
                    $infos[$i]['date'] = date('Y-m-d', $dates);
                    $infos[$i]['week'] = $week;
                    $infos[$i]['info'] = $activity_record_info;
                } else {
                    if (empty($infos[$i]['info'])) {
                        $infos[$i]['date'] = date('Y-m-d', $dates);
                        $infos[$i]['week'] = $week;
                        $infos[$i]['info'] = [];
                    }
                }
            }
        }

        if (empty($info)) {
            for ($i = 0; $i < 7; $i++) {
                $dates = $time + 24 * 3600 * $i;
                $week = getTimeWeek($dates);
                $infos[$i]['date'] = date('Y-m-d', $dates);
                $infos[$i]['week'] = $week;
                $infos[$i]['info'] = [];
            }
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $infos];

        return json($data);
    }

    /*S3-S2活动记录回答*/
    public function activity_reply()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $type = input('post.type'); //1=课程，2=练习
        $bad_time = input('post.bad_time') ?: '';
        $bad_do_what = input('post.bad_do_what') ?: '';
        $good_time = input('post.good_time') ?: '';
        $good_do_what = input('post.good_do_what/a', array());
        $good_activity = input('post.good_activity/a', array());
        $want_activity = input('post.want_activity/a', array());
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($bad_time) || empty($bad_do_what) || empty($good_time) || empty($good_do_what) || empty($good_activity) || empty($want_activity)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写完整的信息', 'data' => []];
                return json($data);
            }
        }

        $aa =  implode('||', $good_do_what);

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交S3-活动回答记录
            $list = [
                'open_id'        => $open_id,
                'bad_time'       => $bad_time,
                'bad_do_what'    => $bad_do_what,
                'good_time'      => $good_time,
                'good_do_what'   => implode('||', $good_do_what),
                'good_activity'  => implode('||', $good_activity),
                'want_activity'  => implode('||', $want_activity),
                'new'         => $new,
                'type'        => $type,
                'stime'       => $stime,
                'etime'       => time(),
                'ltime'       => timediff($stime, time())
            ];
            $arr_find_a = Db::name('activity_answer')->where(['open_id' => $open_id, 'new' => 1])->field('id')->find();
            if ($arr_find_a) {
                Db::name('activity_answer')->where('id', $arr_find_a['id'])->update($list);
                Db::name('user_activity_keys')->where('open_id', $open_id)->delete();
            } else {
                Db::name('activity_answer')->insertGetId($list);
            }

            //提交百宝箱
            $info = [];
            if (!empty($good_do_what)) {
                foreach ($good_do_what as $value) {
                    $info[] = [
                        'open_id' => $open_id,
                        'activity' => $value,
                        'stime' => time()
                    ];
                }
            }
            if (!empty($good_activity)) {
                foreach ($good_activity as $va) {
                    $info[] = [
                        'open_id' => $open_id,
                        'activity' => $va,
                        'stime' => time()
                    ];
                }
            }
            if (!empty($want_activity)) {
                foreach ($want_activity as $v) {
                    $info[] = [
                        'open_id' => $open_id,
                        'activity' => $v,
                        'stime' => time()
                    ];
                }
            }

            Db::name('user_activity_keys')->insertAll($info);
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

    /*S3-百宝箱展示*/
    public function user_activity_keys()
    {
        $open_id = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $info = Db::name('user_activity_keys')->where(['open_id' => $open_id])->where('activity', '<>', '')->field('activity')->select()->toArray();
        if ($info) {
            $infoArr = [];
            foreach ($info as $k => $v) {
                $infoArr[]  = $v['activity'];
            }
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $infoArr];
        } else {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
        }
        return json($data);
    }



    /*S3-百宝箱修改提交*/
    public function edit_activity_keys()
    {
        $open_id = input('post.open_id');
        $activity = input('post.activity/a', array());
        $stime = input('post.stime');

        validat_data([$open_id, $stime], ReturnMsg::MISS_NECESSARY_PARA);
        validat_data([$activity], '活动' . ReturnMsg::NOT_EMPTY);

        $all =  Db::name('user_activity_keys')->where('open_id', $open_id)->field('activity')->select()->toArray();

        foreach ($all as $k => $v) {
            foreach ($activity as $k1 => $v2) {
                if ($v['activity'] == $v2) {
                    unset($activity[$k1]);
                }
            }
        }
        $iArr = [];
        foreach ($activity as $v) {
            $iArr[] = [
                'open_id' => $open_id,
                'activity' => $v,
                'stime' => $stime
            ];
        }
        // 启动事务
        Db::startTrans();
        try {
            Db::name('user_activity_keys')->insertAll($iArr);
            // 提交事务
            Db::commit();
            return_msg(ReturnCode::SUCCESS, '成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, '失败');
    }

    /*S3-活动安排*/
    public function activity_plan()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $type = input('post.type'); //1=课程，2=练习
        $source = input('post.source/a', array());
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($source)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写活动安排', 'data' => []];
                return json($data);
            }
        }

        $stfind = Db::name('activity_plan')->where(['open_id' => $open_id, 'stime' => $stime])->field('id')->find();

        if ($stfind) {
            return_msg(ReturnCode::INVALID, '请返回列表重新添加');
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交活动安排
            $info = [];
            if (!empty($source)) {
                foreach ($source as $key => $value) {
                    $info[$key]['open_id']  = $open_id;
                    $info[$key]['date']     = $value['date'];
                    $info[$key]['activity'] = $value['activity'];
                    $info[$key]['step'] = $value['step'];
                    $info[$key]['week']     = getTimeWeek($value['date']);
                    $info[$key]['new']      = $new;
                    $info[$key]['type']     = $type;
                    $info[$key]['stime']    = $stime;
                    $info[$key]['etime']    = time();
                    $info[$key]['ltime']    = timediff($stime, time());
                }

                $arr_find_a = Db::name('activity_plan')->where(['open_id' => $open_id, 'new' => 1])->field('id')->select()->toArray();
                if ($arr_find_a) {
                    foreach ($arr_find_a as $key => $value) {
                        Db::name('activity_plan')->delete($value['id']);
                    }
                }
                Db::name('activity_plan')->insertAll($info);
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

    /*S3-活动安排展示*/
    public function activity_plan_list()
    {
        $open_id = input('post.open_id');
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        //查询最后一次提交的时间
        $infos = [];
        $time = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
        $info = Db::name('activity_plan')->where(['open_id' => $open_id, 'type' => $type])->group('etime')->field('stime,etime')->order('etime desc')->find();

        //查询该时间提交下的活动
        $activitys = Db::name('activity_plan')->where(['open_id' => $open_id, 'type' => $type, 'stime' => $info['stime'], 'etime' => $info['etime']])->field('id,date,week,activity,step')->select()->toArray();

        foreach ($time as $k => $v) {
            $list = [];
            $i = 0;
            foreach ($activitys as $value) {
                if ($value['week'] == $v) {
                    $list[$i]['date'] = date('Y.m.d H:i', $value['date']);
                    $list[$i]['activity'] = $value['activity'];
                    $list[$i]['step'] = $value['step'];
                    $i++;
                }
            }
            $infos[$k]['week'] = $v;
            $infos[$k]['list'] = $list;
        } //dump($infos);die;

        if ($infos) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $infos];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }


    /*S3-S2自动思维展示*/
    public function auto_think_info()
    {
        $open_id = input('post.open_id');
        //$open_id = 'ox1pL5F7O0_pnV-bFumEHQLBm7vY';
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        //判断是否在S2学习时候填写了自动思维
        $auto_thinking = Db::name('auto_thinking')->where(['open_id' => $open_id, 'new' => 1, 'type' => 1, 'course' => 2])->field('id,situation')->find();

        if ($auto_thinking) {
            //查询自动思维表-情绪记录
            $auto_mood = Db::name('think_mood')->where(['at_id' => $auto_thinking['id']])->field('mood,fraction')->select()->toArray();
            $auto_thinking['mood'] = $auto_mood;

            //查询自动思维-思维记录
            $think_think = Db::name('think_think')->where(['at_id' => $auto_thinking['id']])->field('think,fraction')->select()->toArray();
            $auto_thinking['think'] = $think_think;
        } else {
            $auto_thinking = [];
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $auto_thinking];

        return json($data);
    }

    /*S3-识别思维误区提交*/
    public function identify_misunderstanding()
    {
        $open_id = input('post.open_id');
        $situation = input('post.situation') ?: '';
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $mood = input('post.mood/a', array());
        $think = input('post.think/a', array());
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($situation)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写情境', 'data' => []];
                return json($data);
            }
            if (empty($mood)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写情绪', 'data' => []];
                return json($data);
            }
            if (empty($think)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写自动思维及思维误区', 'data' => []];
                return json($data);
            }
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交自动思维主表
            $list = [
                'open_id'     => $open_id,
                'course'      => 3,
                'situation'   => $situation,
                'new'         => $new,
                'type'        => $type,
                'stime'       => $stime,
                'etime'       => time(),
                'ltime'       => timediff($stime, time())
            ];

            $arr_find_a = Db::name('auto_thinking')->where(['open_id' => $open_id, 'new' => 1])->find();
            if ($arr_find_a) {
                Db::name('think_mood')->where('at_id', $arr_find_a['id'])->delete();
                Db::name('think_think')->where('at_id', $arr_find_a['id'])->delete();
                Db::name('auto_thinking')->delete($arr_find_a['id']);
            }

            $id = Db::name('auto_thinking')->insertGetId($list);
            //提交情绪记录表数据
            if (!empty($mood)) {
                foreach ($mood as $key => $value) {
                    $mood[$key]['at_id'] = $id;
                }
            } else {
                $mood = [
                    [
                        'mood'     => '',
                        'fraction' => 0,
                        'at_id'    => $id,
                    ]
                ];
            }
            Db::name('think_mood')->insertAll($mood);
            //提交思维记录表数据
            if (!empty($think)) {
                foreach ($think as $k => $v) {
                    $think[$k]['at_id'] = $id;
                }
            } else {
                $think = [
                    [
                        'think'    => '',
                        'fraction' => 0,
                        'at_id'    => $id,
                    ]
                ];
            }
            Db::name('think_think')->insertAll($think);
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

    /*S3-识别思维误区比率提交*/
    public function misunderstanding_ratio()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $think = input('post.think/a', array());
        $type = input('post.type'); //1=课程，2=练习
        if (empty($open_id) || empty($new) || empty($stime) || empty($type)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if ($type == 2) {
            if (empty($think)) {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写思维误区及百分比', 'data' => []];
                return json($data);
            }
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交自动思维主表
            $list = [
                'open_id'     => $open_id,
                'new'         => $new,
                'type'        => $type,
                'stime'       => $stime,
                'etime'       => time(),
                'ltime'       => timediff($stime, time())
            ];

            $arr_find_a = Db::name('misunderstanding_ratio')->where(['open_id' => $open_id, 'new' => 1])->find();
            if ($arr_find_a) {
                Db::name('misunderstanding_info')->where('misun_id', $arr_find_a['id'])->delete();
                Db::name('misunderstanding_ratio')->delete($arr_find_a['id']);
            }

            $id = Db::name('misunderstanding_ratio')->insertGetId($list);
            //提交思维误区及百分比
            if (!empty($think)) {
                foreach ($think as $k => $v) {
                    $think[$k]['misun_id'] = $id;
                }
            } else {
                $think = [
                    [
                        'think_error' => '',
                        'ratio'       => '',
                        'misun_id'    => $id,
                    ]
                ];
            }
            Db::name('misunderstanding_info')->insertAll($think);
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

    /*课程完成*/
    public function course_end()
    {
        $open_id = input('post.open_id');
        $course = input('post.course');
        $cut_type = input('post.cut_type'); //断点类型：1=课前自评，2=课程部分，3=课程反馈，4=课前信息
        $content_end = input('post.content_end');
        $type = input('post.type'); //类型：1=完成，2=中断
        $new = input('post.new'); //学习状态：1=学习，2=复习
        $data_a = Request::param();

        if (empty($open_id) || empty($type) || empty($course) || empty($cut_type) || empty($new)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            // 先判断是多少节课
            switch ($course) {
                case 1:
                    $table = 'one_click';
                    break;
                case 2:
                    $table = 'two_click';
                    break;
                case 3:
                    $table = 'three_click';
                    break;
                case 4:
                    $table = 'four_click';
                    break;
                case 5:
                    $table = 'five_click';
                    break;
                case 6:
                    $table = 'six_click';
                    break;
                case 7:
                    $table = 'seven_click';
                    break;
                default:
                    $k = 0;
            }

            //查询该用户断点信息是否存在
            $info = Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->field('id')->find();
            if ($info) {
                if ($cut_type == 3) {
                    //记录断点信息
                    Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->update([
                        'cut_type' => $cut_type,
                        'cut_info' => $content_end,
                        'status' => $type,
                        'cut_time' => time(),
                    ]);
                } else {

                    if ((($course == 1 && $content_end == '3-1') || ($course == 2 && $content_end == '4-0') || ($course == 3 && $content_end == '4-0') || ($course == 4 && $content_end == '3-0') || ($course == 5 && $content_end == '3-0') || ($course == 6 && $content_end == '3-0') || ($course == 7 && $content_end == '2-1')) && isset($data_a['endflag'])) {
                        //记录断点信息
                        Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->update([
                            'cut_type' => $cut_type,
                            'cut_info' => '10-0',
                            'status' => $type,
                            'cut_time' => time(),
                        ]);
                    } else {
                        //记录断点信息
                        Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->update([
                            'cut_type' => $cut_type,
                            'cut_info' => $content_end,
                            'status' => $type,
                            'cut_time' => time(),
                        ]);
                    }
                }
            } else {
                if ($cut_type == 3) {
                    //记录断点信息
                    Db::name('course_break')->insertGetId([
                        'open_id'  => $open_id,
                        'course'   => $course,
                        'cut_type' => $cut_type,
                        'status' => $type,
                        'cut_info' => $content_end,
                        'cut_time' => time(),
                    ]);
                } else {
                    if ((($course == 1 && $content_end == '3-1') || ($course == 2 && $content_end == '4-0') || ($course == 3 && $content_end == '4-0') || ($course == 4 && $content_end == '3-0') || ($course == 5 && $content_end == '3-0') || ($course == 6 && $content_end == '3-0') || ($course == 7 && $content_end == '2-1')) && isset($data_a['endflag'])) {
                        //记录断点信息
                        Db::name('course_break')->insertGetId([
                            'open_id'  => $open_id,
                            'course'   => $course,
                            'cut_type' => $cut_type,
                            'status' => $type,
                            'cut_info' => '10-0',
                            'cut_time' => time(),
                        ]);
                    } else {
                        //记录断点信息
                        Db::name('course_break')->insertGetId([
                            'open_id'  => $open_id,
                            'course'   => $course,
                            'cut_type' => $cut_type,
                            'status' => $type,
                            'cut_info' => $content_end,
                            'cut_time' => time(),
                        ]);
                    }
                }
            }
            //判断是否是课程部分提交
            if ($cut_type == '2') {
                //查询是否存在这条学习开始记录
                $course_info = Db::name('course_info')->where(['open_id' => $open_id, 'course' => $course, 'type' => '0'])->field('id,content_start,stime')->order('id desc')->find();
                if ($course_info) {
                    if ((($course == 1 && $content_end == '3-1') || ($course == 2 && $content_end == '4-0') || ($course == 3 && $content_end == '4-0') || ($course == 4 && $content_end == '3-0') || ($course == 5 && $content_end == '3-0') || ($course == 6 && $content_end == '3-0') || ($course == 7 && $content_end == '2-1')) && isset($data_a['endflag'])) {
                        //记录学习记录表结束信息
                        Db::name('course_info')->where(['id' => $course_info['id']])->update([
                            'content_end' => '10-0',
                            'type'        => $type,
                            'etime'       => time(),
                            'long_time'   => timediff(time(), $course_info['stime']),
                            'ltime'       => time() - $course_info['stime']
                        ]);

                        //记录点击数
                        if ($course_info['content_start'] != $content_end) {
                            click($table, $type, $open_id, $course, $course_info['content_start'], $content_end, 'end');
                        }
                    } else {
                        //记录学习记录表结束信息
                        Db::name('course_info')->where(['id' => $course_info['id']])->update([
                            'content_end' => $content_end,
                            'type'        => $type,
                            'etime'       => time(),
                            'long_time'   => timediff(time(), $course_info['stime']),
                            'ltime'       => time() - $course_info['stime']
                        ]);
                        //记录点击数
                        if ($course_info['content_start'] != $content_end) {
                            click($table, $type, $open_id, $course, $course_info['content_start'], $content_end, $new);
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
        }
        $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        return json($data);
    }

    /*分享记录*/
    public function share()
    {
        $open_id = input('post.open_id');
        $course = input('post.course');
        if (empty($open_id) || empty($course)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $row = Db::name('course_info')->where(['open_id' => $open_id, 'course' => $course, 'type' => '0'])->update(['share' => 1]);

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '转发成功', 'data' => $row];
        return json($data);
    }

    /*问题反馈*/
    public function feedback()
    {
        $open_id = input('post.open_id');
        $course = input('post.course');
        $params = input('post.params');
        $stime = input('post.stime');
        $etime = input('post.etime');

        if (empty($open_id) || empty($course) || empty($params) || empty($stime) || empty($etime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $params_arr  = explode(',', $params);

        if ($params_arr[5] == '无') {

            $list  = [
                'open_id'  => $open_id,
                'course'   => $course,
                'Q1'       => $params_arr[0],
                'Q2'       => $params_arr[1],
                'Q3'       => $params_arr[2],
                'Q4'       => $params_arr[3],
                'Q5'       => $params_arr[4],
                'Q6'       => $params_arr[5],
                'stime'    => $stime,
                'etime'    => $etime,
                'ltime'    => timediff($etime, $stime)
            ];
        } else {
            $list  = [
                'open_id'  => $open_id,
                'course'   => $course,
                'Q1'       => $params_arr[0],
                'Q2'       => $params_arr[1],
                'Q3'       => $params_arr[2],
                'Q4'       => $params_arr[3],
                'Q5'       => $params_arr[4],
                'question' => $params_arr[5],
                'stime'    => $stime,
                'etime'    => $etime,
                'ltime'    => timediff($etime, $stime)
            ];
        }



        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //先判断是多少节课
            switch ($course) {
                case 1:
                    $key = 'one_status';
                    $table = 'one_click';
                    break;
                case 2:
                    $key = 'two_status';
                    $table = 'two_click';
                    break;
                case 3:
                    $key = 'three_status';
                    $table = 'three_click';
                    break;
                case 4:
                    $key = 'four_status';
                    $table = 'four_click';
                    break;
                case 5:
                    $key = 'five_status';
                    $table = 'five_click';
                    break;
                case 6:
                    $key = 'six_status';
                    $table = 'six_click';
                    break;
                case 7:
                    $key = 'seven_status';
                    $table = 'seven_click';
                    break;
                default:
                    $k = 0;
            }
            //提交数据
            Db::name('feedback')->insertGetId($list);
            //添加点击次数
            Db::name($table)->where(['open_id' => $open_id, 'course' => $course])->inc('feedback');
            //修改学习状态
            $check = Db::name('course_record')->where(['open_id' => $open_id])->find();
            if ($check) {
                Db::name('course_record')->where(['open_id' => $open_id])->update([$key => '2']);
            } else {
                Db::name('course_record')->insertGetId(['open_id' => $open_id, $key => '2', 'createtime' => time()]);
            }
            //修改断点状态为完成状态
            Db::name('course_break')->where(['open_id' => $open_id, 'course' => $course])->update([
                'status' => '1',
                'cut_time' => time(),
            ]);
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

    /*
     * 首页心情记录
     * */
    public function mood_record()
    {
        $open_id  = input('post.open_id');
        //抑郁自评数据
        $C01      = input('post.C01');
        $C02      = input('post.C02');
        $C03      = input('post.C03');
        $C04      = input('post.C04');
        $C05      = input('post.C05');
        $C06      = input('post.C06');
        $C07      = input('post.C07');
        $C08      = input('post.C08');
        $C09      = input('post.C09');
        $c_stime  = input('post.c_stime');
        $c_etime  = input('post.c_etime');
        //焦虑自评数据
        $D01      = input('post.D01');
        $D02      = input('post.D02');
        $D03      = input('post.D03');
        $D04      = input('post.D04');
        $D05      = input('post.D05');
        $D06      = input('post.D06');
        $D07     = input('post.D07');
        if (empty($open_id) || empty($c_stime) || empty($c_etime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (empty($C01) && $C01 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C02) && $C02 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C03) && $C03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C04) && $C04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C05) && $C05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C06) && $C06 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C07) && $C07 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C08) && $C08 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($C09) && $C09 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }

        if (empty($D01) && $D01 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D02) && $D02 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D03) && $D03 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D04) && $D04 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D05) && $D05 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        if (empty($D06) && $D06 != '0') {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请您填写完整信息', 'data' => []];
            return json($data);
        }
        $CP = $C01 + $C02 + $C03 + $C04 + $C05 + $C06 + $C07 + $C08 + $C09;
        $DP = $D01 + $D02 + $D03 + $D04 + $D05 + $D06 + $D06;
        $depression = [
            'openid'  => $open_id,
            'course'  => 'a',
            'C01'     => $C01,
            'C02'     => $C02,
            'C03'     => $C03,
            'C04'     => $C04,
            'C05'     => $C05,
            'C06'     => $C06,
            'C07'     => $C07,
            'C08'     => $C08,
            'C09'     => $C09,
            'CP'      => $CP,
            'stime'   => $c_stime,
            'etime'   => $c_etime,
            'date'    => strtotime(date('Y-m-d', $c_etime)),
            'ltime'   => timediff($c_etime, $c_stime)
        ];

        $anxiety = [
            'openid'   => $open_id,
            'course'   => 'a',
            'D01'      => $D01,
            'D02'      => $D02,
            'D03'      => $D03,
            'D04'      => $D04,
            'D05'      => $D05,
            'D06'      => $D06,
            'D07'      => $D07,
            'DP'       => $DP,
            'stime'    => $c_etime,
            'etime'    => time(),
            'date'    => strtotime(date('Y-m-d', time())),
            'time'    => timediff(time(), $c_etime)
        ];

        //事务操作，保证数据一致性
        Db::startTrans();
        try {
            //提交抑郁自评数据
            $dep_id = Db::name('depression_info')->insertGetId($depression);
            //提交焦虑自评数据
            $anxiety['dep_id'] = $dep_id;
            Db::name('anxiety_info')->insertGetId($anxiety);
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

    /*
     * 首页心情记录折线图数据
     * */
    public function mood_record_info()
    {
        $open_id  = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $now_time = strtotime(date('Y-m-d', time()));
        $expire_time = $now_time - 7 * 24 * 3600;
        //查询抑郁自评数据
        $depression_infos = [];
        $depression_info = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 'a'])->where([['etime', '>', $expire_time]])->group("date")->field('max(etime) as etime,max(id) as id,date')->order('etime')->select()->toArray();
        if (!empty($depression_info)) {
            //            for($i = 0;$i < 7;$i++){
            //                $ex_time = (6-$i)*24*3600;
            //                //设定初始值
            //                $depression_infos[$i]['CP'] = '';
            //                $depression_infos[$i]['etime'] = date('m.d',($now_time-$ex_time));
            //                //根据现有值修改初始数据
            //                foreach ($depression_info as $k => $v){
            //                    if(($now_time-$ex_time) == $v['date']){
            //                        $cp = Db::name('depression_info')->where('id',$v['id'])->value('CP');
            //                        $depression_infos[$i]['CP'] = $cp;
            //                        $depression_infos[$i]['etime'] = date('m.d',$v['etime']);
            //                    }
            //                }
            //            }
            foreach ($depression_info as $k => $v) {
                $cp = Db::name('depression_info')->where('id', $v['id'])->value('CP');
                $depression_infos[$k]['CP'] = $cp;
                $depression_infos[$k]['etime'] = date('m.d', $v['etime']);
            }
        }
        //查询焦虑自评数据
        $anxiety_infos = [];
        $anxiety_info = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 'a',])->where([['etime', '>', $expire_time]])->group("date")->field('max(etime) as etime,max(id) as id,date')->order('etime')->select()->toArray();
        if (!empty($anxiety_info)) {
            //            for($m = 0;$m < 7;$m++){
            //                $ex_times = (6-$m)*24*3600;
            //                //设定初始值
            //                $anxiety_infos[$m]['DP'] = '';
            //                $anxiety_infos[$m]['etime'] = date('m.d',($now_time-$ex_times));
            //                //根据现有值修改初始数据
            //                foreach ($anxiety_info as $ka => $va){
            //                    if(($now_time-$ex_times) == $va['date']) {
            //                        $dp = Db::name('anxiety_info')->where('id', $va['id'])->value('DP');
            //                        $anxiety_infos[$m]['DP'] = $dp;
            //                        $anxiety_infos[$m]['etime'] = date('m.d', $va['etime']);
            //                    }
            //                }
            //            }

            foreach ($anxiety_info as $ka => $va) {
                $dp = Db::name('anxiety_info')->where('id', $va['id'])->value('DP');
                $anxiety_infos[$ka]['DP'] = $dp;
                $anxiety_infos[$ka]['etime'] = date('m.d', $va['etime']);
            }
        }
        $return = [
            'depression_info' => $depression_infos,
            'anxiety_info'    => $anxiety_infos
        ];

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    /*
     * 自评测试量表结果返回
     * */
    public function self_evaluation()
    {
        $open_id  = input('post.open_id');
        $course  = input('post.course');
        if (empty($open_id) || empty($course)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        //查询抑郁自评数据
        $depression_infos = ['title' => '抑郁自评量表得分'];
        $depression_info = Db::name('depression_info')->where(['openid' => $open_id, 'course' => $course])->order("etime desc")->value('CP');
        $depression_infos['fraction'] = $depression_info;
        $depression_infos['notice'] = '提醒：测评结果仅作为识别抑郁症状的参考性意见，不具备临床诊断意义，如需进一步评估，请到专科医院就诊。';
        if ($depression_info >= 0 && $depression_info <= 4) {
            $depression_infos['name'] = '无抑郁症状';
            $depression_infos['info'] = '您的量表得分低，表明您在近两周尚未出现明显的抑郁症状。从目前看来，您拥有较好的情绪调节能力，尽管在遇到某些事情的时候，您还是难免地会体验到一些随之而来的消极的情绪。然而，这些情绪都是正常的，绝大部分人都会如此。拥有很好的情绪调节能力的你，总是能较快地找到有效的应对和解决的方法，让自己重新投入到生活中。相信您一定有一套属于自己的缓解情绪压力的方法，请您继续保持。您也可以跟随《阳光心情》课程学习更多的调节情绪的方法。';
        } elseif ($depression_info >= 5 && $depression_info <= 9) {
            $depression_infos['name'] = '轻度抑郁症状';
            $depression_infos['info'] = '您的量表得分较低，表明您目前出现了轻度的抑郁症状。可能近来你的生活发生了一些让你备感压力的事情，让你觉得伤心、难过。通常，当人们遇到这些不顺利的事情时，难免会出现一些抑郁的情绪，这都很正常。大部分人在事情过去后，心情自然而然地跟着“放晴”起来，无须过分担忧。相反地，这个时候，“抑郁”是一种信号，它可能提醒我们的生活失衡了，或是告诉你生活中哪些事情对你来说才是重要的。不用害怕抑郁，认识抑郁，也是认识自己。但是，如果抑郁的情绪确实给您造成了困扰、无处安放时，您可以向自己的亲人或朋友袒露心扉，寻求情感的支持，也可以适当地安排一些能令您感到愉快的活动，如运动、听音乐等。同时，您也可以坚持学习《阳光心情》课程，本课程是在认知行为疗法的基础上研发而成，很多研究表明认知行为治疗对改善抑郁情绪有很好的效果。';
        } elseif ($depression_info >= 10 && $depression_info <= 14) {
            $depression_infos['name'] = '中度抑郁症状';
            $depression_infos['info'] = '您的量表得分相对偏高，表明您目前出现了中度的抑郁症状。在过去的2周，您可能在某几天中会感到心情低落、做事提不起劲、无精打采等等，这有可能是来自生活中一些不如意，也有可能是突如其来的感受。除此之外，您可能还会出现食欲不振、难以入睡、夜间易醒等问题。如果这种状态持续的时间已经近半个月甚至更久，给你的工作、生活、社交带来了很多负面的影响，建议您及时寻求专业帮助，在医生的指导下学习《阳光心情》课程，与您有着相同经历的其他人在学完7次课后，抑郁症状都有了较大的改善。';
        } elseif ($depression_info >= 15 && $depression_info <= 19) {
            $depression_infos['name'] = '中重度抑郁症状';
            $depression_infos['info'] = '您的量表得分偏高，表明您目前出现了中重度的抑郁症状。在过去的2周时间里，可能在几乎超过了一半的时间里，您都感到情绪低落、无精打采、做事提不起劲，吃不下饭，有时还会出现一些入睡困难、睡眠浅、容易惊醒等睡眠问题，时常有自己是糟糕的、失败的等消极想法。如果这种状态已经持续了半个月或更久，通过您自己的努力调整也没有改善，日常生活也明显地受到了影响，建议您及时寻求专业的帮助。同时，您也可以在医生的指导学习《阳光心情》课程，课程共有7次课，每次课程都有不同的治疗目标，从认知和行为两个层面帮助您逐步改善抑郁情绪。现在开始进入课程，开启一段认知行为治疗之旅吧。';
        } elseif ($depression_info >= 20 && $depression_info <= 27) {
            $depression_infos['name'] = '重度抑郁症状';
            $depression_infos['info'] = '您的量表得分高，表明您目前出现了重度的抑郁症状。在过去的2周时间里，可能几乎每天您都会感到情绪很低落，沮丧，莫名的疲劳，做什么都提不起劲来，有时甚至会感到很绝望。因为胃口不好，晚上睡不好觉，您可能还会有些消瘦。现在，您可能很难集中注意力做一件事情，说话和动作开始变得很缓慢。您甚至可能对自己产生了怀疑，觉得自己是糟糕的、失败的。如果这种状态持续了半个月或更久，给您的身心带来很多的痛苦感觉，极大地影响到了您的学习、工作和生活，建议您及时寻求专业的帮助，在医生的指导和建议下学习《阳光心情》课程。';
        }
        //查询焦虑自评数据
        $anxiety_infos = ['title' => '焦虑自评量表得分'];
        $anxiety_info = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => $course])->order('etime desc')->value('DP');
        $anxiety_infos['fraction'] = $anxiety_info;
        $anxiety_infos['notice'] = '提醒：测评结果仅作为识别焦虑症状的参考性意见，不具备临床诊断意义，如需进一步评估，请到专科医院就诊。';
        if ($anxiety_info >= 0 && $anxiety_info <= 4) {
            $anxiety_infos['name'] = '无焦虑症状';
            $anxiety_infos['info'] = '您的量表得分低，表明您尚未出现明显的焦虑症状。有时，生活中的一些不确定会让你感到紧张、担忧、失控，然而这种紧张担忧并不会困扰您很久，您总是能找到有效的应对、处理焦虑情绪的方法，总结这些方法，继续保持，也可以跟随《阳光心情》课程学习更多情绪调节的方法。';
        } elseif ($anxiety_info >= 5 && $anxiety_info <= 9) {
            $anxiety_infos['name'] = '轻度焦虑症状';
            $anxiety_infos['info'] = '您的量表得分较低，表明您有轻度的焦虑症状。这有可能是因为您正面临着生活、工作或内心上的不确定，比如即将到来的年度业绩考核、上台演讲、一项前所未有的新任务等等，当遇到一些不在我们可控范围内的事情时，紧张、担心是很自然的，大多数人都会如此，随着事情的结束，这种焦虑情绪也会逐渐消失。
焦虑并不可怕，相反地，“焦虑”是一个信号，帮助您调动全身心的力量，迎接生活中的挑战。但如果焦虑的情绪确实让您感到很困扰、不知所措时，您不妨试着向好友或家人说一说心中的困惑和担忧，向他们取取经，寻求他们的建议，或者跟随《阳光心情》课程学习更多情绪调节的方法，另外课程首页的放松训练板块也是不错的选择。';
        } elseif ($anxiety_info >= 10 && $anxiety_info <= 14) {
            $anxiety_infos['name'] = '中度焦虑症状';
            $anxiety_infos['info'] = '您的量表得分相对偏高，表明您出现了中度的焦虑症状。这意味着在过去的2周时间里，可能有一半的时间，您都感到紧张不安、烦恼易燥等，并伴随着身体上的不适，心慌、呼吸加快、尿频尿急、失眠等。这种焦虑的体验有可能是来自对现在生活的担忧，也可能来自对未来的担忧。
适度的焦虑能够帮助我们提高做事的效率，但是如果焦虑状态持续的时间较长（大于半个月甚至更长时间），对身心的健康是有一定损害的。尝试着在生活中采取一些放松的方法，为焦虑的情绪寻找一些客观的证据，观察一下担心的事情是否会发生。如果通过努力调整情况没有改善，建议您及时寻求专业的帮助。';
        } elseif ($anxiety_info >= 15 && $anxiety_info <= 21) {
            $anxiety_infos['name'] = '重度焦虑症状';
            $anxiety_infos['info'] = '您的量表得分偏高，表明您目前出现了重度焦虑症状。这意味着在过去的2周时间里，几乎大部分时间里，你都会有紧张不安的情绪出现。同时，可能还会伴随着头痛、心慌、心跳加快、心悸、胸痛、胸闷、肌肉紧张等等的身体反应，也有可能会一些消化系统和睡眠问题，比如腹泻、消化不良、夜里难以入睡、夜间容易惊醒等。
生理上的这些不适，连同心理上的焦虑感受，让您烦躁不安，难以集中注意力，无法静下心来做一件事情。如果这种焦虑的状态至少持续了半个月，给您的身心带来了很多的痛苦感觉，极大地影响到了您的学习、工作和生活，那么这个时候您可能需要寻求专业人士的帮助。';
        }
        //失眠测评记录
        $insomnia_infos = ['title' => '失眠严重指数量表得分'];
        $insomnia_info = Db::name('insomnia_info')->where(['openid' => $open_id, 'course' => $course])->order("etime desc")->value('EP');
        $insomnia_infos['fraction'] = $insomnia_info;
        $insomnia_infos['notice'] = '提醒：测评结果仅作为识别失眠症状的参考性意见，不具备临床诊断意义，如需进一步评估，请到专科医院就诊。';
        if ($insomnia_info >= 0 && $insomnia_info <= 7) {
            $insomnia_infos['name'] = '非失眠';
            $insomnia_infos['info'] = '您的量表得分低，表明您的睡眠处于正常范围内，没有明显的失眠现象。偶尔出现的一两次失眠是正常的，大多数人也都会出现，您不需要过于担心。目前的睡眠状态较好，建议您继续保持良好的睡眠卫生习惯。';
        } elseif ($insomnia_info >= 8 && $insomnia_info <= 14) {
            $insomnia_infos['name'] = '轻度失眠';
            $insomnia_infos['info'] = '您的量表得分较低，表明您可能存在轻微的失眠现象。在过去两周里，虽然时不时会出现失眠的情况，但是并不属于长期的慢性失眠现象，仍处于正常范围，无需过分担忧。这可能是因为生活中遇到的某些事情如即将到来的一次重大考试、业绩考核等影响了您的睡眠质量，让你难以入睡、睡的不深，但是随着事情的结束，情况也会有所好转。为了避免偶尔出现的失眠发展成长期的慢性失眠，您需要注意自身的睡眠卫生情况以及注意保持良好的睡眠环境，另外您也可以采取一些放松方法，如课程首页的腹式呼吸、肌肉放松、想象放松等，从而获得平静与放松，改善睡眠。';
        } elseif ($insomnia_info >= 15 && $insomnia_info <= 21) {
            $insomnia_infos['name'] = '中重度失眠';
            $insomnia_infos['info'] = '您的量表得分相对偏高，表明您存在明显的失眠现象。在过去两周的时间里，您可能在大部分晚上都会出现难以入睡、夜间容易醒来、早醒等情况，这些睡眠问题已经较大程度地影响到了您白天的学习、工作和生活，并且较大程度地影响了您的生活质量。如果，这个时候，您的抑郁、焦虑自评量表得分大于5分甚至更高，建议您在医生指导下，坚持学习《阳光心情》课程，通常当抑郁、焦虑情绪得到改善后，睡眠情况也会随之改善。';
        } elseif ($insomnia_info >= 22 && $insomnia_info <= 28) {
            $insomnia_infos['name'] = '重度失眠';
            $insomnia_infos['info'] = '您的量表得分偏高，表明您目前存在明显的、较为严重的失眠现象。在过去两周的时间里，您可能几乎每天晚上都会出现难以入睡、夜间容易醒来、早醒等情况，这些睡眠问题可能在很大程度上影响到了您白天的学习、工作和生活。您在白天可能经常容易觉得疲劳、情绪烦躁，注意力和记忆力下降，这些情况较大程度地影响了您的生活质量，使您饱受失眠的煎熬。这个时候，如果您的抑郁、焦虑自评量表得分大于5分甚至更高，建议您在医生的指导下学习《阳光心情》课程，当抑郁、焦虑情绪有所改善后，睡眠情况往往也会随之改善。';
        }
        //WHO-5生活质量测评记录
        $life_quality_infos = ['title' => 'WHO-5生活质量量表得分'];
        $life_quality_info = Db::name('life_quality_info')->where(['openid' => $open_id, 'course' => $course])->order("etime desc")->value('FP');
        $life_quality_infos['fraction'] = $life_quality_info;
        $life_quality_infos['notice'] = '';
        if ($life_quality_info >= 0 && $life_quality_info <= 13) {
            $life_quality_infos['name'] = '生活质量状况较差';
            $life_quality_infos['info'] = '您的量表得分较低，结果提示您近段时间的生活质量状况较差，可能存在心理健康方面的问题。过去两周，您可能有少于一半的时间感到快乐、心情舒畅，感到充满了活力，精力充沛，这有可能是您这两周出现了一些情绪和睡眠上的困扰。在这种情况下，您可以尝试学习《阳光心情》课程，通过学习，改善自己的状态，从而提升自己的生活质量。如果，您觉得有需要也可以寻求专业人士的帮助。';
        } elseif ($life_quality_info >= 14 && $life_quality_info <= 25) {
            $life_quality_infos['name'] = '生活质量状况良好';
            $life_quality_infos['info'] = '您的量表得分较高，结果提示您近段时间的生活质量状况和心理健康状况均良好。在过去两周，你有超过一半的时间或者更多的时间都感到快乐、心情舒畅，感到充满了活力，精力充沛。拥有良好生活质量的你，一定能够从容地面对生活中的各种挑战，请继续保持。';
        }
        //席汉功能损害测评记录 
        // $depressive_effectss = ['title' => '席汉功能损害量表得分'];
        // $depressive_effects = Db::name('depressive_effects')->where(['openid' => $open_id, 'course' => $course])->order("etime desc")->value('GP');
        // $depressive_effectss['fraction'] = $depressive_effects;
        // $depressive_effectss['notice'] = '';
        //躯体症状记录
        $somatic_symptomss = ['title' => '躯体症状记录量表得分'];
        $somatic_symptoms = Db::name('somatic_symptoms')->where(['openid' => $open_id, 'course' => $course])->order("etime desc")->value('HP');
        $somatic_symptomss['fraction'] = $somatic_symptoms;
        $somatic_symptomss['notice'] = '';
        $somatic_symptomss['info'] = '该量表用于评定过去一周的躯体症状，得分区间为28-140分。得分越高代表着躯体化症状越明显。必要时可以到专科医院做进一步的检查以明确问题，在医生的指导下对症治疗。
        有很多研究发现，情绪和身体是相互影响的，胸闷、头痛、颈背部疼痛、恶心、呕吐等肠胃问题、心慌等躯体症状与抑郁、焦虑等情绪有关，采取一定的方式缓解情绪，身体上的不适也会随之好转。';


        //心理弹性量表
        $psychological_elastic['title'] = '心理弹性量表得分';
        $findsoma = Db::name('psychological_elastic')->where(['open_id' => $open_id, 'course' => $course])->order("etime desc")->field('IP,tough,power,optimistic')->find();

        $psychological_elastic['fraction_chart'] = [
            'tough' => [$findsoma['tough'], '坚韧性'],
            'power' => [$findsoma['power'], '力量性'],
            'optimistic' => [$findsoma['optimistic'], '乐观型'],
        ];

        $psychological_elastic['fraction'] = $findsoma['IP'];

        // $psychological_elastic['fraction'] = [
        //     $findsoma['IP'],
        //     [$findsoma['tough'],'坚韧性'],
        //     [$findsoma['power'],'力量性'],
        //     [$findsoma['optimistic'],'乐观型']
        // ];

        $psychological_elastic['notice'] = '';
        $psychological_elastic['info'] = '该量表用于心理弹性水平的自我评定，得分区间为0-100分。
        心理弹性被看作是个人的一种能力和品质，具体指的是个人应对压力、挫折和创伤的能力。该量表是通过坚韧性、力量性和乐观性三个维度对个体的心理弹性做出评估，得分越高代表心理弹性越大，这种能力能够帮助你在危机或压力情境中更快地获得平衡，发展出健康的应对方法去面对生活中的各种挑战。';

        //抑郁思维模式评估
        $thinking_pattern['title'] = '抑郁思维模式评估';
        $findthin = Db::name('thinking_pattern')->where(['open_id' => $open_id, 'course' => $course])->order('etime desc')->field('JP,individual,negative,self_confidence,Helpless')->find();
        $thinking_pattern['fraction_chart'] = [
            'individual' => [$findthin['individual'], '个体适应不良及对改变的渴望'],
            'negative' => [$findthin['negative'], '消极的自我概念与消极的期望'],
            'self_confidence' => [$findthin['self_confidence'], '自信不足'],
            'Helpless' => [$findthin['Helpless'], '无助感'],
        ];

        $thinking_pattern['fraction'] = $findthin['JP'];

        $thinking_pattern['notice'] = '';
        $thinking_pattern['info'] = '该量表用于评价在日常生活中不自觉地闪现在你脑海中的负面想法，它能帮助你了解你是如何看待自己、生活和未来的，得分区间为30-150分。
        该量表主要涉及个体适应不良及对改变的渴求、消极的自我评价、描述和期望、自信不足、无助感四个方面，得分越高代表这四个方面的消极思维出现得越频繁，研究表明，消极、负性的思维模式是导致抑郁的一个重要原因，这也是认知行为心理治疗的核心理念，教你进一步走进自己的想法，调整导致你抑郁情绪出现、或持续加重的思维模式。';


        $return = [
            $depression_infos,
            $anxiety_infos,
            $insomnia_infos,
            $life_quality_infos,
            // $depressive_effectss,
            $somatic_symptomss,
            $psychological_elastic,
            $thinking_pattern
        ];

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    /*
    * 第二到七节课自评测试量表结果返回
    * */
    public function two_self_evaluation()
    {
        $open_id  = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        //查询抑郁自评数据
        $depression_info = Db::name('depression_info')->where(['openid' => $open_id, 'state' => '1'])->field('id,course,date,CP,C09')->order('course')->select()->toArray();
        if (!empty($depression_info)) {
            foreach ($depression_info as $k => $v) {
                $depression_info[$k]['date'] = date('m.d', $v['date']);
                $depression_info[$k]['course_name'] = '第' . $v['course'] . '节课';
                if ($depression_info[count($depression_info) - 1] > $depression_info[count($depression_info) - 2]) {
                    $name = '分数上升';
                    $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                    if ($v['C09'] != 0) {
                        $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
                    } else {
                        $notice = '';
                    }
                } elseif ($depression_info[count($depression_info) - 1] == $depression_info[count($depression_info) - 2]) {
                    $name = '分数持平';
                    $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                    $notice = '';
                } else {
                    $name = '分数下降';
                    $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
                    $notice = '';
                }
            }
        }
        //查询焦虑自评数据
        $anxiety_info = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => '1'])->field('id,course,date,DP')->order('course')->select()->toArray();
        if (!empty($anxiety_info)) {
            foreach ($anxiety_info as $ka => $va) {
                $anxiety_info[$ka]['date'] = date('m.d', $va['date']);
                $anxiety_info[$ka]['course_name'] = '第' . $va['course'] . '节课';
            }
        }
        $return = [
            'depression_info' => $depression_info,
            'anxiety_info'    => $anxiety_info,
            'name'            => $name,
            'info'            => $info,
            'notice'          => $notice
        ];

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }
    public function selfEvaluation()
    {
        $open_id = get_data('open_id');
        $course = get_data('course');
        validat_data([$open_id, $course], ReturnMsg::MISS_NECESSARY_PARA);

        switch ($course) {
            case 2:
                $data = $this->sel2($open_id);
                return json($data);
                break;
            case 3:
                $data =  $this->sel3($open_id);
                return json($data);
                break;
            case 4:
                $data =  $this->sel4($open_id);
                return json($data);
                break;
            case 5:
                $data = $this->sel5($open_id);
                return json($data);
                break;
            case 6:
                $data =  $this->sel6($open_id);
                return json($data);
                break;
            case 7:
                $data =  $this->sel7($open_id);
                return json($data);
                break;
        }
    }
    private function sel2($open_id)
    {
        try {
            start_Trans();
            //S1第一次抑郁测评填写的数据

            $findDe = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,CP,C09')->find();

            //S2最新抑郁测评填写的一次数据
            $findDep = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 2])->order('stime desc')->field('date,CP,C09')->find();
            $mid = $findDep['CP'] - $findDe['CP'];

            if ($mid > 0) {
                $name = '分数上升';
                $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                if ($findDep['C09'] != 0) {
                    $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
                } else {
                    $notice = '';
                }
            }

            if ($mid == 0) {
                $name = '分数持平';
                $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                $notice = '';
            }

            if ($mid < 0) {
                $name = '分数下降';
                $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
                $notice = '';
            }

            $findDe['date'] = date('m.d', $findDe['date']);
            $findDep['date'] = date('m.d', $findDep['date']);
            $findDe['course_name'] = '第1节课';
            $findDep['course_name'] = '第2节课';

            //S1焦虑测评第一次填写的数据
            $findan = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,DP')->find();

            //S2焦虑测评最新填写的一次数据
            $findanx = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 2])->order('stime desc')->field('date,DP')->find();

            $findan['date'] = date('m.d', $findan['date']);
            $findan['course_name'] = '第1节课';

            $findanx['date'] = date('m.d', $findanx['date']);
            $findanx['course_name'] = '第2节课';

            $reArr = [
                'depression_info' => [$findDe, $findDep],
                'anxiety_info' => [$findan, $findanx],
                'name' => $name,
                'info' => $info,
                'notice' => $notice,
            ];
            //提交事务
            end_Trans();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }
        $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '失败', 'data' => []];
        return $data;
    }
    private function sel3($open_id)
    {
        try {
            start_Trans();
            //S1第一次抑郁测评填写的数据
            $findDe = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,CP,C09')->find();
            //S2第一次抑郁测评填写的数据
            $findDe2 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,CP,C09')->find();

            //S3最新抑郁测评填写的一次数据
            $findDep = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 3])->order('stime desc')->field('date,CP,C09')->find();
            $mid = $findDep['CP'] - $findDe2['CP'];

            if ($mid > 0) {
                $name = '分数上升';
                $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                if ($findDep['C09'] != 0) {
                    $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
                } else {
                    $notice = '';
                }
            }

            if ($mid == 0) {
                $name = '分数持平';
                $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                $notice = '';
            }

            if ($mid < 0) {
                $name = '分数下降';
                $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
                $notice = '';
            }

            $findDe['date'] = date('m.d', $findDe['date']);
            $findDe['course_name'] = '第1节课';
            $findDe2['date'] = date('m.d', $findDe2['date']);
            $findDe2['course_name'] = '第2节课';

            $findDep['date'] = date('m.d', $findDep['date']);
            $findDep['course_name'] = '第3节课';

            //S1焦虑测评第一次填写的数据
            $findan = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,DP')->find();

            //S2焦虑测评第一次填写的数据
            $findan2 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,DP')->find();

            //S3焦虑测评最新填写的一次数据
            $findanx = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 3])->order('stime desc')->field('date,DP')->find();

            $findan['date'] = date('m.d', $findan['date']);
            $findan['course_name'] = '第1节课';
            $findan2['date'] = date('m.d', $findan2['date']);
            $findan2['course_name'] = '第2节课';
            $findanx['date'] = date('m.d', $findanx['date']);
            $findanx['course_name'] = '第3节课';

            $reArr = [
                'depression_info' => [$findDe, $findDe2, $findDep],
                'anxiety_info' => [$findan, $findan2, $findanx],
                'name' => $name,
                'info' => $info,
                'notice' => $notice,
            ];
            //提交事务
            end_Trans();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }

        $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '失败', 'data' => []];
        return $data;
    }
    private function sel4($open_id)
    {
        try {
            start_Trans();
            //S1第一次抑郁测评填写的数据
            $findDe = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,CP,C09')->find();

            //S2第一次抑郁测评填写的数据
            $findDe2 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,CP,C09')->find();

            //S3第一次抑郁测评填写的数据
            $findDe3 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,CP,C09')->find();

            //S4最新抑郁测评填写的一次数据
            $findDep = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 4])->order('stime desc')->field('date,CP,C09')->find();

            $mid = $findDep['CP'] - $findDe3['CP'];

            if ($mid > 0) {
                $name = '分数上升';
                $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                if ($findDep['C09'] != 0) {
                    $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
                } else {
                    $notice = '';
                }
            }

            if ($mid == 0) {
                $name = '分数持平';
                $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                $notice = '';
            }

            if ($mid < 0) {
                $name = '分数下降';
                $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
                $notice = '';
            }

            $findDe['date'] = date('m.d', $findDe['date']);
            $findDe['course_name'] = '第1节课';
            $findDe2['date'] = date('m.d', $findDe2['date']);
            $findDe2['course_name'] = '第2节课';
            $findDe3['date'] = date('m.d', $findDe3['date']);
            $findDe3['course_name'] = '第3节课';


            $findDep['date'] = date('m.d', $findDep['date']);
            $findDep['course_name'] = '第4节课';

            //S1焦虑测评第一次填写的数据
            $findan = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,DP')->find();

            //S2焦虑测评第一次填写的数据
            $findan2 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,DP')->find();

            //S3焦虑测评第一次填写的数据
            $findan3 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,DP')->find();

            //S4焦虑测评最新填写的一次数据
            $findanx = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 4])->order('stime desc')->field('date,DP')->find();

            $findan['date'] = date('m.d', $findan['date']);
            $findan['course_name'] = '第1节课';
            $findan2['date'] = date('m.d', $findan2['date']);
            $findan2['course_name'] = '第2节课';
            $findan3['date'] = date('m.d', $findan3['date']);
            $findan3['course_name'] = '第3节课';
            $findanx['date'] = date('m.d', $findanx['date']);
            $findanx['course_name'] = '第4节课';

            $reArr = [
                'depression_info' => [$findDe, $findDe2, $findDe3, $findDep],
                'anxiety_info' => [$findan, $findan2, $findan3, $findanx],
                'name' => $name,
                'info' => $info,
                'notice' => $notice,
            ];
            //提交事务
            end_Trans();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }

        $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '失败', 'data' => []];
        return $data;
    }
    private function sel5($open_id)
    {
        try {
            start_Trans();
            //S1第一次抑郁测评填写的数据
            $findDe = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,CP,C09')->find();

            //S2第一次抑郁测评填写的数据
            $findDe2 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,CP,C09')->find();
            //S3第一次抑郁测评填写的数据
            $findDe3 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,CP,C09')->find();

            //S4第一次抑郁测评填写的数据
            $findDe4 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 4])->field('date,CP,C09')->find();

            //S5最新抑郁测评填写的一次数据
            $findDep = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 5])->order('stime desc')->field('date,CP,C09')->find();

            $mid = $findDep['CP'] - $findDe4['CP'];

            if ($mid > 0) {
                $name = '分数上升';
                $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                if ($findDep['C09'] != 0) {
                    $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
                } else {
                    $notice = '';
                }
            }

            if ($mid == 0) {
                $name = '分数持平';
                $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                $notice = '';
            }

            if ($mid < 0) {
                $name = '分数下降';
                $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
                $notice = '';
            }

            $findDe['date'] = date('m.d', $findDe['date']);
            $findDe['course_name'] = '第1节课';
            $findDe2['date'] = date('m.d', $findDe2['date']);
            $findDe2['course_name'] = '第2节课';
            $findDe3['date'] = date('m.d', $findDe3['date']);
            $findDe3['course_name'] = '第3节课';
            $findDe4['date'] = date('m.d', $findDe4['date']);
            $findDe4['course_name'] = '第4节课';


            $findDep['date'] = date('m.d', $findDep['date']);
            $findDep['course_name'] = '第5节课';

            //S1焦虑测评第一次填写的数据
            $findan = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,DP')->find();

            //S2焦虑测评第一次填写的数据
            $findan2 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,DP')->find();

            //S3焦虑测评第一次填写的数据
            $findan3 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,DP')->find();

            //S4焦虑测评第一次填写的数据
            $findan4 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 4])->field('date,DP')->find();

            //S5焦虑测评最新填写的一次数据
            $findanx = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 5])->order('stime desc')->field('date,DP')->find();

            $findan['date'] = date('m.d', $findan['date']);
            $findan['course_name'] = '第1节课';
            $findan2['date'] = date('m.d', $findan2['date']);
            $findan2['course_name'] = '第2节课';
            $findan3['date'] = date('m.d', $findan3['date']);
            $findan3['course_name'] = '第3节课';
            $findan4['date'] = date('m.d', $findan4['date']);
            $findan4['course_name'] = '第4节课';
            $findanx['date'] = date('m.d', $findanx['date']);
            $findanx['course_name'] = '第5节课';

            $reArr = [
                'depression_info' => [$findDe, $findDe2, $findDe3, $findDe4, $findDep],
                'anxiety_info' => [$findan, $findan2, $findan3, $findan4, $findanx],
                'name' => $name,
                'info' => $info,
                'notice' => $notice,
            ];
            //提交事务
            end_Trans();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }

        $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '失败', 'data' => []];
        return $data;
    }
    private function sel6($open_id)
    {
        try {
            start_Trans();
            //S1第一次抑郁测评填写的数据
            $findDe = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,CP,C09')->find();

            //S2第一次抑郁测评填写的数据
            $findDe2 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,CP,C09')->find();

            //S3第一次抑郁测评填写的数据
            $findDe3 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,CP,C09')->find();

            //S4第一次抑郁测评填写的数据
            $findDe4 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 4])->field('date,CP,C09')->find();

            //S5第一次抑郁测评填写的数据
            $findDe5 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 5])->field('date,CP,C09')->find();

            //S6最新抑郁测评填写的一次数据
            $findDep = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 6])->order('stime desc')->field('date,CP,C09')->find();

            $mid = $findDep['CP'] - $findDe5['CP'];

            if ($mid > 0) {
                $name = '分数上升';
                $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                if ($findDep['C09'] != 0) {
                    $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
                } else {
                    $notice = '';
                }
            }

            if ($mid == 0) {
                $name = '分数持平';
                $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
                $notice = '';
            }

            if ($mid < 0) {
                $name = '分数下降';
                $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
                $notice = '';
            }

            $findDe['date'] = date('m.d', $findDe['date']);
            $findDe['course_name'] = '第1节课';
            $findDe2['date'] = date('m.d', $findDe2['date']);
            $findDe2['course_name'] = '第2节课';
            $findDe3['date'] = date('m.d', $findDe3['date']);
            $findDe3['course_name'] = '第3节课';
            $findDe4['date'] = date('m.d', $findDe4['date']);
            $findDe4['course_name'] = '第4节课';
            $findDe5['date'] = date('m.d', $findDe5['date']);
            $findDe5['course_name'] = '第5节课';


            $findDep['date'] = date('m.d', $findDep['date']);
            $findDep['course_name'] = '第6节课';

            //S1焦虑测评第一次填写的数据
            $findan = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,DP')->find();

            //S2焦虑测评第一次填写的数据
            $findan2 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,DP')->find();

            //S3焦虑测评第一次填写的数据
            $findan3 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,DP')->find();

            //S4焦虑测评第一次填写的数据
            $findan4 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 4])->field('date,DP')->find();

            //S5焦虑测评第一次填写的数据
            $findan5 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 5])->field('date,DP')->find();

            //S6焦虑测评最新填写的一次数据
            $findanx = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 6])->order('stime desc')->field('date,DP')->find();

            $findan['date'] = date('m.d', $findan['date']);
            $findan['course_name'] = '第1节课';
            $findan2['date'] = date('m.d', $findan2['date']);
            $findan2['course_name'] = '第2节课';
            $findan3['date'] = date('m.d', $findan3['date']);
            $findan3['course_name'] = '第3节课';
            $findan4['date'] = date('m.d', $findan4['date']);
            $findan4['course_name'] = '第4节课';
            $findan5['date'] = date('m.d', $findan5['date']);
            $findan5['course_name'] = '第5节课';
            $findanx['date'] = date('m.d', $findanx['date']);
            $findanx['course_name'] = '第6节课';

            $reArr = [
                'depression_info' => [$findDe, $findDe2, $findDe3, $findDe4, $findDe5, $findDep],
                'anxiety_info' => [$findan, $findan2, $findan3, $findan4, $findan5, $findanx],
                'name' => $name,
                'info' => $info,
                'notice' => $notice,
            ];
            //提交事务
            end_Trans();
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }

        $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '失败', 'data' => []];
        return $data;
    }
    private function sel7($open_id)
    {
        // try {
        start_Trans();
        //S1第一次抑郁测评填写的数据
        $findDe = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,CP,C09')->find();

        //S2第一次抑郁测评填写的数据
        $findDe2 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,CP,C09')->find();

        //S3第一次抑郁测评填写的数据
        $findDe3 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,CP,C09')->find();

        //S4第一次抑郁测评填写的数据
        $findDe4 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 4])->field('date,CP,C09')->find();

        //S5第一次抑郁测评填写的数据
        $findDe5 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 5])->field('date,CP,C09')->find();

        //S6第一次抑郁测评填写的数据
        $findDe6 = Db::name('depression_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 6])->field('date,CP,C09')->find();

        //S7最新抑郁测评填写的一次数据
        $findDep = Db::name('depression_info')->where(['openid' => $open_id, 'course' => 7])->order('stime desc')->field('date,CP,C09')->find();

        $mid = $findDep['CP'] - $findDe6['CP'];

        if ($mid > 0) {
            $name = '分数上升';
            $info = '很抱歉，相比上周你本周的情绪没有改善。但不用担心，这种情绪的变化是正常、合理的，抑郁的恢复并不总是呈直线上升，更多时候是波浪曲线式好转，在有一些时间段，你还是会体验到情绪的波动和反复，接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
            if ($findDep['C09'] != 0) {
                $notice = '在过去一周，你可能经历对你来说很糟糕的事情，以至于你有强烈的自残或自杀念头，为了你的生命安全，应立即咨询医生';
            } else {
                $notice = '';
            }
        }

        if ($mid == 0) {
            $name = '分数持平';
            $info = '本周你的情绪没有往糟糕的方向发展，这可能跟你采取了一些对你来说有帮助的行动、转换有了一些对你有帮助的想法有关。接下来，如果你要让抑郁情绪改善得更明显，继续坚持做一些想法和行为上的改变，这个课程可以帮到你。';
            $notice = '';
        }

        if ($mid < 0) {
            $name = '分数下降';
            $info = '恭喜你，相比上周你本周的情绪有了改善，一定是你采取了一些对你来说有帮助的行动，或是有了一些不一样的对你有帮助的想法，继续保持，抑郁会改善得越来越明显。';
            $notice = '';
        }

        $findDe['date'] = date('m.d', $findDe['date']);
        $findDe['course_name'] = '第1节课';
        $findDe2['date'] = date('m.d', $findDe2['date']);
        $findDe2['course_name'] = '第2节课';
        $findDe3['date'] = date('m.d', $findDe3['date']);
        $findDe3['course_name'] = '第3节课';
        $findDe4['date'] = date('m.d', $findDe4['date']);
        $findDe4['course_name'] = '第4节课';
        $findDe5['date'] = date('m.d', $findDe5['date']);
        $findDe5['course_name'] = '第5节课';
        $findDe6['date'] = date('m.d', $findDe6['date']);
        $findDe6['course_name'] = '第6节课';


        $findDep['date'] = date('m.d', $findDep['date']);
        $findDep['course_name'] = '第7节课';

        //S1焦虑测评第一次填写的数据
        $findan = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 1])->field('date,DP')->find();

        //S2焦虑测评第一次填写的数据
        $findan2 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 2])->field('date,DP')->find();

        //S3焦虑测评第一次填写的数据
        $findan3 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 3])->field('date,DP')->find();

        //S4焦虑测评第一次填写的数据
        $findan4 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 4])->field('date,DP')->find();

        //S5焦虑测评第一次填写的数据
        $findan5 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 5])->field('date,DP')->find();

        //S6焦虑测评第一次填写的数据
        $findan6 = Db::name('anxiety_info')->where(['openid' => $open_id, 'state' => 1, 'course' => 6])->field('date,DP')->find();

        //S7焦虑测评最新填写的一次数据
        $findanx = Db::name('anxiety_info')->where(['openid' => $open_id, 'course' => 7])->order('stime desc')->field('date,DP')->find();

        $findan['date'] = date('m.d', $findan['date']);
        $findan['course_name'] = '第1节课';
        $findan2['date'] = date('m.d', $findan2['date']);
        $findan2['course_name'] = '第2节课';
        $findan3['date'] = date('m.d', $findan3['date']);
        $findan3['course_name'] = '第3节课';
        $findan4['date'] = date('m.d', $findan4['date']);
        $findan4['course_name'] = '第4节课';
        $findan5['date'] = date('m.d', $findan5['date']);
        $findan5['course_name'] = '第5节课';
        $findan6['date'] = date('m.d', $findan6['date']);
        $findan6['course_name'] = '第6节课';
        $findanx['date'] = date('m.d', $findanx['date']);
        $findanx['course_name'] = '第7节课';

        $reArr = [
            'depression_info' => [$findDe, $findDe2, $findDe3, $findDe4, $findDe5, $findDe6, $findDep],
            'anxiety_info' => [$findan, $findan2, $findan3, $findan4, $findan5, $findan6, $findanx],
            'name' => $name,
            'info' => $info,
            'notice' => $notice,
        ];
        //提交事务
        // end_Trans();
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
        return $data;
        // } catch (\Exception $e) {
        //     // 回滚事务
        //     roll_back();
        // }

        $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '失败', 'data' => []];
        return $data;
    }
}
