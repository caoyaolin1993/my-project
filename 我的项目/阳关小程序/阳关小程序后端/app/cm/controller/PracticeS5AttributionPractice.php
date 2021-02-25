<?php
declare (strict_types = 1);

namespace app\cm\controller;

use app\cm\model\AttributionPractice;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\Request;

class PracticeS5AttributionPractice extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $open_id = get_data('open_id');
        $data = AttributionPractice::where('open_id',$open_id)->field('id,things,etime')->order('stime')->select()->toArray();
        foreach ($data as $key => $value) {
            $data[$key]['etime'] = date('Y-m-d H:i',$value['etime']);
        }
        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS,$data);
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
        $validate = $validateRuleMessage->butio;
        $message = $validateRuleMessage->butio1;
        foreach ($data['main_reason'] as $value) {
            validate($validate, $message)->check($value);
        }
        $data['main_reason'] = json_encode($data['main_reason']);
        $data['etime'] = time();
        $data['ltime'] = timediff($data['stime'],$data['etime']);
        AttributionPractice::create($data);
        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS);
    }

    /**  
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data = AttributionPractice::withoutField('stime,etime,ltime,new,type')->find($id)->toArray();

        $data['main_reason'] = json_decode($data['main_reason'],true);
        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS,$data);
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
        $data['main_reason'] = json_encode($data['main_reason']);

        AttributionPractice::update($data);
        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        AttributionPractice::destroy($id);
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }
}