<?php

/**
 * 地区缓存
 */

namespace app\common\cache;

use think\facade\Cache;

class RegionCache
{
  /**
   * 缓存key  对每一条信息返回一个唯一的key
   *
   * @param integer $region_id 地区id
   * @return void
   */
  public static function key($region_id = 0)
  {
    // 根据地区id和类名前缀生成一个唯一的key
    $key = 'region:' . $region_id;
    return $key;
  }

  /**
   * 缓存设置
   *
   * @param integer $region_id 地区id
   * @param array $region 地区信息
   * @param integer $expire 有效时间(秒)
   * @return void
   */
  public static function set($region_id = 0, $region = [], $expire = 0)
  {
    // 根据地区id生成唯一key
    $key = self::key($region_id);

    // $val 需要保存的数据
    $val = $region;
    // $ttl 默认有效时间(秒)
    $ttl = 7 * 24 * 60 * 60;

    // $exp 有效时间(秒) 如果$expire有效则为$expire 否则为$ttl
    $exp = $expire ?: $ttl;
    // 存入缓存中
    $res = Cache::set($key, $val, $exp);
    return $res;
  }

  /**
   * 缓存获取
   *
   * @param integer $region_id 地区id
   * @return void
   */
  public static function get($region_id = 0)
  {
    // 通过缓存id拿到缓存key
    $key = self::key($region_id);

    // 通过缓存key 从缓存中拿到数据
    $res = Cache::get($key);

    // 返回数据
    return $res;
  }

  /**
   * 缓存删除
   *
   * @param integer $region_id 地区id
   * @return void
   */
  public static function del($region_id = 0)
  {
    // 通过地区id拿到缓存key
    $key = self::key($region_id);

    // 通过key删除对应的缓存
    $res = Cache::delete($key);
    // 返回结果
    return $res;
  }
}
