<?php
declare (strict_types = 1);

namespace app\cm\validate;

use think\Validate;

class S4ActivityRecord extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'open_id'=>'require',
        'stime'=>'require',
        'new'=>'require',
        'type'=>'require',
        'source'=>'require|array',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'open_id.require' => ['code'=>10001,'msg'=>'缺少必要参数/参数错误'],
        'stime.require' => ['code'=>10001,'msg'=>'缺少必要参数/参数错误'],
        'new.require' => ['code'=>10001,'msg'=>'缺少必要参数/参数错误'],
        'type.require' => ['code'=>10001,'msg'=>'缺少必要参数/参数错误'],
        'source.require' => ['code'=>10001,'msg'=>'请填写活动安排'],
        'source.array' => ['code'=>10001,'msg'=>'只能为数组格式'],
    ];
}
