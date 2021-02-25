<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\ProblemSolving;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\Request;

class PracticeS5ProblemSolving extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $open_id = $request->param('open_id');
        $data = ProblemSolving::where('open_id', $open_id)->field('id,problem,etime')->order('stime')->select()->toArray();
        
        foreach ($data as $key => $value) {
            $data[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data);
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
        $validateRuleMessage = app('validateRuleMessage');
        $validate = $validateRuleMessage->plan;
        $message = $validateRuleMessage->plan1;
        foreach ($data['plan'] as $value) {
            validate($validate, $message)->check($value);
        }
        
        $validate1 = $validateRuleMessage->steps;
        $message1 = $validateRuleMessage->steps1;
        foreach ($data['steps'] as $value) {
            validate($validate1, $message1)->check($value);
        }
        $data['plan'] =  json_encode($data['plan']);
        $data['steps'] =  json_encode($data['steps']);
        $data['etime'] = time();
        $data['ltime'] = timediff($data['etime'], $data['stime']);

        ProblemSolving::create($data);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data = ProblemSolving::withoutField('stime,etime,ltime,new,type')->find($id)->toArray();
        $data['plan'] = json_decode($data['plan'],true);
        $data['steps'] = json_decode($data['steps'],true);
        
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data);
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
        $data = $request->param();
        $data['plan'] =  json_encode($data['plan']);
        $data['steps'] =  json_encode($data['steps']);

        ProblemSolving::update($data);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        ProblemSolving::destroy($id);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }
}
