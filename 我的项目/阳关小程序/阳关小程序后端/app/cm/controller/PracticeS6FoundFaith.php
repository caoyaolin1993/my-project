<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\AutoThinking;
use app\cm\model\Idea;
use app\cm\model\ThinkMood;
use app\cm\model\ThinkThink;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS6FoundFaith extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $open_id = $request->param('open_id');
        $where = [
            'open_id' => $open_id,
            'new' => 1,
            'type' => 1,
            'course' => 3
        ];
        $data = AutoThinking::where($where)->field('id,situation,etime')->findOrEmpty()->toArray();

        $data_a = AutoThinking::where(['open_id' => $open_id, 'course' => 6])->field('id,situation,etime')->select()->toArray();

        array_unshift($data_a, $data);

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data_a);
    }

    /**
     * 显示创建资源表单页.  
     *
     * @return \think\Response
     */
    public function create()
    {
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = $request->param();

        $moodArr = $data['mood'];
        $thinkArr = $data['think'];
        unset($data['mood']);
        unset($data['think']);
        $data['etime'] = time();
        $data['ltime'] = timediff($data['stime'], $data['etime']);
        $data['course'] = 6;

        $validateRuleMessage = app('validateRuleMessage');
        $validateMood = $validateRuleMessage->moodFractionRequireValidate;
        $messageMood = $validateRuleMessage->moodFractionRequireMessage;

        $validateThink = $validateRuleMessage->thinkFractionMisRequireValidate;
        $messageThink = $validateRuleMessage->thinkFractionMisRequireMessage;

        foreach ($moodArr as $value) {
            validate($validateMood, $messageMood)->check($value);
        }
        foreach ($thinkArr as $value) {
            validate($validateThink, $messageThink)->check($value);
        }
        try {
            Db::transaction(function () use ($data, $moodArr, $thinkArr) {
                $auto = AutoThinking::create($data);
                foreach ($moodArr as $key => $value) {
                    $moodArr[$key]['at_id'] = $auto->id;
                }
                foreach ($thinkArr as $key => $value) {
                    $thinkArr[$key]['at_id'] = $auto->id;
                }
                $thinkMood = app('thinkMood');
                $thinkThink = app('thinkThink');
                $thinkMood->saveAll($moodArr);
                $thinkThink->saveAll($thinkArr);
            });
            $id = AutoThinking::where($data)->order('id desc')->field('id')->find();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS,$id['id']);
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
        $auto = AutoThinking::field('id,situation')->findOrEmpty($id);
        $auto->thinkMood;
        $auto->thinkThink;

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $auto->toArray());
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
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
        $data_a = $request->post();
        $arr_mood_a = $data_a['mood'];
        $arr_think_a = $data_a['think'];
        unset($data_a['mood']);
        unset($data_a['think']);
        $validateRuleMessage = app('validateRuleMessage');
        $validateMood = $validateRuleMessage->moodFractionRequireValidate;
        $messageMood = $validateRuleMessage->moodFractionRequireMessage;
        $validateThink = $validateRuleMessage->thinkFractionMisRequireValidate;
        $messageThink = $validateRuleMessage->thinkFractionMisRequireMessage;

        foreach ($arr_mood_a as $key => $value) {
            validate($validateMood, $messageMood)->check($value);
            $arr_mood_a[$key]['at_id'] = $id;
        }
        foreach ($arr_think_a as $key => $value) {
            validate($validateThink, $messageThink)->check($value);
            $arr_think_a[$key]['at_id'] = $id;
        }
        $obj_auto_a = AutoThinking::find($id);
        if (empty($obj_auto_a)) {
            return_msg(ReturnCode::RESOURCES_NO_EXIST, ReturnMsg::RESOURCES_NO_EXIST);
        }

        try {
            Db::transaction(function () use ($data_a, $obj_auto_a, $arr_mood_a, $arr_think_a, $id) {
                ThinkThink::destroy(function ($query) use ($id) {
                    $query->where('at_id', $id);
                });

                ThinkMood::destroy(function ($query) use ($id) {
                    $query->where('at_id', $id);
                });
                $obj_auto_a->save($data_a);
                $obj_mood_a = app('thinkMood');
                $obj_think_a = app('thinkThink');
                $obj_mood_a->saveAll($arr_mood_a);
                $obj_think_a->saveAll($arr_think_a);
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), ReturnMsg::SERVER_ERROR);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $obj_a = AutoThinking::find($id);
        if ($obj_a) {
            $data_a = $obj_a->thinkThink()->field('id')->select()->toArray();
            foreach ($data_a as $key => $value) {
                $arr_a[] = $value['id'];
            }
            try {
                Db::transaction(function () use ($arr_a, $obj_a, $id) {
                    Idea::destroy(function ($query) use ($arr_a) {
                        $query->whereIn('tt_id', $arr_a);
                    });

                    ThinkThink::destroy(function ($query) use ($id) {
                        $query->where('at_id', $id);
                    });

                    ThinkMood::destroy(function ($query) use ($id) {
                        $query->where('at_id', $id);
                    });
                    $obj_a->delete();
                });
                return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
            } catch (\Throwable $th) {
                return_msg($th->getCode(), ReturnMsg::SERVER_ERROR);
            }
        } else {
            return_msg(ReturnCode::RESOURCES_NO_EXIST, ReturnMsg::RESOURCES_NO_EXIST);
        }
    }
}
