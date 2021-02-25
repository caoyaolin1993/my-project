<?php
declare (strict_types = 1);

namespace app\cm\validate;

use think\Validate;

class PracticeS4AutoThink extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'open_id' => 'require',
        'stime' => 'require|unique:auto_thinking',
        'new' => 'require',
        'type' => 'require',
        'situation' => 'require',
        'mood' => 'require|array',
        'think' => 'require|array',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'situation.require' => ['code'=>10001,'msg'=>'情境不能为空'],
        'mood.require' => ['code'=>10002,'msg'=>'情绪不能为空'],
        'think.require' => ['code'=>10003,'msg'=>'自动思维不能为空'],
        'mood.array' => ['code'=>10004,'msg'=>'只能为数组格式'],
        'think.array' => ['code'=>10005,'msg'=>'只能为数组格式'],
        'open_id.require' => ['code'=>10006,'msg'=>'缺少必要参数'],
        'stime.require' => ['code'=>10007,'msg'=>'缺少必要参数'],
        'new.require' => ['code'=>10008,'msg'=>'缺少必要参数'],
        'type.require' => ['code'=>10009,'msg'=>'缺少必要参数'],
        'stime.unique' => ['code'=>10010,'msg'=>'开始时间已存在'],
    ];
}
