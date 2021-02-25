<?php

declare(strict_types=1);

namespace app\cm\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AutoThinking extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'       => 'int',
        'open_id'  => 'varchar',
        'course'   => 'tinyint',
        'situation'=> 'varchar',
        'stime'    => 'int',
        'etime'    => 'int',
        'ltime'    => 'varchar',
        'new'      => 'tinyint',
        'type'     => 'tinyint',
    ];

    public function thinkMood()
    {
        return $this->hasMany(ThinkMood::class,'at_id');
    }

    public function thinkThink()
    {
        return $this->hasMany(ThinkThink::class,'at_id');
    }

    public function getEtimeAttr($value)
    {
        
        return date('Y-m-d H:i',$value);
    }


}
