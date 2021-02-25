<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\AutoThinking;
use app\cm\model\AutoThinkS4;
use app\cm\model\ThinkThink;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS4ThinkThink extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($id)
    {
        $auto = AutoThinking::find($id);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $auto->thinkThink->toArray());
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        /*
        open_id
        q5_1
        q5_2
        q6
        q7_1
        q7_2
        q7_3
        q8_1
        q8_2
        q9
        q10[0][feel]
        q10[0][strength]
        q10[1][feel]
        q10[1][strength]
        stime
        new
        type
        tt_id
         */
        $data = $request->param();
        $data['q10'] = json_encode($data['q10']);

        $data['etime'] = time();
        $data['ltime'] = timediff($data['stime'], $data['etime']);

        try {
            Db::transaction(function () use ($data) {
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

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $thinkThink = ThinkThink::find($id);
        $arr = $thinkThink->autoThinkS4;
        if ($arr) {
            $arr = $arr->toArray();
            $arr['q10'] = json_decode($arr['q10'], true);
        }else{
            $arr = [];
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
    public function update(Request $request, $id)
    {
        $data = $request->post();
        $data['q10'] = json_encode($data['q10']);

        $auto = AutoThinkS4::find($id);
        $auto->save($data);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }
}
