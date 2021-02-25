<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\Belief;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\Request;

class PracticeS6AssessFaith extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $open_id = $request->post('open_id');

        $data = Belief::where('open_id', $open_id)->field('id,original,etime')->select()->toArray();

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
        $data['support'] = json_encode($data['support']);
        $data['fresh_support'] = json_encode($data['fresh_support']);

        $data['etime'] = time();
        $data['ltime'] = timediff($data['stime'], $data['etime']);

        Belief::create($data);
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
        $data = Belief::withoutField('stime,etime,ltime,new,type')->findOrEmpty($id)->toArray();

        $data['support'] = json_decode($data['support'],true);
        $data['fresh_support'] = json_decode($data['fresh_support'],true);
        
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

        $data['support'] = json_encode($data['support']);
        $data['fresh_support'] = json_encode($data['fresh_support']);

        Belief::update($data);  
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
        Belief::destroy($id);
        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS);
    }
}
