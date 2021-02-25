<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\util\ReturnCode;
use app\util\ReturnMsg;
use DOMDocument;
use think\facade\Cache;
use app\tool\WXBizMsgCrypt;
use think\facade\Db;

class ThirdParty extends cmPrefix
{
    public function index()
    {
        $encodingAesKey = "yydd7x0TBcSEIwULdV0EXoV1SPL1LFqwZcX4YPp5IwD";
        $token = "Z6BjOdwq1jjwZcG8fG1ym8QBbMuLSgcZZb9tH1eU1r8E";
        $appId = "wx84b9a45b5e839547";

        $timeStamp = empty(input('get.timestamp')) ? "" : trim(input('get.timestamp'));
        $nonce = empty(input('get.nonce')) ? "" : trim(input('get.nonce'));
        $msg_sign = empty(input('get.msg_signature')) ? "" : trim(input('get.msg_signature'));
        $encryptMsg = file_get_contents('php://input');

        $pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);

        $xml_tree = new DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;

        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);

        // 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            $xml = new DOMDocument();
            $xml->loadXML($msg);
            $array_e = $xml->getElementsByTagName('ComponentVerifyTicket');
            $component_verify_ticket = $array_e->item(0)->nodeValue;
            Cache::set('component_verify_ticket', $component_verify_ticket);
            echo 'success';
            exit;
        } else {
            echo 'fail';
            exit;
        }
    }

    public function get_ticket()
    {
        $component_verify_ticket = Cache::get('component_verify_ticket');
        if (!$component_verify_ticket) {
            $component_verify_ticket = '';
        }

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $component_verify_ticket);
    }

    public function callback()
    {
    }


    public function test()
    {
        $timeStamp = empty(input('get.timestamp')) ? "" : trim(input('get.timestamp'));
        $nonce = empty(input('get.nonce')) ? "" : trim(input('get.nonce'));
        $msg_sign = empty(input('get.msg_signature')) ? "" : trim(input('get.msg_signature'));
        $encryptMsg = file_get_contents('php://input');

        $arra = [
            'timeStamp' => $timeStamp,
            'nonce' => $nonce,
            'msg_sign' => $msg_sign,
            'encryptMsg' => $encryptMsg,
        ];

        dump($arra);
    }

    public function get_token()
    {
        // if (Cache::get('component_access_token')) {
        //     return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS,Cache::get('component_access_token'));
        // }

        $url  = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
        $data['component_appid'] = 'wx84b9a45b5e839547';
        $data['component_appsecret'] = '7aa305c4f8c4abd24a6d96be5f57d0c8';
        $data['component_verify_ticket'] = Cache::get('component_verify_ticket');

        $da = json_encode($data);
        $result =  $this->https_request($url, $da);
        $arr = json_decode($result, true);
        Cache::set('component_access_token', $arr['component_access_token'], $arr['expires_in']);
        return $arr['component_access_token'];
    }

    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        $header[] = "Content-type: text/xml";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function get_pre_auth_code()
    {
        // if (Cache::get('pre_auth_code')) {
        //     return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, Cache::get('pre_auth_code'));
        // }

        if (Cache::get('component_access_token')) {
            $component_access_token = Cache::get('component_access_token');
        } else {
            $component_access_token = $this->get_token();
        }

        $url  = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=" . $component_access_token;

        $data['component_appid'] = 'wx84b9a45b5e839547';
        $da = json_encode($data);

        $arr =  $this->https_request($url, $da);

        $result = json_decode($arr, true);
        // Cache::set('pre_auth_code', $result['pre_auth_code'], $result['expires_in']);

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $result['pre_auth_code']);
    }

    public function get_auth_code()
    {
        Cache('auth_code', request()->param('auth_code'), request()->param('expires_in'));

        if (Cache::get('component_access_token')) {
            $component_access_token = Cache::get('component_access_token');
        } else {
            $component_access_token = $this->get_token();
        }

        $url  = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=" . $component_access_token;

        $data['component_appid'] = 'wx84b9a45b5e839547';
        $data['authorization_code'] = Cache::get('auth_code');
        // $data['authorization_code'] = "queryauthcode@@@EUjxOeW60UfQBD9zJAiWaGTezYCm_gZzlTHDS2g1qI8dDAYzVHr3QU45AcRTH0n8mxWE3QLMikk-zoakPMWAjg";
        $da = json_encode($data);
        $arr =  $this->https_request($url, $da);
        $result = json_decode($arr, true);
        if (isset($result['errcode'])) {
            return_msg(ReturnCode::INVALID,ReturnMsg::FAIL,$result['errmsg']);
        }

        // $res_a = Db::name('refresh_token')->insert(['refresh_token'=>$result['authorization_info']['authorizer_refresh_token'],'access_token'=>$result['authorization_info']['authorizer_access_token']]);

        Cache::set('authorizer_access_token', $result['authorization_info']['authorizer_access_token'], $result['authorization_info']['expires_in']);
        Cache::set('authorizer_refresh_token', $result['authorization_info']['authorizer_refresh_token']);
        return_msg(ReturnCode::SUCCESS,ReturnMsg::SUCCESS,$result);
    }
}
