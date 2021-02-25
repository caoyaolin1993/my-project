<?php

/**
 *工具类
 */

namespace app\util;

use app\cm\controller\ThirdParty;
use think\Db;
use think\facade\Cache;

class Weixin
{
  const APPID = "wx6ada410c648a0fa9";
  const APPSECRET = "52335505cd308b4d294651772028bfc7";
  // const APPID="wxd05201280cfe3e62";
  // const APPSECRET="e8f00bb72592655903ca1a6b5b6cd8e7";
  // const APPID="wxb6869e2c96331118";
  // const APPSECRET="fec6554791bfdabdccc28fa76cf2109e";

  public static function get_openid($code)
  {
    //开发者使用登陆凭证 code 获取 session_key 和 openid
    $APPID = Weixin::APPID; //自己配置

    $AppSecret = Weixin::APPSECRET; //自己配置

    $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . self::define_str_replace($code) . "&grant_type=authorization_code";

    $arr = Weixin::vget($url); // 一个使用curl实现的get方法请求
    return json_decode($arr, true);
  }
  public static function get_public_openid($code)
  {
    //开发者使用登陆凭证 code 获取 session_key 和 openid
    $APPID = "wx094eef7bbffe7995"; //自己配置   //慢病院 appid
    // $APPID = "wxd05201280cfe3e62"; //自己配置  //GK appid

    // $AppSecret = "e8f00bb72592655903ca1a6b5b6cd8e7";//自己配置
    $component_appid = 'wx84b9a45b5e839547';
    $obj_party = new ThirdParty();
    $component_access_token = $obj_party->get_token();

    $url = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$APPID."&code=".self::define_str_replace($code)."&grant_type=authorization_code&component_appid=".$component_appid."&component_access_token=".$component_access_token;

    $arr = Weixin::vget($url); // 一个使用curl实现的get方法请求
    return json_decode($arr, true);
  }

  public static function vget($url)
  {
    $info = curl_init();
    curl_setopt($info, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($info, CURLOPT_HEADER, 0);
    curl_setopt($info, CURLOPT_NOBODY, 0);
    curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($info, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($info, CURLOPT_URL, $url);
    $output = curl_exec($info);
    curl_close($info);
    return $output;
  }

  /**
   * 请求过程中因为编码原因+号变成了空格
   * 需要用下面的方法转换回来
   */
  public static function define_str_replace($data)
  {
    return str_replace(' ', '+', $data);
  }
  //
}
