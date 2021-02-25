<?php

namespace app\common\cache;
/**
 * 会员管理
 */

use app\admin\service\MemberService;
use think\facade\Cache;

class MemberCache
{

  /**
   * 设置缓存key  
   *
   * @param [type] $member_id 会员id
   * @return void
   */
  public static function key($member_id)
  {
    // 拿到会员id 通过这个会员id 和文件类名生成一个在cache文件夹中唯一的的key
    $key = 'member:' . $member_id;
    return $key;
  }

  /**
   * 设置缓存
   * 
   * @param [type] $member_id 会员id
   * @param [type] $data  数据
   * @param [type] $expire  有效时间
   * @return void
   */
  public static function set($member_id, $data, $expire = '')
  {
    // 通过member_id生成key
    $key = self::key($member_id);

    // 判断是否设置了有效时间 如果没有 则给定默认值
    $exp = 24 * 60 * 60;
    $ttl = $expire ?: $exp;

    // 将信息存入缓存中
    $res = Cache::set($key, $data, $ttl);

    return $res;
  }

  /**
   * 获取
   *
   * @param [type] $member_id 会员id
   * @return void
   */
  public static function get($member_id)
  {
    // 通过会员id 得到该会员信息存储的key
    $key = self::key($member_id);

    // 通过key从缓存中拿到会员信息
    $data = Cache::get($key);

    // 返回数据
    return $data;
  }

  /**
   * 删除
   *
   * @param [type] $member_id 会员id
   * @return void
   */
  public static function del($member_id)
  {
    // 根据会员id拿到缓存该会员信息的key
    $key = self::key($member_id);

    // 删除该缓存信息
    $res =  Cache::delete($key);

    return $res;
  }

  /**
   * 缓存更新
   *
   * @param [type] $member_id 会员id
   * @return void
   */
  public static function upd($member_id)
  {
    // 先将之前的缓存通过会员id删除
    self::del($member_id);

    // 再从会员服务层拿到会员的信息 因为缓存中已经没有 所以会重新在数据库中拿最新的信息
    $res = MemberService::info($member_id);

    return $res;
  }
}
