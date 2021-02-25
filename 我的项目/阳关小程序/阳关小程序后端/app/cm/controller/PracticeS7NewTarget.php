<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\NewTarget;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\Request;

class PracticeS7NewTarget
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $open_id = $request->post('open_id');

        $data_a = NewTarget::where('open_id', $open_id)->field('id,new_target,etime')->select()->toArray();

        foreach ($data_a  as $key => $value) {
            $data_a[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $data_a[$key]['new_target'] = implode(',', json_decode($value['new_target'], true));
        }

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data_a);
    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data_a = $request->post();
        $obj_vm = app('validateRuleMessage');

        $arr_ntr_a = $obj_vm->ntr_a;
        $arr_ntr_b = $obj_vm->ntr_b;

        foreach ($data_a['plan'] as $key => $value) {
            validate($arr_ntr_a, $arr_ntr_b)->check($value);
        }
        $data_a['plan'] = json_encode($data_a['plan']);

        $data_a['new_target'] = json_encode($data_a['new_target']);


        $data_a['etime'] = time();
        $data_a['ltime'] = timediff($data_a['stime'], $data_a['etime']);

        NewTarget::create($data_a);

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
        $data_a = NewTarget::field('id,new_target,plan,way')->find($id)->toArray();
        if (!empty($data_a['plan'])) {
            $data_a['plan'] = json_decode($data_a['plan'], true);
            $data_a['new_target'] = json_decode($data_a['new_target'], true);
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data_a);
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
        $data_a = $request->param();

        $obj_vm = app('validateRuleMessage');
        $arr_ntr_a = $obj_vm->ntr_a;
        $arr_ntr_b = $obj_vm->ntr_b;

        foreach ($data_a['plan'] as $key => $value) {
            validate($arr_ntr_a, $arr_ntr_b)->check($value);
        }

        $data_a['plan'] = json_encode($data_a['plan']);
        $data_a['new_target'] = json_encode($data_a['new_target']);

        NewTarget::update($data_a);
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
        NewTarget::destroy($id);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }
}
