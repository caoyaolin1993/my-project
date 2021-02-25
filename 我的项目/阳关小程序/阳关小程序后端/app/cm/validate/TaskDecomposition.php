<?php
declare (strict_types = 1);

namespace app\cm\validate;

use think\Validate;

class TaskDecomposition extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'open_id' => 'require',
        'task' => 'require',
        'stime' => 'require',
        'steps' => 'array|require',
        'new' => 'require',
        'type' => 'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'open_id.require' => ['code'=> 10001,'msg'=>'缺少必要参数/参数错误'],
        'task.require' => ['code'=> 10002,'msg'=>'任务不能为空'],
        'stime.require' => ['code'=> 10003,'msg'=>'缺少必要参数/参数错误'],
        'new.require' => ['code'=> 10004,'msg'=>'缺少必要参数/参数错误'],
        'type.require' => ['code'=> 10005,'msg'=>'缺少必要参数/参数错误'],
        'steps.array' => ['code'=> 10006,'msg'=>'只能为数组格式'],
        'steps.require' => ['code'=> 10007,'msg'=>'步骤不能为空'],
    ];

    protected $scene = [
        'update'  =>  ['open_id','task','steps']
    ];  

    // 自定义验证规则
    // protected function checkName($value, $rule, $data=[])
    // {
    //     return $rule == $value ? true : '名称错误';
    // }


}
