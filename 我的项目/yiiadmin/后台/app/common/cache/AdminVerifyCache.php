<?php
/**
 * 验证码缓存 
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminVerifyCache
{
  /**
   * 缓存key
   *
   * @param integer $verify_id   设置id
   * @return void
   */
  public static function key($verify_id = 0)
  {
    $key = 'adminVerify:'.$verify_id;
    return $key;
  }

  /**
   * 缓存设置
   * 
   * @param integer $verify_id 设置id       
   * @param array $admin_setting      设置信息       
   * @param integer $expire           有效时间(秒)   
   * @return bool 
   */
  public static function set($verify_id = 0,$verify_code=[],$expire=0)
  {
    $key = self::key($verify_id);
    $val = $verify_code;
    $ttl = 7 * 24 * 60 * 60;
    $exp = $expire ?:$ttl ;

    $res = Cache::set($key,$val,$exp);
    return $res;
  }

  /**
   * 缓存获取
   *
   * @param integer $verify_id 设置id
   * @return array 设置信息
   */
  public static function get($verify_id = 0)
  {
    $key = self::key($verify_id);
    $res = Cache::get($key);
    return $res;
  }


  /**
   * 缓存删除
   *
   * @param integer $verify_id  设置id
   * @return bool
   */ 
  public static function del($verify_id=0)
  {
    $key = self::key($verify_id);
    $res = Cache::delete($key);
    return $res;
  }
}