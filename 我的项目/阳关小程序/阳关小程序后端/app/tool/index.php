<?php
$url         = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";

// $data  = '
// {
//           "component_appid":"wx84b9a45b5e839547",
//           "component_appsecret":"7aa305c4f8c4abd24a6d96be5f57d0c8",
//           "component_verify_ticket":"ticket@@@85Cbr-gwaO8K00wLjIvs_5IO-xY_IMkTNeR7RiTDOPPUuLxb1DnGpW2uU7cS7tbm83CWQTufmDgaLDGpwz3T9w",
// }
// ';

$data['component_appid'] = 'wx84b9a45b5e839547';
$data['component_appsecret'] = '7aa305c4f8c4abd24a6d96be5f57d0c8';
$data['component_verify_ticket'] = 'ticket@@@85Cbr-gwaO8K00wLjIvs_5IO-xY_IMkTNeR7RiTDOPPUuLxb1DnGpW2uU7cS7tbm83CWQTufmDgaLDGpwz3T9w';

$da = json_encode($data);


$result = https_request($url,$da);
var_dump($result);


function https_request($url, $data = null)
{
  $curl = curl_init();
  $header[] = "Content-type: text/xml";
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
  if (!empty($data)) {
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  }
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($curl);
  curl_close($curl);
  return $output;
}