<?php

/**
 * 用户缓存 
 */

namespace app\common\cache;

use app\admin\service\AdminUserService;
use think\facade\Cache;

class AdminUserCache
{
  /**
   * 缓存key
   *
   * @param integer $admin_user_id   设置id
   * @return void
   */
  public static function key($admin_user_id = 0)
  {
    $key = 'adminUser:' . $admin_user_id;
    return $key;
  }

  /**
   * 缓存设置
   * 
   * @param integer $admin_user_id 设置id       
   * @param array $admin_user      设置信息       
   * @param integer $expire           有效时间(秒)   
   * @return bool 
   */
  public static function set($admin_user_id = 0, $admin_user = [], $expire = 0)
  {
    $key = self::key($admin_user_id);
    $val = $admin_user;
    $ttl = 7 * 24 * 60 * 60;
    $exp = $expire ?: $ttl;

    $res = Cache::set($key, $val, $exp);
    return $res;
  }

  /**
   * 缓存获取
   *
   * @param integer $admin_user_id 设置id
   * @return array 设置信息
   */
  public static function get($admin_user_id = 0)
  {
    $key = self::key($admin_user_id);
    $res = Cache::get($key);
    return $res;
  }


  /**
   * 缓存删除
   *
   * @param integer $admin_user_id  设置id
   * @return bool
   */
  public static function del($admin_user_id = 0)
  {
    $key = self::key($admin_user_id);
    $res = Cache::delete($key);
    return $res;
  } 
      
  /**
   * 缓存更新
   *
   * @param [type] $admin_user_id 用户id
   * @return void
   */
  public static function upd($admin_user_id)
  {
    // 拿到更新前用户的所有信息  这时里面有了登录时生成的token
    $old = AdminUserService::info($admin_user_id);
    
    // 删除缓存中的用户信息
    self::del($admin_user_id);

    // 当缓存中没有用户信息 再次请求拿到用户信息 这时里面会生成新的token 那就不对 需要除掉 然后存入登录时的token才正常
    $new = AdminUserService::info($admin_user_id);
    
    // 去掉新的用户信息中的token
    unset($new['admin_token']);

    // array_merge() 函数 如果两个或更多个数组元素有相同的键名,则最后的元素会覆盖其它元素 
    // 如果您仅仅向 array_merge() 函数输入一个数组，且键名是整数，则该函数将返回带有整数键名的新数组，其键名以 0 开始进行重新索引
    $user = array_merge($old,$new);

    // 重新存入缓存
    $res = self::set($admin_user_id,$user);

    return $res;
  }
}
