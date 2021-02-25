<?php
/**
 * IP信息缓存 
 */

namespace app\common\cache;

use think\facade\Cache;

class IpInfoCache
{
  /**
   * 缓存key
   *
   * @param integer $ip   设置id
   * @return void
   */
  public static function key($ip = 0)
  {
    $key = 'ipInfo:'.$ip;
    return $key;
  }

  /**
   * 缓存设置
   *
   * @param integer $ip 设置id
   * @param array $admin_setting      设置信息
   * @param integer $expire           有效时间(秒)
   * @return bool
   */
  public static function set($ip = 0,$ipinfo=[],$expire=0)
  {
    $key = self::key($ip);
    $val = $ipinfo;
    $ttl = 7 * 24 * 60 * 60;
    $exp = $expire ?:$ttl ;

    $res = Cache::set($key,$val,$exp);
    return $res;
  }

  /**
   * 缓存获取
   *
   * @param integer $ip 设置id
   * @return array 设置信息
   */
  public static function get($ip = 0)
  {
    $key = self::key($ip);
    $res = Cache::get($key);
    return $res;
  }


  /**
   * 缓存删除
   *
   * @param integer $ip  设置id
   * @return bool
   */ 
  public static function del($ip=0)
  {
    $key = self::key($ip);
    $res = Cache::delete($key);
    return $res;
  }
}