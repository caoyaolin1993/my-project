<?php

declare(strict_types=1);

namespace app\cm\controller;

use think\Controller;
use app\util\ReturnCode;
use app\util\ReturnMsg;
use app\util\Weixin;
use app\util\WXBizDataCrypt;
use think\facade\Cache;
use think\facade\Db;

class Register extends cmPrefix
{
    /* 获取openid */
    public function getOpenId()
    {
        $code = input('post.code');
        $arr = Weixin::get_openid($code);
        if (isset($arr['errcode'])) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '无任何信息返回', 'data' => []];
            return json($data);
        }
        $session_key = $arr['session_key'];
        $openid = $arr['openid'];

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => ['openid' => $openid, 'session_key' => $session_key]];
        return json($data);
    }
    /* 获取公众号openid */
    public function getPublicOpenId()
    {
        $open_id = input('post.open_id');
        $code = input('post.code');
        $arr = Weixin::get_public_openid($code);
        if (isset($arr['errcode'])) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '无任何信息返回', 'data' => $arr];
            return json($data);
        }
        $find = Db::name('ds')->where('open_id', $open_id)->find();

        if ($find) {
            Db::name('ds')->where('open_id', $open_id)->update([
                'public_open_id' => $arr['openid']
            ]);
        } else {
            Db::name('ds')->insertGetId([
                'open_id' => $open_id,
                'public_open_id' => $arr['openid']
            ]);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
        return json($data);
    }

    public function getCourseWarnTime()
    {
        $open_id = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数/参数错误', 'data' => []];
            return json($data);
        }
        $arr_find_a = Db::name('ds')->where('open_id',$open_id)->field('lx_time,kc_time_str')->find();


        if($arr_find_a){
            if($arr_find_a['lx_time']){
                $data['lx_time'] = date('Y-m-d H:i',$arr_find_a['lx_time']);
                $data['lx_time'] = substr($data['lx_time'],11);
            }else{
                $data['lx_time'] = "";
            }   
            $data['kc_time'] = $arr_find_a['kc_time_str'];
        }else{
            $data['lx_time'] = "";
            $data['kc_time'] = "";
        }

        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS,$data);

    }

    //判断用户是否关注公众号
    public function is_attention_pub()
    {
        // $appId = "wxd05201280cfe3e62"; 
        // $appSecret = "e8f00bb72592655903ca1a6b5b6cd8e7";
        $open_id = input('post.open_id');
        if (empty($open_id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少参数', 'data' => []];
            return json($data);
        }

        // $find = Db::name('access_token_public')->order('id')->limit(1)->select()->toArray(); 

        // if ($find) { 
        //     if ($find[0]['expires_in'] < time()) { 
        //         $access_token = $this->getAccessToken($appId,  $appSecret); 
        //         $expires_in = time() + 7200; 
        //         Db::name('access_token_public')->where('id', $find[0]['id'])->update([
        //             'access_token' => $access_token,
        //             'expires_in' => $expires_in
        //         ]);
        //         $access_token_use = $access_token;
        //     } else {
        //         $access_token_use = $find[0]['access_token'];
        //     }
        // } else {
        //     $access_token = $this->getAccessToken($appId, $appSecret);
        //     $expires_in = time() + 7200;
        //     Db::name('access_token_public')->insertGetId([
        //         'access_token' => $access_token,
        //         'expires_in' => $expires_in
        //     ]);
        //     $access_token_use = $access_token;
        // }

        $obj_timeing_a = new Timing();

        $token = $obj_timeing_a->get_authorizer_access_token();


        $find1 = Db::name('ds')->where('open_id', $open_id)->field('public_open_id')->find();
        if ($find1['public_open_id']) {
            $subscribe_msg = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $token . "&openid=" . $find1['public_open_id'];

            $subscribe = json_decode(file_get_contents($subscribe_msg));

            $gzxx = $subscribe->subscribe;

            if ($gzxx === 1) {
                $res = 1;  //已关注
            } else {
                $res = 2; //未关注
            };
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $res];
            return json($data);
        }
    }

    public function getAccessToken($appId, $appSecret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
        $html = file_get_contents($url);
        $output = json_decode($html, true);
        $access_token = $output['access_token'];
        return $access_token;
    }

    /* 获取用户信息 */
    public function getInfo()
    {
        $openid = input('post.openid');
        $nickname = input('post.nickName');
        if (empty($nickname)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少参数', 'data' => []];
            return json($data);
        }
        if (empty($openid)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少参数', 'data' => []];
            return json($data);
        }
        // openid不存在需添加进数据库
        $res = Db::name('user')
            ->where('open_id', $openid)
            ->find();
        if (!$res) {
            Db::name('user')->insert(['open_id' => $openid, 'wx_nickname' => $nickname, 'first_login_time' => time()]);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
        return json($data);
    }

    public function getMobile()
    {
        $appid = "wx6ada410c648a0fa9";
        $openid = input('post.openid');
        $session_key = input('post.session_key');
        $encryptedData = input('post.encryptedData');
        $iv = input('post.iv');
        $data = decryptData($appid, define_str_replace($session_key), $encryptedData, define_str_replace($iv));
        if ($data['phoneNumber']) {
            $res = Db::name('user')->where('open_id', $openid)->update(['wx_phone' => $data['phoneNumber']]);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $data['phoneNumber']];
        return json($data);
    }

    /*注册资料*/
    public function register()
    {
        $openid = input('post.openid');
        $code  = trim(input('post.code'));
        //$phone = input('post.phone');
        if (empty($openid)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数', 'data' => []];
            return json($data);
        }
        //如果邀请码存在就验证
        $info = [];
        if ($code) {
            $check = Db::name('user_code')->where(['code' => $code])->field('id,number,name,phone,type,code')->find();
            if ($check) {
                //同步数据
                $update = [
                    'phone' => $check['phone'],
                    'code'  => $check['code'],
                    'type'  => $check['type'],
                    'name'  => $check['name'],
                    'number'  => $check['number'],
                    'type_way' => 1,
                    'user_code_id' => $check['id'],
                    'login_time'   => time(),
                    'first_login_time' => time(),
                ];
                $res = Db::name('user')->where('open_id', $openid)->update($update);
                $info = $check['name'];
            } else {
                $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '邀请码有误', 'data' => []];
                return json($data);
            }
        } else {
            //同步数据
            $update = [
                'login_time'   => time(),
                'first_login_time' => time(),
            ];
            $res = Db::name('user')->where('open_id', $openid)->update($update);
        }

        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $info];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败', 'data' => []];
        }
        return json($data);
    }

    /*测试*/
    public function test()
    {
        $result = Db::name('user_code')->where(['code' => ''])->field('id')->select()->toArray();
        foreach ($result as $key => $value) {
            $code = self::createNonceStr();
            $result[$key]['code'] = $code;
            $result[$key]['updatetime'] = time();
        }
        //更新数据
        $result = Db::name('user_code')->saveAll($result);
        if ($result) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => []];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    private function createNonceStr($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        //验证是否已经存在该邀请码
        $check = Db::name('user_code')->where('code', $str)->find();
        if ($check) {
            self::createNonceStr(6);
        }
        return $str;
    }
}
