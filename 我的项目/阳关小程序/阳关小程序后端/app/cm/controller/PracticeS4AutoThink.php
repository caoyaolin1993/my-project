<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\AutoThinking;
use app\cm\model\AutoThinkS4;
use app\cm\model\ThinkMood;
use app\cm\model\ThinkThink;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS4AutoThink extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $open_id = get_data('open_id');
        $resS3 =  AutoThinking::where(['open_id' => $open_id, 'course' => 3, 'new' => 1, 'type' => 1])->field('id,situation,etime')->findOrEmpty()->toArray();

        $resS4 = AutoThinking::where(['open_id' => $open_id, 'course' => 4])->field('id,situation,etime')->select()->toArray();
        if (!empty($resS3)) {
            array_unshift($resS4, $resS3);
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $resS4);
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
        $data['course'] = 4;

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
            $res = AutoThinking::where('stime', $data['stime'])->field('id')->find()->toArray();
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $res);
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
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        /*
        situation      
        mood[0][mood]   
        mood[0][fraction] 
        mood[1][mood]   
        mood[1][fraction] 
        
        think[0][think]
        think[0][fraction]
        think[0][misunderstanding]
        think[1][think]
        think[1][fraction]
        think[1][misunderstanding]
        */
        $data = $request->post();
        $moodArr = $data['mood'];
        $thinkArr = $data['think'];
        unset($data['mood']);
        unset($data['think']);

        $validateRuleMessage = app('validateRuleMessage');
        $validateMood = $validateRuleMessage->moodFractionRequireValidate;
        $messageMood = $validateRuleMessage->moodFractionRequireMessage;
        $validateThink = $validateRuleMessage->thinkFractionMisRequireValidate;
        $messageThink = $validateRuleMessage->thinkFractionMisRequireMessage;

        foreach ($moodArr as $key => $value) {
            validate($validateMood, $messageMood)->check($value);
            $moodArr[$key]['at_id'] = $id;
        }
        //form == 1 新增   ==2 编辑  ==3  删除
        $arr_data = [
            '新增' => [],
            '编辑' => [],
            '删除' => [],
        ];
        foreach ($thinkArr as $key => $value) {
            if ($value['form'] == 1 || $value['form'] == 2) {
                validate($validateThink, $messageThink)->check($value);
                if ($value['form'] == 1) {
                    $value['at_id'] = $id;
                    unset($value['form']);
                    $arr_data['新增'][] = $value;
                    continue;
                }
                if ($value['form'] == 2) {
                    unset($value['form']);
                    $arr_data['编辑'][] = $value;
                    continue;
                }
            }

            if ($value['form'] == 3) {
                $arr_data['删除'] = $value['id_arr'];
            }
        }

        if ($arr_data['删除'] != []) {
            foreach ($arr_data['删除'] as $key => $value) {
                $arr_sta = ThinkThink::field('status')->find($value);

                if ($arr_sta['status'] == 2) {
                    $arr_data['删除'][$key] = [
                        'tt_id' => $value,
                        'type' => 2     //type ==2 需要删除  ==1 不需要
                    ];
                } elseif ($arr_sta['status'] == 1) {
                    $arr_data['删除'][$key] = [
                        'tt_id' => $value,
                        'type' => 1     //type ==2 需要删除  ==1 不需要
                    ];
                }
            }
        }

        try {
            Db::transaction(function () use ($data, $arr_data, $id) {
                $obj_auto = AutoThinking::find($id);
                $obj_auto->situation = $data['situation'];

                if ($arr_data['新增'] != []) {
                    foreach ($arr_data['新增'] as $key => $value) {
                        ThinkThink::create($value);
                    }
                }

                if ($arr_data['编辑'] != []) {
                    foreach ($arr_data['编辑'] as $key => $value) {
                        ThinkThink::update($value);
                    }
                }

                if ($arr_data['删除'] != []) {
                    foreach ($arr_data['删除'] as $key => $value) {
                        if ($value['type'] == 1) {
                            ThinkThink::destroy($value['tt_id']);
                        } elseif ($value['type'] == 2) {
                            AutoThinkS4::where('tt_id', $value['tt_id'])->delete();
                            ThinkThink::destroy($value['tt_id']);
                        }
                    }
                }
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
    public function delete($id)
    {
        $auto = AutoThinking::find($id);
        if (!$auto) {
            return_msg(ReturnCode::RESOURCES_NO_EXIST, ReturnMsg::RESOURCES_NO_EXIST);
        }
        $arr =   $auto->thinkThink()->field('id,status')->select()->toArray();
        $sarr = [];
        foreach ($arr as $key => $value) {
            if ($value['status'] == 2) {
                $sarr[] = $value['id'];
            }
        }
        try {
            Db::transaction(function () use ($sarr, $auto, $id) {
                AutoThinkS4::destroy(function ($query) use ($sarr) {
                    $query->whereIn('tt_id', $sarr);
                });

                ThinkThink::destroy(function ($query) use ($id) {
                    $query->where('at_id', $id);
                });

                ThinkMood::destroy(function ($query) use ($id) {
                    $query->where('at_id', $id);
                });
                $auto->delete();
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }
}
