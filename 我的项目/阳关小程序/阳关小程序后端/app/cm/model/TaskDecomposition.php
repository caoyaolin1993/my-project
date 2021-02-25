<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class TaskDecomposition extends Model
{
     // 设置字段信息
     protected $schema = [
        'id'        => 'int',
        'open_id'   => 'varchar',
        'task'      => 'varchar',
        'stime'     => 'int',
        'etime'     => 'int',
        'ltime'     => 'varchar',
        'new'       => 'tinyint',
        'type'      => 'tinyint',
    ];

    public function TaskDecompositionInfo()
    {
        return $this->hasMany(TaskDecompositionInfo::class,'task_id');
    }

    public function getStimeAttr($value)
    {
        return date('Y-m-d H:i',$value);
    }


}
