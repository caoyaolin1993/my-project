<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ActivityPlanS6 extends Model
{
     // 设置字段信息
     protected $schema = [
        'id'       => 'int',
        'open_id'  => 'varchar',
        'date'     => 'int',
        'week'     => 'varchar',
        'activity' => 'varchar',
        'step' => 'varchar',
        'stime'    => 'int',
        'etime'    => 'int',
        'ltime'    => 'varchar',
        'new'      => 'tinyint',
        'type'     => 'tinyint',
    ];
}
