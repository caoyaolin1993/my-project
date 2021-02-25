<?php

declare(strict_types=1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class TaskDecompositionInfo extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'steps'       => 'varchar',
        'difficulty'  => 'varchar',
        'ctime'       => 'varchar',
        'task_id'     => 'int',
    ];
}
