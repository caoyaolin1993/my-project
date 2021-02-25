<?php
declare (strict_types = 1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ThinkThink extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'       => 'int',
        'think'    => 'varchar',
        'fraction' => 'varchar',
        'misunderstanding'=> 'varchar',
        'at_id'    => 'int',
        'status'    => 'tinyint',
    ];

    public function autoThinkS4()
    {
        return $this->hasOne(AutoThinkS4::class,'tt_id');
    }

}