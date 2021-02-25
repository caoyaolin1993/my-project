<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\ActivityPlanS4;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS4ActivityPlan extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $open_id = get_data('open_id');

        $find = ActivityPlanS4::where('open_id', $open_id)->group('stime')->order('stime')->field('stime')->select()->toArray();

        foreach ($find as $key => $value) {
            $find[$key]['zh_stime'] = date('Y-m-d H:i', $value['stime']);
        }

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $find);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
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
                'activity' => $value['activity'],
                'step' => $value['step'],
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
            Db::transaction(function () use ($info) {
                $activityPlanS4 = app('activityPlanS4');
                $activityPlanS4->saveAll($info);
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }

    /**
     * 显示指定的资源
     *
     * @return \think\Response
     */
    public function read()
    {
        $open_id = get_data('open_id');
        $stime = get_data('stime');

        $weekName = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];

        $res = ActivityPlanS4::where(['open_id' => $open_id, 'stime' => $stime])->field('date,week,activity,step')->select()->toArray();

        $infos = [];
        foreach ($weekName as $key => $value) {
            $list = [];
            $i = 0;
            foreach ($res as $v) {
                if ($v['week'] == $value) {
                    $list[$i]['date'] = date('Y.m.d H:i', $v['date']);
                    $list[$i]['activity'] = $v['activity'];
                    $list[$i]['step'] = $v['step'];
                    $i++;
                }
            }
            $infos[$key]['week'] = $value;
            $infos[$key]['list'] = $list;
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $infos);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $open_id = get_data('open_id');
        $stime = get_data('stime');
        $source = get_data_arr('source');

        $validateRuleMessage = app('validateRuleMessage');
        $validate = $validateRuleMessage->interactionTwoSaveValidate;
        $message = $validateRuleMessage->interactionTwoSaveMessage;

        foreach ($source as $value) {
            validate($validate, $message)->check($value);
        }

        $find = ActivityPlanS4::where(['open_id' => $open_id, 'stime' => $stime])->field('etime,ltime,new,type')->find()->toArray();

        foreach ($source as $value) {
            $info[] = [
                'open_id' => $open_id,
                'date' => $value['date'],
                'activity' => $value['activity'],
                'step' => $value['step'],
                'week' => getTimeWeek($value['date']),
                'new' => $find['new'],
                'type' => $find['type'],
                'stime' => $stime,
                'etime' => $find['etime'],
                'ltime' => $find['ltime']
            ];
        }

        try {
            Db::transaction(function () use ($info, $open_id, $stime) {
                ActivityPlanS4::destroy(function ($query) use ($open_id, $stime) {
                    $query->where(['open_id' => $open_id, 'stime' => $stime]);
                });
                $activityObj = app('activityPlanS4');
                $activityObj->saveAll($info);
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        $open_id = get_data('open_id');
        $stime = get_data('stime');
        try {
            Db::transaction(function()use($open_id,$stime){
                ActivityPlanS4::destroy(function($query)use($open_id,$stime){
                    $query->where(['open_id'=>$open_id,'stime'=>$stime]);
                });
            });
            return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(),$th->getMessage());    
        }
    }
}