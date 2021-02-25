<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\MasterDegree;
use app\cm\model\NewTarget;
use app\Request;
use app\util\ReturnCode;
use app\util\ReturnMsg;

class LessonSeven extends cmPrefix
{
    //交互1保存
    public function interactionOneSave(Request $request)
    {
        $data_a = $request->post();
        $data_a['technology'] = json_encode($data_a['technology']);
        $data_a['etime'] = time();
        $data_a['ltime'] = timediff($data_a['stime'], $data_a['etime']);
        try {
            MasterDegree::create($data_a);
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), ReturnMsg::SERVER_ERROR);
        }
    }

    //交互2保存
    public function interactionTwoSave(Request $request)
    {
        $data_a = $request->post();
        $data_a['plan'] = json_encode($data_a['plan']);
        $data_a['new_target'] = json_encode($data_a['new_target']);
        $data_a['etime'] = time();
        $data_a['ltime'] = timediff($data_a['stime'],$data_a['etime']);

        try {
            NewTarget::create($data_a);
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), ReturnMsg::SERVER_ERROR);
        }
    }
}
