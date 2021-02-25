<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ThinkMood extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'       => 'int',
        'mood'     => 'varchar',
        'fraction' => 'varchar',
        'at_id'    => 'int',
    ];
}
