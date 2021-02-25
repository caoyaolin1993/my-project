<?php

/**
 * 菜单缓存 
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminMenuCache
{

  /**
   * 缓存key
   *
   * @param string $admin_menu_id 菜单id
   * @return void
   */
  public static function key($admin_menu_id = '')
  {
    // 判断传入的参数是否为空 如果为空 则是生成缓存全部菜单的id
    if (empty($admin_menu_id)) {
      $admin_menu_id = 'all';
    }
    // 如果不为空 则生成缓存当前菜单的id
    $key = 'adminMenu:' . $admin_menu_id;
    return $key;
  }

  /**
   * 缓存设置
   *
   * @param string $admin_menu_id 菜单 id
   * @param array $admin_menu   菜单信息
   * @param integer $expire 有效时间(秒)
   * @return void
   */
  public static function set($admin_menu_id='',$admin_menu=[],$expire = 0)
  {
    // 先生成当前传过来的菜单id的缓存key
    $key  = self::key($admin_menu_id);

    // 将需要 缓存的信息赋值给一个变量$val
    $val = $admin_menu;

    // 设置一个默认的过期时间一天 如果传过来的有效时间为空 则使用默认过期时间 如果穿过来的不为空 就使用传过来的时间
    $ttl = 1 * 24 * 60 * 60 ;
    $exp = $expire ?: $ttl;

    // 存入缓存
    $res = Cache::set($key,$val,$expire);

    return $res;
  }

  /**
   * 缓存获取
   *
   * @param string $admin_menu_id 菜单id
   * @return void
   */
  public static function get($admin_menu_id='')
  {
    // 通过穿过来的菜单id生成该条信息的key
    $key = self::key($admin_menu_id);
    $res = Cache::get($key);

    return $res;
  }

  /**
   * 缓存删除
   *
   * @param [type] $admin_menu_id 菜单id
   * @return void
   */
  public function del($admin_menu_id)
  {
    // 通过传过来的菜单id生成缓存key
    $key = self::key($admin_menu_id);

    $res = Cache::delete($key);

    return $res;
  }



}
