<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Db;

class ApiAuth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //
        $appId =  "wx6ada410c648a0fa9";
        $appSecret = "52335505cd308b4d294651772028bfc7";
        $data = $request->param();
        $find = Db::name('access_token')->find();
        if ($find) {
            if ($find['expires_in'] < time()) {
                $access_token = $this->getAccessToken($appId, $appSecret);
                Db::name('access_token')->where('id', $find['id'])->update([
                    'access_token' => $access_token,
                    'expires_in' => time() + 7200
                ]);
            } else {
                $access_token = $find['access_token'];
            }
        } else {
            $access_token = $this->getAccessToken($appId, $appSecret);
            Db::name('access_token')->insertGetId([
                'access_token' => $access_token,
                'expires_in' => time() + 7200
            ]);
        } 

        $str = '';
        foreach ($data as $k => $v) {
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
                $data = ['code' => -100, 'msg' => '内容含有违法违规内容'];
                return json($data);
            }
        }
        return $next($request);
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


  //发送post请求
  function curlPost($url, $data)
  {
      $ch = curl_init();
      $params[CURLOPT_URL] = $url;    //请求url地址
      $params[CURLOPT_HEADER] = FALSE; //是否返回响应头信息
      $params[CURLOPT_SSL_VERIFYPEER] = false;
      $params[CURLOPT_SSL_VERIFYHOST] = false;
      $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
      $params[CURLOPT_POST] = true;
      $params[CURLOPT_POSTFIELDS] = $data;
      curl_setopt_array($ch, $params); //传入curl参数
      $content = curl_exec($ch); //执行
      curl_close($ch); //关闭连接
      return $content;
  }
}
