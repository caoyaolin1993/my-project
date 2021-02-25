<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\cm\model\TaskDecomposition;
use app\cm\model\TaskDecompositionInfo as ModelTaskDecompositionInfo;
use app\cm\validate\TaskDecompositionInfo;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS4TaskResolve extends cmPrefix
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $open_id = get_data('open_id');

        $task = TaskDecomposition::where('open_id', $open_id)->order('stime')->field('id,task,stime')->select()->toArray();
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $task);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $task = TaskDecomposition::field('id,task')->find($id);
        $arr = [];
        if ($task) {
            $task->TaskDecompositionInfo;
            $arr = $task->toArray();
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
        foreach ($data['steps'] as $value) {
            validate(TaskDecompositionInfo::class)->check($value);
        }
        $steps = $data['steps'];
        unset($data['steps']);
        try {
            Db::transaction(function () use ($data, $steps, $id) {
                $task = TaskDecomposition::find($id);
                $task->save($data);

                foreach ($steps as $key => $value) {
                    $steps[$key]['task_id'] = $task->id;
                }
                ModelTaskDecompositionInfo::destroy(function ($query) use ($id) {
                    $query->where('task_id', '=', $id);
                });
                $taskInfo = app('taskInfo');
                $taskInfo->saveAll($steps);
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
        try {
            Db::transaction(function () use ($id) {
                ModelTaskDecompositionInfo::destroy(function ($query) use ($id) {
                    $query->where('task_id', '=', $id);
                });
                $task = TaskDecomposition::find($id);
                $task->delete();
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $open_id = get_data('open_id');
        $task = get_data('task');
        $stime = get_data('stime');
        $steps = get_data_arr('steps');
        $new = get_data('new'); //1=学习，2=复习
        $type = get_data('type');  //1=课程，2=练习
        foreach ($steps as $value) {
            validate(TaskDecompositionInfo::class)->check($value);
        }

        $etime = time();

        $taskDecompositionArr = [
            'open_id' => $open_id,
            'task' => $task,
            'stime' => $stime,
            'etime' => $etime,
            'ltime' => timediff($etime, $stime),
            'new' => $new,
            'type' => $type
        ];

        try {
            Db::transaction(function () use ($steps, $taskDecompositionArr) {
                $task = TaskDecomposition::create($taskDecompositionArr);
                foreach ($steps as $key => $value) {
                    $steps[$key]['task_id'] = $task->id;
                }

                $taskInfo = app('taskInfo');
                $taskInfo->saveAll($steps);
            });
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
        } catch (\Throwable $th) {
            return_msg($th->getCode(), $th->getMessage());
        }
    }
}
