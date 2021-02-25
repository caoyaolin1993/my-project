<?php
// 这是系统自动生成的公共文件
use think\facade\Db;

//获取微信手机号
function decryptData($appid, $sessionKey, $encryptedData, $iv)
{
    $OK = 0;
    $IllegalAesKey = -41001;
    $IllegalIv = -41002;
    $IllegalBuffer = -41003;
    $DecodeBase64Error = -41004;

    if (strlen($sessionKey) != 24) {
        return $IllegalAesKey;
    }
    $aesKey = base64_decode($sessionKey);

    if (strlen($iv) != 24) {
        return $IllegalIv;
    }
    $aesIV = base64_decode($iv);

    $aesCipher = base64_decode($encryptedData);

    $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
    $dataObj = json_decode($result);
    if ($dataObj  == NULL) {
        return $IllegalBuffer;
    }
    if ($dataObj->watermark->appid != $appid) {
        return $DecodeBase64Error;
    }
    $data = json_decode($result, true);

    return $data;
}

function define_str_replace($data)
{
    return str_replace(' ', '+', $data);
}

function isIdCard($number)
{ // 检查是否是身份证号
    // $number = getIDCard($number);
    // 转化为大写，如出现x
    $number = strtoupper($number);
    //加权因子
    $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码串
    $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    //按顺序循环处理前17位
    $sigma = 0;
    for ($i = 0; $i < 17; $i++) {
        //提取前17位的其中一位，并将变量类型转为实数
        $b = (int) $number{
            $i};

        //提取相应的加权因子
        $w = $wi[$i];

        //把从身份证号码中提取的一位数字和加权因子相乘，并累加
        $sigma += $b * $w;
    }
    //计算序号
    $snumber = $sigma % 11;

    //按照序号从校验码串中提取相应的字符。
    $check_number = $ai[$snumber];

    if ($number{
        17} == $check_number) {
        return true;
    } else {
        return false;
    }
}


function getTimeWeek($time, $i = 0)
{
    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
    $oneD = 24 * 60 * 60;
    return "周" . $weekarray[date("w", $time + $oneD * $i)];
}


function click($table, $type, $open_id, $course, $content_start, $content_end, $new)
{
    $click_arr = [];
    //查询点击数
    $click = Db::name($table)->where(['open_id' => $open_id, 'course' => $course])->find();
    $start_arr = explode('-', $content_start);
    $end_arr = explode('-', $content_end);
    $click_arr = array();
    for ($i = $start_arr[0]; $i <= $end_arr[0]; $i++) {
        if ($i == $end_arr[0]) {
            if ($table == 'two_click' || $table == 'six_click') {
                $click_arr['v' . $i] = $click['v' . $i] + 1;
            } else {
                $click_arr['v' . $i] = $click['v' . $i] + 1;
                $click_arr['p' . $i] = $click['p' . $i] + 1;
            }
        } else {
            $click_arr['v' . $i] = $click['v' . $i] + 1;
            $click_arr['p' . $i] = $click['p' . $i] + 1;
        }
    }

    if ($new != 'end') {
        if ($start_arr[1] == '1') {
            unset($click_arr['v' . $start_arr[0]]);
        }
        if ($end_arr[1] == '0') {
            unset($click_arr['p' . $end_arr[0]]);
        }
    }

    //判断课程反馈是否需要添加点击次数
    //        if($new == '1'){
    //            if($type == '1'){
    //                $click_arr['feedback'] = '1';
    //            }
    //        }
    //        dump($content_start);dump($content_end);
    //        dump($click_arr);die;
    //记录点击数
    if ($click) {
        Db::name($table)->where(['open_id' => $open_id, 'course' => $course])->update($click_arr);
    } else {
        $click_arr['open_id'] = $open_id;
        $click_arr['course']  = $course;
        $click_arr['createtime'] = time();
        Db::name($table)->insertGetId($click_arr);
    }
}
