<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\ActivityPlanS4 as ModelActivityPlanS4;
use app\cm\model\AutoThinking;
use app\cm\model\AutoThinkS4;
use app\cm\model\TaskDecomposition;
use app\cm\model\TaskDecompositionInfo as ModelTaskDecompositionInfo;
use app\cm\model\ThinkThink;
use app\cm\validate\ActivityPlanS4;
use app\cm\validate\TaskDecompositionInfo;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class LessonFour extends cmPrefix
{
  //交互1保存
  public function interactionOneSave()
  {
    $open_id = get_data('open_id');
    $task = get_data('task');

    $stime = get_data('stime');
    $steps = get_data_arr('steps');
    $new = get_data('new'); //1=学习，2=复习
    $type = get_data('type');  //1=课程，2=练习
    $flag = 1;
    foreach ($steps as $key => $value) {
      if ($value['steps'] || $value['difficulty'] || $value['ctime']) {
        $flag = 2;
      }
    }
    if (empty($task) && ($flag == 1)) {
      return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    if (empty($task) && ($flag == 2)) {
      return_msg(ReturnCode::EMPTY_PARAMS, '请填写任务');
    }
    // foreach ($steps as $value) {
    //   validate(TaskDecompositionInfo::class)->check($value);
    // }

    $etime = time();

    $taskDecompositionArr = [
      'open_id' => $open_id,
      'task' => $task,
      'stime' => $stime,
      'etime' => $etime,
      'ltime' => timediff($etime, $stime),
      'new' => $new,
      'type' => $type
    ];

    try {
      $arr_find_a = TaskDecomposition::where(['open_id' => $open_id, 'new' => 1])->field('id')->find();

      Db::transaction(function () use ($steps, $taskDecompositionArr, $arr_find_a) {
        if ($arr_find_a) {
          ModelTaskDecompositionInfo::where('task_id', $arr_find_a['id'])->delete();
          TaskDecomposition::destroy($arr_find_a['id']);
        }

        $task = TaskDecomposition::create($taskDecompositionArr);
        foreach ($steps as $key => $value) {
          $steps[$key]['task_id'] = $task->id;
        }

        $taskInfo = app('taskInfo');
        $taskInfo->saveAll($steps);
      });
      return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    } catch (\Throwable $th) {
      return_msg($th->getCode(), $th->getMessage());
    }
  }

  //交互2保存
  public function interactionTwoSave()
  {
    $open_id = get_data('open_id');
    $stime = get_data('stime');
    $new = get_data('new');  //学习状态：1=学习，2=复习
    $type = get_data('type');   //1=课程，2=练习 
    $source = get_data_arr('source');

    $validateRuleMessage = app('validateRuleMessage');
    $validate = $validateRuleMessage->interactionTwoSaveValidate;
    $message = $validateRuleMessage->interactionTwoSaveMessage;

    foreach ($source as $value) {
      validate($validate, $message)->check($value);
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
      $arr_find_a = ModelActivityPlanS4::where(['open_id' => $open_id, 'new' => 1])->field('id')->select()->toArray();

      Db::transaction(function () use ($info, $arr_find_a) {
        if ($arr_find_a) {
          foreach ($arr_find_a as $key => $value) {
            ModelActivityPlanS4::destroy($value['id']);
          }
        }
        $activityPlanS4 = app('activityPlanS4');
        $activityPlanS4->saveAll($info);
      });
      return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    } catch (\Throwable $th) {
      return_msg($th->getCode(), $th->getMessage());
    }
  }

  //交互3展示
  public function interactionThreeRead()
  {
    $open_id = get_data('open_id');

    $auto = AutoThinking::where(['open_id' => $open_id, 'course' => 3, 'new' => 1])->withoutField('course,stime,etime,ltime,new,type')->find();

    $arr = [];
    if ($auto) {
      $auto->thinkMood;
      $auto->thinkThink;
      $arr = $auto->toArray();

      foreach ($arr['thinkThink'] as $key => $value) {
        $arr['thinkThink'][$key]['think'] = $value['think'] . ' ' . $value['fraction'];
      }
    }
    return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $arr);
  }

  //交互3保存
  public function interactionThreeSave()
  {
    $data = request()->param();
    $data['q3_4'] = json_encode($data['q3_4']);
    $data['q10'] = json_encode($data['q10']);

    $data['etime'] = time();
    $data['ltime'] = timediff($data['stime'], $data['etime']);
    try {
      $arr_find_a = AutoThinkS4::where(['open_id' => $data['open_id'], 'new' => 1])->field('id,tt_id')->find();
      Db::transaction(function () use ($data, $arr_find_a) {
        if ($arr_find_a) {
          AutoThinkS4::destroy($arr_find_a['id']);
          $obj_think_a = ThinkThink::find($arr_find_a['tt_id']);
          $obj_think_a->status = 1;
          $obj_think_a->save();
        }

        $thinkThink = ThinkThink::find($data['tt_id']);
        $thinkThink->status = 2;
        $thinkThink->save();
        AutoThinkS4::create($data);
      });
      return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    } catch (\Throwable $th) {
      return_msg($th->getCode(), $th->getMessage());
    }
  }
}
