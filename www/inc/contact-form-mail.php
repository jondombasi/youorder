<?php
require 'php-mailer/phpmailerautoload.php';
$to = 'yourmail@gmail.com';
$myname = 'yourname';

if( isset($_POST['subject']) )
{
	$subject = $_POST['subject'];
}else{
    $subject = "no subject";
}
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';

$mail->isSendmail();

$mail->Subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
$mail->setFrom($email, $name);
$mail->addAddress($to,$myname);
$mail->IsHTML(true);
$mail->Body = preg_replace('/\[\]/','',$message);
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

if (!$mail->send()){
	$arrResult = 'error';
} else{
	$arrResult = 'success';
}

echo $arrResult;
?>