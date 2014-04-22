<?php
require_once ('class.phpmailer.php');
$mail = new PHPMailer();
$body = '有货了，试试看';
$mail->CharSet = "UTF-8";
$mail->IsSMTP();
$mail->SMTPDebug = 1;
$mail->SMTPAuth = TRUE;
$mail->SMTPSecure = "ssl";
$mail->Host = "smtp.exmail.qq.com";
$mail->Port = 465;
$mail->Username = "service@xxtime.com";
$mail->Password = "********";
$mail->Subject = 'Notice From XXtime';
$mail->SetFrom('service@xxtime.com', 'xxtime service');
$mail->AddReplyTo('service@xxtime.com', 'xxtime service');
$mail->AddAddress('xxtime@xxtime.com', "Joe");
$mail->MsgHTML($body);

if (! $mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent successfully";
}
