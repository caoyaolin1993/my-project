<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ProblemSolving extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'       => 'int',
        'open_id'  => 'varchar',
        'problem'  => 'varchar',
        'target'  => 'varchar',
        'plan'  => 'text',
        'selected'  => 'tinyint',
        'steps'  => 'text',
        'stime'    => 'int',
        'etime'    => 'int',
        'ltime'    => 'varchar',
        'new'      => 'tinyint',
        'type'     => 'tinyint',
    ];
}
