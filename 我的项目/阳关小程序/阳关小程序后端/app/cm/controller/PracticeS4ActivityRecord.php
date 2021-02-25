<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\S4ActivityRecord;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS4ActivityRecord extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $open_id = get_data('open_id');

        $res = S4ActivityRecord::where('open_id', $open_id)->field('stime')->order('stime')->group('stime')->select()->toArray();

        foreach ($res as $value) {
            $arr[] = [
                'stime' => $value['stime'],
                'zh_stime' => date('Y-m-d H:i', $value['stime'])
            ];
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $arr);
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
        $new = get_data('new');
        $type = get_data('type');
        $source = get_data_arr('source');

        $validateRuleMessage = app('validateRuleMessage');
        $validate = $validateRuleMessage->dateActivityTimeValidate;
        $message = $validateRuleMessage->dateActivityTimeMessage;
        foreach ($source as $value) {
            validate($validate, $message)->check($value);
        }

        $list = [];
        foreach ($source as $value) {
            for ($i = 0; $i < 24; $i++) {
                if ($i == $value['time']) {
                    $list[$i] = [
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
        try {
            Db::transaction(function () use ($list) {
                $s4ActivityRecord = app('s4ActivityRecord');
                $s4ActivityRecord->saveAll($list);
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read()
    {
        $open_id = get_data('open_id');
        $stime = get_data('stime');

        $res = S4ActivityRecord::where(['open_id' => $open_id, 'stime' => $stime])->where('activity', '<>', '')->field('date,time,activity,pleasure,achievement')->select()->toArray();
        foreach ($res as $value) {
            $arr[] = [
                'date' => date('Y.m.d', $value['date']) . ' ' . $value['time'] . '-' . ($value['time'] + 1) . '点',
                'activity' => $value['activity'],
                'pleasure' => $value['pleasure'],
                'achievement' => $value['achievement']
            ];
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $arr);
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
        $validate = $validateRuleMessage->dateActivityTimeValidate;
        $message = $validateRuleMessage->dateActivityTimeMessage;
        foreach ($source as $value) {
            validate($validate, $message)->check($value);
        }

        $res = S4ActivityRecord::where(['open_id' => $open_id, 'stime' => $stime])->field('etime,ltime,new,type')->find()->toArray();

        $list = [];
        foreach ($source as $value) {
            for ($i = 0; $i < 24; $i++) {
                if ($i == $value['time']) {
                    $list[$i] = [
                        'open_id'     => $open_id,
                        'date'        => $value['date'],
                        'time'        => $value['time'],
                        'activity'    => $value['activity'],
                        'pleasure'    => $value['pleasure'],
                        'achievement' => $value['achievement'],
                        'week'        => getTimeWeek($value['date']),
                        'new'         => $res['new'],
                        'type'        => $res['type'],
                        'stime'       => $stime,
                        'etime'       => $res['etime'],
                        'ltime'       => $res['ltime']
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
                            'new'         => $res['new'],
                            'type'        => $res['type'],
                            'stime'       => $stime,
                            'etime'       => $res['etime'],
                            'ltime'       => $res['ltime']
                        ];
                    }
                }
            }
        }

        try {
            Db::transaction(function()use($list,$open_id,$stime){
                S4ActivityRecord::destroy(function($query)use($open_id,$stime){
                    $query->where(['open_id'=>$open_id,'stime'=>$stime]);
                });
                $s4ActivityRecord = app('s4ActivityRecord');
                $s4ActivityRecord->saveAll($list);
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
                S4ActivityRecord::destroy(function($query)use($open_id,$stime){
                    $query->where(['open_id'=>$open_id,'stime'=>$stime]);
                });
            });
            return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(),$th->getMessage());
        }
    }
}
