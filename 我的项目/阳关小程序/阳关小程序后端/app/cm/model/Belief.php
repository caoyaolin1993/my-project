<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Belief extends Model
{
     // 设置字段信息
     protected $schema = [
        'id'       => 'int',
        'open_id'  => 'varchar',
        'original'  => 'varchar',
        'support'  => 'text',
        'fresh'  => 'varchar',
        'fresh_support'  => 'text',
        'stime'  => 'int',
        'etime'  => 'int',
        'ltime'  => 'varchar',
        'new'  => 'tinyint',
        'type'  => 'tinyint',
    ];
}
