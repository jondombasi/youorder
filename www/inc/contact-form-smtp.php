<?php
require 'php-mailer/phpmailerautoload.php';
$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

$to = 'yourmail@gmail.com';
$myname = 'yourname';

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->SMTPDebug = 0;                                 // Enable verbose debug output

$mail->Host = 'smtp.gmail.com';  					  // Specify main and backup SMTP servers
$mail->Username = 'yourmail@gmail.com';               // SMTP username
$mail->Password = 'yourpassword';                          // SMTP password
$mail->Port = 587;                                    // TCP port to connect to
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted

$mail->Debugoutput = 'html';

if( isset($_POST['subject']) )
{
	$subject = $_POST['subject'];
}else{
    $subject = "no subject";
}

//$subject = $_POST['subject'];
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

$mail->From = $email;
$mail->FromName = $name;
$mail->addAddress($to,$myname); // Add a recipient
$mail->isHTML(true);

$order   = array("\r\n", "\n", "\r");
$replace = '<br />';
$html_messasge = str_replace($order, $replace, $message);

$mail->Subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
$mail->Body = $html_messasge;
$mail->AltBody    = $message;


if (!$mail->send()){
	$arrResult = 'error';
} else{
	$arrResult = 'success';
}

echo $arrResult;

?>
