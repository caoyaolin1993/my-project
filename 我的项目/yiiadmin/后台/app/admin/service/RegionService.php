<?php

declare(strict_types=1);
/**
 * 地区管理服务层 与控制器层打交道  处理逻辑和操作数据库
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\RegionCache;

class RegionService
{
  // 地区树形key
  protected static $tree_key = 'tree';

  /**
   * 地区信息
   * region_id=tree:树形
   * @param [type] $region_id 地区id
   * @return void
   */
  public static function info($region_id)
  {
    // 通过地区id从缓存中拿到信息
    $region = RegionCache::get($region_id);

    // 如果没有从缓存中拿到数据 那么从数据库中拿到数据再存入缓存
    if (empty($region)) {
      // 如果地区id为树形key 需要单独做处理  这时的key为字符串
      if ($region_id == self::$tree_key) {
        // 这时需要在数据库中查询全部的地区信息

        /*
            将数据库中的数据转成树形结构有两种方式 
            1: 先从数据库中找出父级id为0的数据  再从数据库中找到以这些数据的id为父级id的数据 如此循环
            2: 先从数据库库中找到全部的信息放到一个数据中 然后通过操作这个数据进行树形结构的转换 这样只操作
              一次数据库,能缓解数据库的压力
          */
        // 先从数据库中拿到全部的地区id 地区父级id 地区名称放到$region数组中
        $region = Db::name('region')
          ->field('region_id,region_pid,region_name')
          ->where('is_delete', '=', 0)
          ->select()
          ->toArray();

        // 将$region数组改造成树形
        $region = self::toTree($region, 0);
      } else {
        // 否则为整型地区id
        // 通过地区id从数据库中查到对应的地区信息
        $region = Db::name('region')
          ->where('region_id', $region_id)
          ->find();
        // 如果结果不存在 则抛出错误
        if (empty($region)) {
          exception('地区不存在:' . $region_id);
        }
        // 地区完整名称

        // 将数据表中路径字段中存放的至少一个(这条信息本身)或多个地区id拿出来
        $region_path = explode(',', $region['region_path']);

        // 如果该数组中只有一个id 那么说明这条地区的完整名称就是该条信息的地区名称
        if (count($region_path) == 1) {
          // 完整名称
          $region_fullname = $region['region_name'];
          // 完整名称拼音
          $region_fullname_py = $region['region_pinyin'];
        } else {
          // 定义一个数组 按顺序存放路径里存放的地址每个id的地址名称和地址拼音
          $region_pid = [];
          foreach ($region_path as $k => $v) {
            // 将当前id的信息存入$region_pid中  
            $region_pid[] = Db::name('region')
              ->field('region_name,region_pinyin')
              ->where('region_id', '=', $v)
              ->find();
          }
          // 将$region_pid数组中每条信息的键值为region_name和region_pinyin的值拿出来新组合成一个数组将新数组中的地址拼接成字符串
          $region_fullname = array_column($region_pid, 'region_name');
          $region_fullname = implode('-', $region_fullname);
          $region_fullname_py = array_column($region_pid, 'region_pinyin');
          $region_fullname_py = implode('-', $region_fullname_py);
        }

        // 缓存中存入的信息 不一定就是数据库中存在的信息 还可以加入新的信息存入缓存中
        $region['region_fullname'] = $region_fullname;
        $region['region_fullname_py'] = $region_fullname_py;
      }
      
      // 存入缓存中
      RegionCache::set($region_id, $region);
    }
    // 返回地区信息
    return $region;
  }

  /**
   * 地区转化树形
   *
   * @param [type] $region 所有地区
   * @param [type] $region_pid 地区父级id
   * @return void
   */
  public static function toTree($region, $region_pid)
  {
    // 定义$tree 给默认值[] 存放树形结构的数组
    $tree = [];

    // 循环所有地区 改造成树形
    foreach ($region as $k => $v) {
      // 如果当前这条信息的父级id = 传入到这个函数方法的父级id 那么就进行操作
      if ($v['region_pid'] == $region_pid) {
        // 向当前信息中多加一个键值 存放以当前信息的region_id为父级id的数据
        $v['children'] = self::toTree($region, $v['region_id']);
        // 通过这个操作 返回给外界调用这个方法的数据 就只有外界传过来的父级id的这些数据 例如传0 那么返回的数组中就只有regin_pid = 0 的那些信息
        $tree[] = $v;
      }
    }
    // 返回树形
    return $tree;
  }
}
