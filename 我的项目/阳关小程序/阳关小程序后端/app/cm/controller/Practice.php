<?php

declare(strict_types=1);

namespace app\cm\controller;

use think\Controller;
use think\Request;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;

class Practice extends cmPrefix
{
    //练习总览 S1愉快事件首页展示
    public function pleasure_event_list()
    {
        $open_id = input('post.open_id');

        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('record_happy_event')->where('open_id', $open_id)->field('id,what_time,what_place,have_people,what_thing,pleasure_deg,etime')->order('id')->select()->toArray();
        $ar = [];
        if ($list) {
            foreach ($list as $K => $v) {
                $vN = $v;
                unset($vN['id']);
                unset($vN['etime']);
                //除去字符串中换行F；
                foreach ($vN as $k1 => $v1) {
                    $vN[$k1] = preg_replace('/\r|\n/', '', $v1);
                    $vN[$k1] = trim($vN[$k1]);
                }
                $str = implode(',', $vN);
                $ar[] = [
                    'id' => $v['id'],
                    'title' =>  $str,
                    'etime' => date('Y-m-d H:i', $v['etime'])
                ];
            }
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $ar];
        return json($data);
    }

    //练习总览 S1愉快事件提交
    public function pleasure_event_submit()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        $what_time = input('post.what_time');
        $what_place = input('post.what_place');
        $have_people = input('post.have_people');
        $what_thing = input('post.what_thing');
        $pleasure_deg = input('post.pleasure_deg');

        if (empty($open_id) || empty($what_time) || empty($what_place) || empty($have_people) || empty($what_thing)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $res = Db::name('record_happy_event')->insertGetId([
            'open_id' => $open_id,
            'what_time' => $what_time,
            'what_place' => $what_place,
            'have_people' => $have_people,
            'what_thing' => $what_thing,
            'pleasure_deg' => $pleasure_deg,
            'stime' => $stime,
            'etime' => time(),
            'ltime' => timediff($stime, time())
        ]);

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //返回S1愉快事件 单条信息
    public function pleasure_event_one_list()
    {
        $id = input('post.id');

        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('record_happy_event')->where('id', $id)->find();

        if ($list) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $list];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '数据已删除', 'data' => []];
            return json($data);
        }
    }

    //S1愉快事件编辑
    public function pleasure_event_edit()
    {
        $id = input('post.id');
        $what_time = input('post.what_time');
        $what_place = input('post.what_place');
        $have_people = input('post.have_people');
        $what_thing = input('post.what_thing');
        $pleasure_deg = input('post.pleasure_deg');

        if (empty($id) || empty($what_time) || empty($what_place) || empty($have_people) || empty($what_thing)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $res =  Db::name('record_happy_event')->where('id', $id)->update([
            'what_time' => $what_time,
            'what_place' => $what_place,
            'have_people' => $have_people,
            'what_thing' => $what_thing,
            'pleasure_deg' => $pleasure_deg,
        ]);

        if ($res || $res == 0) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //S1愉快事件删除
    public function pleasure_event_del()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $res = Db::name('record_happy_event')->delete($id);

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //练习总览 S2目标清单展示
    public function pr_target_list()
    {
        $open_id = input('post.open_id');

        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('target_list')->where('open_id', $open_id)->field('id,main_target,specific_goals,etime')->order('id')->select()->toArray();
        foreach ($list as $K => $v) {
            if ($v['main_target']) {
                $problem =  $v['main_target'] . ',' . str_replace("||", ",", $v['specific_goals']);
            } else {
                $problem = str_replace("||", ",", $v['specific_goals']);
            }

            $ar[] = [
                'id' => $v['id'],
                'title' => $problem,
                'etime' => date('Y-m-d H:i', $v['etime'])
            ];
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $ar];
        return json($data);
    }
    //练习总览 返回S2目标清单单条信息
    public function pr_target_one_list()
    {
        $id = input('post.id');

        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('target_list')->where('id', $id)->find();
        if ($list['problem']) {
            $list['problem'] = explode('||', $list['problem']);
        } else {
            $list['problem']  = [];
        }
        if ($list['specific_goals']) {
            $list['specific_goals'] = explode('||', $list['specific_goals']);
        } else {
            $list['specific_goals']  = [];
        }
        if ($list) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $list];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '数据已删除', 'data' => []];
            return json($data);
        }
    }

    //练习总览 S2目标清单编辑
    public function pr_target_one_edit()
    {
        $id = input('post.id');
        $problem = input('post.problem');
        $main_target = input('post.main_target');
        $specific_goals = input('post.specific_goals');

        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        // if ($type == 2) {
        //     if (empty($problem)) {
        //         $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写问题清单', 'data' => []];
        //         return json($data);
        //     }
        //     if (empty($main_target)) {
        //         $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写总目标', 'data' => []];
        //         return json($data);
        //     }
        //     if (empty($specific_goals)) {
        //         $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写具体目标', 'data' => []];
        //         return json($data);
        //     }
        // }

        $list  = [
            'problem'        => $problem,
            'main_target'    => $main_target,
            'specific_goals' => $specific_goals,
        ];

        $res =  Db::name('target_list')->where('id', $id)->update($list);
        if ($res || $res == 0) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //练习总览 S2目标清单删除
    public function pr_target_del()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $res = Db::name('target_list')->delete($id);

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //练习总览 S2活动记录展示
    public function pr_activity_record_list()
    {
        $open_id = input('post.open_id');

        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }


        $list = Db::name('activity_record')->where('open_id', $open_id)->field('stime')->order('stime')->group('stime')->select()->toArray();

        foreach ($list as $k => $v) {
            $ar[] = [
                'stime' => $v['stime'],
                'title' => date('Y-m-d H:i', $v['stime'])
            ];
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $ar];
        return json($data);
    }

    //练习总览 S2活动记录单条记录展示
    public function pr_activity_record_one_list()
    {
        $open_id = input('post.open_id');

        $stime = input('post.stime');
        if (empty($stime) || empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('activity_record')->where(['open_id' => $open_id, 'stime' => $stime])->where('activity', '<>', '')->field('date,time,activity,pleasure,achievement')->select()->toArray();
        $ar = [];
        if ($list) {
            foreach ($list as $k => $v) {
                if ($v['time'] >= 0 && $v['time'] < 12) {
                    $nData = date('Y.m.d', $v['date']) . ' ' . $v['time'] . '-' . ($v['time'] + 1) . '点';
                } elseif ($v['time'] >= 12 && $v['time'] < 24) {
                    $nData = date('Y.m.d', $v['date']) . ' ' . $v['time'] . '-' . ($v['time'] + 1) . '点';
                }

                $ar[] = [
                    'date' => $nData,
                    'activity' => $v['activity'],
                    'pleasure' => $v['pleasure'],
                    'achievement' => $v['achievement']
                ];
            }
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $ar];
        return json($data);
    }
    //练习总览 S2活动记录编辑
    public function pr_activity_record_edit()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime'); //此stime 是从后台返回的stime

        $source = input('post.source/a', array());
        if (empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (empty($source) || !is_array($source)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写活动记录', 'data' => []];
            return json($data);
        }

        $acList = Db::name('activity_record')->where(['open_id' => $open_id, 'stime' => $stime])->field('etime,ltime,new,type,open_id')->find();

        if (!empty($source)) {
            $list = [];
            foreach ($source as $key => $value) {
                for ($i = 0; $i < 24; $i++) {
                    if ($i == $value['time']) {
                        $list[$i]  = [
                            'open_id'     => $acList['open_id'],
                            'date'        => $value['date'],
                            'time'        => $value['time'],
                            'activity'    => $value['activity'],
                            'pleasure'    => $value['pleasure'],
                            'achievement' => $value['achievement'],
                            'week'        => getTimeWeek($value['date']),
                            'new'         => $acList['new'],
                            'type'        => $acList['type'],
                            'stime'       => $stime,
                            'etime'       => $acList['etime'],
                            'ltime'       => $acList['ltime']
                        ];
                    } else {
                        if (empty($list[$i]['activity'])) {
                            $list[$i] = [
                                'open_id'     => $acList['open_id'],
                                'date'        => $value['date'],
                                'time'        => $i,
                                'activity'    => '',
                                'pleasure'    => 0,
                                'achievement' => 0,
                                'week'        => getTimeWeek($value['date']),
                                'new'         => $acList['new'],
                                'type'        => $acList['type'],
                                'stime'       => $stime,
                                'etime'       => $acList['etime'],
                                'ltime'       => $acList['ltime']
                            ];
                        }
                    }
                }
            }
        } else {
            for ($i = 0; $i < 24; $i++) {
                $list[]  = [
                    'open_id'     => $acList['open_id'],
                    'time'        => $i,
                    'activity'    => '',
                    'pleasure'    => 0,
                    'achievement' => 0,
                    'new'         => $acList['new'],
                    'type'        => $acList['type'],
                    'stime'       => $stime,
                    'etime'       => $acList['etime'],
                    'ltime'       => $acList['ltime']
                ];
            }
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {

            Db::name('activity_record')->where('stime', $stime)->delete();
            Db::name('activity_record')->insertAll($list);

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

    //练习总览 S2活动记录删除
    public function pr_activity_record_del()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        if (empty($stime)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $res = Db::name('activity_record')->where(['open_id' => $open_id, 'stime' => $stime])->delete();

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //练习总览 S2自动思维记录显示
    public function pr_s2_auto_think_list()
    {
        $open_id = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('auto_thinking')
            ->where(['course' => 2, 'open_id' => $open_id])
            ->field('id,situation,etime')->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['etime'] = date('Y-m-d H:i', $v['etime']);
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $list];
        return json($data);
    }

    //练习总览 返回S2自动思维记录单条数据
    public function pr_s2_auto_think_one_list()
    {
        $id = get_data('id');
        validat_data($id, ReturnMsg::MISS_NECESSARY_PARA);

        $auRes = Db::name('auto_thinking')->where('id', $id)->field('situation')->find();

        $moRes = Db::name('think_mood')->where('at_id', $id)->withoutField('id')->select()->toArray();
        $thRes = Db::name('think_think')->where('at_id', $id)->withoutField('id')->select()->toArray();

        $auRes['mood'] = $moRes;
        $auRes['think'] = $thRes;

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $auRes);
    }

    //练习总览 S2自动思维记录删除
    public function pr_s2_auto_think_del()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {

            Db::name('think_mood')->where('at_id', $id)->delete();
            Db::name('think_think')->where('at_id', $id)->delete();
            Db::name('auto_thinking')->delete($id);

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

    //练习总览 S2自动思维记录编辑
    public function pr_s2_auto_think_edit()
    {
        $id = get_data('id');
        $situation = get_data('situation');
        $mood = get_data_arr('mood');
        $think = get_data_arr('think');

        validat_data($id, ReturnMsg::MISS_NECESSARY_PARA);
        validat_data($situation, '情境' . ReturnMsg::NOT_EMPTY);
        validat_data([$mood], '情绪' . ReturnMsg::NOT_EMPTY);
        validat_data([$think], '自动思维' . ReturnMsg::NOT_EMPTY);

        //开启事务
        start_Trans();
        try {
            Db::name('auto_thinking')->where('id', $id)->update([
                'situation' => $situation
            ]);

            Db::name('think_mood')->where('at_id', $id)->delete();
            Db::name('think_think')->where('at_id', $id)->delete();

            $moodInsertArr = $this->getMoodInsertArr([$mood, $id]);
            $thinkInsertArr = $this->getThinkInsertArr([$think, $id]);

            Db::name('think_mood')->insertAll($moodInsertArr);
            Db::name('think_think')->insertAll($thinkInsertArr);

            //提交事务
            end_Trans();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, ReturnMsg::FAIL);
    }

    /**
     * S3-活动宝箱保存
     *
     * @return void
     */
    public function pr_s3_activity_box_save()
    {
        $open_id = get_data('open_id');
        $activity = get_data_arr('activity');
        $stime = get_data('stime');

        validat_data([$open_id, $stime], ReturnMsg::MISS_NECESSARY_PARA);
        validat_data([$activity], "活动" . ReturnMsg::NOT_EMPTY);

        //开启事务
        start_Trans();

        try {
            Db::name('user_activity_keys')->where('open_id', $open_id)->delete();
            $arr = [];
            foreach ($activity as $v) {
                $arr[] = [
                    'open_id' => $open_id,
                    'activity' => $v,
                    'stime' => $stime,
                ];
            }
            Db::name('user_activity_keys')->insertAll($arr);
            end_Trans();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, ReturnMsg::FAIL);
    }

    /**
     * S3-活动宝箱列表展示
     *
     * @return void
     */
    public function pr_s3_activity_box_list()
    {
        // 获取open_id
        $open_id = input('post.open_id');

        // 验证open_id的有效性
        if (empty($open_id)) {
            return_msg(ReturnCode::EMPTY_PARAMS, '缺少必要参数/参数错误');
        }

        // 从`cm_user_activity_keys`中查找该`open_id`所有活动
        $res =  Db::name('user_activity_keys')->where('open_id', $open_id)->where('activity','<>','')->select()->toArray();

        foreach ($res as $v) {
            $arr[] = $v['activity'];
        }


        //将查到的结果返回
        return_msg(ReturnCode::SUCCESS, '成功', $arr);
    }

    /**
     * S3-活动宝箱单条展示
     *
     * @return void
     */
    public function pr_s3_activity_box_one_list()
    {
        //获取参数
        $id = input('post.id');

        //验证数据有效性
        validat_data($id, '缺少必要参数/参数错误');

        //从表中查询数据
        $find = Db::name('user_activity_keys')->where('id', $id)->find();

        //结果返回
        return_msg(ReturnCode::SUCCESS, '成功', $find);
    }

    /**
     * S3-活动宝箱删除单条记录
     *
     * @return void
     */
    public function pr_s3_activity_box_del()
    {
        //获取活动`id`
        $id = input('post.id');

        //验证数据的有效性
        if (empty($id)) {
            return_msg(ReturnCode::EMPTY_PARAMS, '缺少必要参数/参数错误');
        }

        //将当前`id`的活动从`cm_user_activity_keys`中删除
        Db::name('user_activity_keys')->delete($id);

        //将查到的结果返回
        return_msg(ReturnCode::SUCCESS, '成功');
    }

    /**
     * S3-活动宝箱编辑
     *
     * @return void
     */
    public function pr_s3_activity_box_edit()
    {
        //获取活动`id`
        $id = input('post.id');
        $activity = input('post.activity');

        //验证数据的有效性
        if (empty($id)) {
            return_msg(ReturnCode::EMPTY_PARAMS, '缺少必要参数/参数错误');
        }

        if (empty($activity)) {
            return_msg(ReturnCode::EMPTY_PARAMS, '请添加活动');
        }

        //修改表中数据
        $res = Db::name('user_activity_keys')->where('id', $id)->update([
            'activity' => $activity
        ]);

        //返回结果
        if ($res || $res == 0) {
            return_msg(ReturnCode::SUCCESS, '成功');
        } else {
            return_msg(ReturnCode::DB_SAVE_ERROR, '失败');
        }
    }

    /**
     * S3-活动宝箱增加
     *
     * @return void
     */
    public function pr_s3_activity_box_add()
    {
        //获取参数
        $open_id = input('post.open_id'); #open_id
        $activity = input('post.activity'); #活动名称
        $stime = input('post.stime'); #添加时间

        //验证数据有效性
        validat_data([$open_id, $stime], '缺少必要参数/参数错误');
        validat_data($activity, '请添加活动');

        //向表中添加数据
        $res = Db::name('user_activity_keys')->insertGetId([
            'open_id' => $open_id,
            'activity' => $activity,
            'stime' => $stime,
        ]);

        if ($res) {
            return_msg(ReturnCode::SUCCESS, '成功');
        } else {
            return_msg(ReturnCode::DB_SAVE_ERROR, '失败');
        }
    }

    /**
     * 练习总览 S3 活动安排列表
     *
     * @return void
     */
    public function pr_s3_activity_plan_list()
    {
        $open_id = input('post.open_id');

        validat_data($open_id, '缺少必要参数/参数错误');

        $res =  Db::name('activity_plan')->where('open_id', $open_id)->group('stime')->order('stime')->field('stime')->select()->toArray();

        $ar = [];
        foreach ($res as $v) {
            $ar[] = [date('Y-m-d H:i', $v['stime']), $v['stime']];
        }

        return_msg(ReturnCode::SUCCESS, '成功', $ar);
    }

    /**
     * 练习总览 S3 活动安排详情
     *
     * @return void
     */
    public function pr_s3_activity_plan_one_list()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');

        validat_data([$open_id, $stime], '缺少必要参数/参数错误');

        $infos = [];
        $time = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
        //查询该open_id的该stime下所提交的活动
        $res = Db::name('activity_plan')->where(['open_id' => $open_id, 'stime' => $stime])->field('id,date,week,activity,step')->select()->toArray();


        foreach ($time as $k => $v) {
            $list = [];
            $i = 0;
            foreach ($res as  $value) {
                if ($value['week'] == $v) {
                    $list[$i]['date'] = date('Y.m.d H:i', $value['date']);
                    $list[$i]['activity'] = $value['activity'];
                    $list[$i]['step'] = $value['step'];
                    $i++;
                }
            }
            $infos[$k]['week'] = $v;
            $infos[$k]['list'] = $list;
        }

        return_msg(ReturnCode::SUCCESS, '成功', $infos);
    }

    //练习总览 S3 活动安排步骤显示
    public function pr_s3_activity_plan_step_list()
    {
        $open_id = get_data('open_id');
        $stime = get_data('stime');
        $step = get_data('step');
        validat_data([$open_id, $stime, $step], ReturnMsg::MISS_NECESSARY_PARA);

        $res = Db::name('activity_plan')->where(['open_id' => $open_id, 'stime' => $stime, 'step' => $step])->field('date,activity,step')->select();
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $res);
    }

    /**
     * 练习总览 S3 活动安排编辑
     *
     * @return void
     */
    public function pr_s3_activity_plan_edit()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        $source = input('post.source/a', array());

        validat_data([$open_id, $stime], '缺少必要参数/参数错误');
        validat_data([$source], '请填写活动安排');

        Db::startTrans();

        try {
            $find = Db::name('activity_plan')->where(['open_id' => $open_id, 'stime' => $stime])->field('etime,ltime,new,type')->find();

            $info = [];
            foreach ($source as $key => $value) {
                $info[$key]['open_id'] = $open_id;
                $info[$key]['date'] = $value['date'];
                $info[$key]['activity'] = $value['activity'];
                $info[$key]['step'] = $value['step'];
                $info[$key]['week'] = getTimeWeek($value['date']);
                $info[$key]['new'] = $find['new'];
                $info[$key]['type'] = $find['type'];
                $info[$key]['stime'] = $stime;
                $info[$key]['etime'] = $find['etime'];
                $info[$key]['ltime'] = $find['ltime'];
            }
            Db::name('activity_plan')->where(['open_id' => $open_id, 'stime' => $stime])->delete();
            Db::name('activity_plan')->insertAll($info);

            // 提交事务
            Db::commit();
            return_msg(ReturnCode::SUCCESS, '成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, '失败');
    }

    /**
     * 练习总览 S3 活动安排删除
     *
     * @return void
     */
    public function pr_s3_activity_plan_del()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');

        validat_data([$open_id, $stime], '缺少必要参数/参数错误');

        Db::startTrans();

        try {
            Db::name('activity_plan')->where(['open_id' => $open_id, 'stime' => $stime])->delete();
            Db::commit();
            return_msg(ReturnCode::SUCCESS, '成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, '失败');
    }

    //练习总览 S3活动记录展示
    public function pr_s3_activity_record_list()
    {
        $open_id = input('post.open_id');

        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }


        $list = Db::name('s3_activity_record')->where('open_id', $open_id)->field('stime')->order('stime')->group('stime')->select()->toArray();
        $ar = [];
        foreach ($list as $v) {
            $ar[] = [
                'stime' => $v['stime'],
                'title' => date('Y-m-d H:i', $v['stime'])
            ];
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $ar];
        return json($data);
    }

    //练习总览 S3活动记录单条记录展示
    public function pr_s3_activity_record_one_list()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        if (empty($stime) || empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $list = Db::name('s3_activity_record')->where(['open_id' => $open_id, 'stime' => $stime])->where('activity', '<>', '')->field('date,time,activity,pleasure,achievement')->select()->toArray();

        foreach ($list as $k => $v) {
            if ($v['time'] >= 0 && $v['time'] < 12) {
                $nData = date('Y.m.d', $v['date']) . ' ' . $v['time'] . '-' . ($v['time'] + 1) . '点';
            } elseif ($v['time'] >= 12 && $v['time'] < 24) {
                $nData = date('Y.m.d', $v['date']) . ' ' . $v['time'] . '-' . ($v['time'] + 1) . '点';
            }

            $ar[] = [
                'date' => $nData,
                'activity' => $v['activity'],
                'pleasure' => $v['pleasure'],
                'achievement' => $v['achievement']
            ];
        }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $ar];
        return json($data);
    }

    //练习总览 S3活动记录编辑
    public function pr_s3_activity_record_edit()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime'); //此stime 是从后台返回的stime

        $source = input('post.source/a', array());
        if (empty($stime) || empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        if (empty($source) || !is_array($source)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写活动记录', 'data' => []];
            return json($data);
        }

        $acList = Db::name('s3_activity_record')->where(['open_id' => $open_id, 'stime' => $stime])->field('etime,ltime,new,type,open_id')->find();

        if (!empty($source)) {
            $list = [];
            foreach ($source as $key => $value) {
                for ($i = 0; $i < 24; $i++) {
                    if ($i == $value['time']) {
                        $list[$i]  = [
                            'open_id'     => $acList['open_id'],
                            'date'        => $value['date'],
                            'time'        => $value['time'],
                            'activity'    => $value['activity'],
                            'pleasure'    => $value['pleasure'],
                            'achievement' => $value['achievement'],
                            'week'        => getTimeWeek($value['date']),
                            'new'         => $acList['new'],
                            'type'        => $acList['type'],
                            'stime'       => $stime,
                            'etime'       => $acList['etime'],
                            'ltime'       => $acList['ltime']
                        ];
                    } else {
                        if (empty($list[$i]['activity'])) {
                            $list[$i] = [
                                'open_id'     => $acList['open_id'],
                                'date'        => $value['date'],
                                'time'        => $i,
                                'activity'    => '',
                                'pleasure'    => 0,
                                'achievement' => 0,
                                'week'        => getTimeWeek($value['date']),
                                'new'         => $acList['new'],
                                'type'        => $acList['type'],
                                'stime'       => $stime,
                                'etime'       => $acList['etime'],
                                'ltime'       => $acList['ltime']
                            ];
                        }
                    }
                }
            }
        } else {
            for ($i = 0; $i < 24; $i++) {
                $list[]  = [
                    'open_id'     => $acList['open_id'],
                    'time'        => $i,
                    'activity'    => '',
                    'pleasure'    => 0,
                    'achievement' => 0,
                    'new'         => $acList['new'],
                    'type'        => $acList['type'],
                    'stime'       => $stime,
                    'etime'       => $acList['etime'],
                    'ltime'       => $acList['ltime']
                ];
            }
        }

        //事务操作，保证数据一致性
        Db::startTrans();
        try {

            Db::name('s3_activity_record')->where(['stime' => $stime, 'open_id' => $open_id])->delete();
            Db::name('s3_activity_record')->insertAll($list);

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

    //练习总览 S3活动记录删除
    public function pr_s3_activity_record_del()
    {
        $open_id = input('post.open_id');
        $stime = input('post.stime');
        if (empty($stime) || empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }

        $res = Db::name('s3_activity_record')->where(['open_id' => $open_id, 'stime' => $stime])->delete();

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
            return json($data);
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
            return json($data);
        }
    }

    //练习总览 S3活动记录新增
    public function pr_s3_activity_record_add()
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

        $info = Db::name('s3_activity_record')->insertAll($list);
        if ($info) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    //练习总览 S3自动思维记录显示
    public function pr_s3_auto_think_list()
    {
        $open_id = get_data('open_id');
        validat_data($open_id, ReturnMsg::MISS_NECESSARY_PARA);

        $res = Db::name('auto_thinking')->where(['open_id' => $open_id, 'course' => 3])->field('etime,situation,id')->select()->toArray();

        foreach ($res as $key => $value) {
            $res[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
        }

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS,$res);
    }

    //练习总览 返回S3自动思维记录单条数据
    public function pr_s3_auto_think_one_list()
    {
        $id = get_data('id');
        validat_data($id, ReturnMsg::MISS_NECESSARY_PARA);

        $auRes = Db::name('auto_thinking')->where('id', $id)->field('situation')->find();

        $moRes = Db::name('think_mood')->where('at_id', $id)->withoutField('id')->select()->toArray();
        $thRes = Db::name('think_think')->where('at_id', $id)->withoutField('id')->select()->toArray();

        $auRes['mood'] = $moRes;
        $auRes['think'] = $thRes;

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $auRes);
    }

    //练习总览 S3自动思维记录删除
    public function pr_s3_auto_think_del()
    {
        $id = get_data('id');

        validat_data($id, ReturnMsg::MISS_NECESSARY_PARA);

        //开启事务
        start_trans();
        try {
            Db::name('auto_thinking')->where('id', $id)->delete();
            Db::name('think_mood')->where('at_id', $id)->delete();
            Db::name('think_think')->where('at_id', $id)->delete();
            //提交事务
            end_Trans();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, ReturnMsg::FAIL);
    }

    //练习总览 S3自动思维记录编辑
    public function pr_s3_auto_think_edit()
    {
        $id = get_data('id');
        $situation = get_data('situation');
        $mood = get_data_arr('mood');
        $think = get_data_arr('think');

        validat_data($id, ReturnMsg::MISS_NECESSARY_PARA);
        validat_data($situation, '情境' . ReturnMsg::NOT_EMPTY);
        validat_data([$mood], '情绪' . ReturnMsg::NOT_EMPTY);
        validat_data([$think], '自动思维' . ReturnMsg::NOT_EMPTY);

        //开启事务
        start_Trans();
        try {
            Db::name('auto_thinking')->where('id', $id)->update([
                'situation' => $situation
            ]);

            Db::name('think_mood')->where('at_id', $id)->delete();
            Db::name('think_think')->where('at_id', $id)->delete();

            $moodInsertArr = $this->getMoodInsertArr([$mood, $id]);
            $thinkInsertArr = $this->getThinkInsertArr([$think, $id]);

            Db::name('think_mood')->insertAll($moodInsertArr);
            Db::name('think_think')->insertAll($thinkInsertArr);

            //提交事务
            end_Trans();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Exception $e) {
            // 回滚事务
            roll_back();
        }
        return_msg(ReturnCode::DB_SAVE_ERROR, ReturnMsg::FAIL);
    }

    private function getMoodInsertArr($arr = [])
    {
        foreach ($arr[0] as $key => $value) {
            $arr[0][$key]['at_id'] = $arr[1];
        }
        return $arr[0];
    }
    private function getThinkInsertArr($arr = [])
    {
        foreach ($arr[0] as $key => $value) {
            $arr[0][$key]['at_id'] = $arr[1];
        }
        return $arr[0];
    }
}
