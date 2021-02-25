<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\Idea;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\Request;

class PracticeS6FoundFaithNext extends cmPrefix
{
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data_a = $request->param();
        $data_a['idea'] = json_encode($data_a['idea']);
        Idea::create($data_a);
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
        $data_a = Idea::Where('tt_id', $id)->field('idea')->findOrEmpty()->toArray();
        if ($data_a) {
            $data_a['idea']  = json_decode($data_a['idea'], true);
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
    public function update(Request $request, $tt_id)
    {
        $data = $request->post();
        $data['idea'] = json_encode($data['idea']);
        Idea::where('tt_id', $tt_id)->update($data);

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }
}
