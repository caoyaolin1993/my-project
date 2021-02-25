<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\ActivityPlanS5;
use app\cm\model\AttributionPractice;
use app\cm\model\ProblemSolving;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;

class LessonFive extends cmPrefix
{
    //交互1保存
    public function interactionOneSave()
    {
        $data = request()->param();
        if (empty($data['things'])) {
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        }
        $data['main_reason'] = json_encode($data['main_reason']);

        $data['etime'] = time();
        $data['ltime'] = timediff($data['etime'], $data['stime']);
        $arr_find_a = AttributionPractice::where(['open_id' => $data['open_id'], 'new' => 1])->field('id')->find();

        Db::transaction(function () use ($data, $arr_find_a) {
            if ($arr_find_a) {
                AttributionPractice::destroy($arr_find_a['id']);
            }
            AttributionPractice::create($data);
        });

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    //交互2保存
    public function interactionTwoSave()
    {
        $data = request()->param();
        if (empty($data['problem'])) {
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        }
        $data['plan'] = json_encode($data['plan']);
        $data['steps'] = json_encode($data['steps']);
        $data['etime'] = time();
        $data['ltime'] = timediff($data['etime'], $data['stime']);

        $arr_find_a = AttributionPractice::where(['open_id' => $data['open_id'], 'new' => 1])->field('id')->find();

        Db::transaction(function () use ($data, $arr_find_a) {
            if ($arr_find_a) {
                ProblemSolving::destroy($arr_find_a['id']);
            }
            ProblemSolving::create($data);
        });

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    //交互3保存
    public function interactionThreeSave()
    {
        $open_id = get_data('open_id');
        $stime = get_data('stime');
        $new = get_data('new');  //学习状态：1=学习，2=复习
        $type = get_data('type');   //1=课程，2=练习 
        $source = get_data_arr('source');
        if ($source == []) {
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        }
        $etime = time();
        $info = [];
        foreach ($source as $value) {
            $info[] = [
                'open_id' => $open_id,
                'date' => $value['date'],
                'step' => $value['step'],
                'activity' => $value['activity'],
                'week' => getTimeWeek($value['date']),
                'new' => $new,
                'type' => $type,
                'stime' => $stime,
                'etime' => $etime,
                'new' => $new,
                'ltime' => timediff($stime, $etime),
            ];
        }
        try {
            $arr_find_a = ActivityPlanS5::where(['open_id'=>$open_id,'new'=>1])->field('id')->select()->toArray();

            Db::transaction(function () use ($info,$arr_find_a) {
                if ($arr_find_a) {
                    foreach ($arr_find_a as $key => $value) {
                        ActivityPlanS5::destroy($value['id']);
                    }
                }
                $activityPlanS5 = app('activityPlanS5');
                $activityPlanS5->saveAll($info);
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }
}
