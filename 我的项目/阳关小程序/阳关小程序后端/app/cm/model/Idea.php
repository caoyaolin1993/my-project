<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Idea extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'       => 'int',
        'idea'  => 'text',
        'tt_id'  => 'int',
    ];
}
