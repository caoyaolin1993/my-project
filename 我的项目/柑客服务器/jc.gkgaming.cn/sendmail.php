<?php
// php 脚本永不过期
set_time_limit(0);
// php 发送邮件，可以  phpmailer

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$redis = include './conn.php';


// Load Composer's autoloader
require './vendor/autoload.php';


// 队列key
$key = 'sendmaillist';

// 读一下队列中是否有记录

$bool = $redis->exists($key);

if (!$bool) return;


while(true){
  // 列表中有数据
  if ($redis->lLen($key)>0) {
      $toMail = $redis->rPop($key);
      sendmail($toMail);

  }
  sleep(2);
}


function sendmail(string $toMail)
{
  // Instantiation and passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.qq.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = '1157089703@qq.com';                     // SMTP username
    $mail->Password   = 'CYL110guo*==';                               // SMTP password
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    // $mail->SMTPSecure = "cfnhhfkvgukjibfd";         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->SMTPSecure = "tls";         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('1157089703@qq.com', 'zhangsan');
    $mail->addAddress($toMail, 'lisi');     // Add a recipient

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = '登录提示';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';

    $mail->send();
    echo "send ik \n";
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}
