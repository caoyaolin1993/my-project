<?php

declare(strict_types=1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AutoThinkS4 extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'       => 'int',
        'open_id'  => 'varchar',
        'q3_4'   => 'varchar',
        'q5_1' => 'varchar',
        'q5_2' => 'varchar',
        'q6'    => 'varchar',
        'q7_1'    => 'varchar',
        'q7_2'    => 'varchar',
        'q7_3'      => 'varchar',
        'q8_1'     => 'varchar',
        'q8_2'     => 'varchar',
        'q9'     => 'varchar',
        'q10'     => 'varchar',
        'stime'     => 'int',
        'etime'     => 'int',
        'ltime'     => 'varchar',
        'new'     => 'tinyint',
        'type'     => 'tinyint',
        'tt_id'     => 'tinyint',
    ];
}
