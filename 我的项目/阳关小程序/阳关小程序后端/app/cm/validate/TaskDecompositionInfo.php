<?php
declare (strict_types = 1);

namespace app\cm\validate;

use think\Validate;

class TaskDecompositionInfo extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'steps' => 'require',
        'difficulty' => 'require',
        'ctime' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'steps.require' => ['code'=> 10001,'msg'=>'步骤不能为空'],
        'difficulty.require' => ['code'=> 10002,'msg'=>'难度不能为空'],
        'ctime.require' => ['code'=> 10003,'msg'=>'完成时间不能为空'],
    ];
}
