<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class cmPrefix
{
    protected $data;

    public function __construct()
    {
        $this->data = request()->param();
        $this->security();
    }

    public function security()
    {
        $appId =  "wx6ada410c648a0fa9";
        $appSecret = "52335505cd308b4d294651772028bfc7";

        $find = Db::name('access_token')->order('id')->limit(1)->select()->toArray();
        if ($find) {
            if ($find[0]['expires_in'] < time()) {
                $access_token = $this->getAccessToken($appId, $appSecret);
                Db::name('access_token')->where('id', $find[0]['id'])->update([
                    'access_token' => $access_token,
                    'expires_in' => time() + 7200
                ]);
            } else {
                $access_token = $find[0]['access_token'];
            }
        } else {
            $access_token = $this->getAccessToken($appId, $appSecret);
            Db::name('access_token')->insertGetId([
                'access_token' => $access_token,
                'expires_in' => time() + 7200
            ]);
        }

        $str = '';
        foreach ($this->data as $k => $v) {
            if (is_string($v) && !empty($v)) {
                $str .= $v;
            }
        }

        if (!empty($str)) {
            $url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token=" . $access_token;
            $data = '
        {
            "content":"' . $str . '"
        }
        ';
            $content = json_decode($this->https_request($url, $data), true);

            if ($content['errcode'] != 0) {
                return_msg(ReturnCode::INVALID,'内容含有违法违规内容');
            }
        }

    }

    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    // 小程序 appID 和 appSecret 获取 token   
    function getAccessToken($appId, $appSecret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
        $html = file_get_contents($url);
        $output = json_decode($html, true);
        $access_token = $output['access_token'];
        return $access_token;
    }
}
