<?php

include_once "wxBizMsgCrypt.php";

// 第三方发送消息给公众平台
$encodingAesKey = "yydd7x0TBcSEIwULdV0EXoV1SPL1LFqwZcX4YPp5IwD";
$token = "Z6BjOdwq1jjwZcG8fG1ym8QBbMuLSgcZZb9tH1eU1r8E";
// $timeStamp = "1409304348";
// $nonce = "xxxxxx";
$appId = "wx84b9a45b5e839547";
// $text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";


$timeStamp  = empty($_GET['timestamp'])     ? ""    : trim($_GET['timestamp']);
$nonce      = empty($_GET['nonce'])     ? ""    : trim($_GET['nonce']);
$msg_sign   = empty($_GET['msg_signature']) ? ""    : trim($_GET['msg_signature']);
$encryptMsg = file_get_contents('php://input', 'r');


libxml_disable_entity_loader(true);
$result = json_decode(json_encode(simplexml_load_string($encryptMsg, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
// $encryptMsg = '';
// $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);

// if ($errCode == 0) {
// 	var_dump($encryptMsg);die;
// 	// print("加密后: " . $encryptMsg . "\n");
// } else {
// 	print($errCode . "\n");
// }

$xml_tree = new DOMDocument();
$xml_tree->loadXML($encryptMsg);
$array_e = $xml_tree->getElementsByTagName('Encrypt');
// $array_s = $xml_tree->getElementsByTagName('MsgSignature');
$encrypt = $array_e->item(0)->nodeValue;
// $msg_sign = $array_s->item(0)->nodeValue;

$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
$from_xml = sprintf($format, $encrypt);

// 第三方收到公众号平台发送的消息
$msg = '';
$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
if ($errCode == 0) {
	$xml = new \DOMDocument();
	$xml->loadXML($msg);
	$array_e = $xml->getElementsByTagName('ComponentVerifyTicket');
	$component_verify_ticket = $array_e->item(0)->nodeValue;
	file_put_contents('data.txt','解密后的component_verify_ticket是：'.$component_verify_ticket);
	echo 'success';
	exit;
} else {
	file_put_contents('data.txt',"出错了：".$errCode);
	echo 'fail';
	exit;
}
