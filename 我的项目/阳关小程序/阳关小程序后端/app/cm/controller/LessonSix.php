<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\AutoThinking;
use app\cm\model\Belief;
use app\cm\model\Idea;
use app\Request;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;

class LessonSix extends cmPrefix
{
    //交互1展示 
    public function interactionOneRead()
    {
        $open_id = get_data('open_id');

        $where = [
            'open_id' => $open_id,
            'course' => '3',
            'new' => 1,
            'type' => 1
        ];
        $data = AutoThinking::where($where)->findOrEmpty();
        if ($data->toArray()) {
            $data->thinkMood;
            $data->thinkThink;
            $data = $data->toArray();
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data);
    }

    //交互1保存
    public function interactionOneSave(Request $request)
    {
        $data = $request->param();
        $data['idea'] = json_encode($data['idea']);
        $obj_idea = Idea::create($data);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS,$obj_idea->id);
    }

    //交互2展示
    public function interactionTwoRead(Request $request)
    {
        $tt_id = $request->param('tt_id');

        $data = Idea::where('id', $tt_id)->find()->toArray();
        $data['idea'] = json_decode($data['idea'], true);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data);
    }

    //交互2保存
    public function interactionTwoSave(Request $request)
    {   
        $data = $request->param();
        $data['support'] = json_encode($data['support']);
        $data['fresh_support'] = json_encode($data['fresh_support']);
        $data['etime'] = time();
        $data['ltime'] = timediff($data['stime'], $data['etime']);

        $arr_find_a = Belief::where(['open_id' => $data['open_id'], 'new' => 1])->field('id')->find();

        Db::transaction(function () use ($data, $arr_find_a) {
            if ($arr_find_a) {
                Belief::destroy($arr_find_a['id']);
            }
            Belief::create($data);
        });
        
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }
}
