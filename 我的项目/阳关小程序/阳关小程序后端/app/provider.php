<?php

use app\cm\model\ActivityPlanS4;
use app\cm\model\ActivityPlanS5;
use app\cm\model\ActivityPlanS6;
use app\cm\model\S4ActivityRecord;
use app\cm\model\S5ActivityRecord;
use app\cm\model\S6ActivityRecord;
use app\cm\model\TaskDecompositionInfo;
use app\cm\model\ThinkMood;
use app\cm\model\ThinkThink;
use app\common\validaterulemessage\ValidateRuleMessage;
use app\ExceptionHandle;
use app\Request;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    // 'think\exception\Handle' => ExceptionHandle::class,
     // 绑定自定义异常处理handle类
     'think\exception\Handle'       => '\\app\\common\\exception\\Http',
     'taskInfo' => TaskDecompositionInfo::class,
     'activityPlanS4' => ActivityPlanS4::class,
     'activityPlanS5' => ActivityPlanS5::class,
     'activityPlanS6' => ActivityPlanS6::class,
     's4ActivityRecord' => S4ActivityRecord::class,
     's5ActivityRecord' => S5ActivityRecord::class,
     's6ActivityRecord' => S6ActivityRecord::class,
     'thinkMood' => ThinkMood::class,
     'thinkThink' => ThinkThink::class,
     'validateRuleMessage' => ValidateRuleMessage::class
];