<?php
$Name=$Fname;
$Email=$email;
$Message=$MGS;
require 'email/PHPMailerAutoload.php';
$mail= new PHPMailer;
$mail->isSMTP();
$mail->Host='smtp.gmail.com';
$mail->Port=587;
$mail->SMTPAuth=true;
$mail->SMTPSecure='tls';

$mail->Username='';
$mail->Password='';

$mail->setFrom("senderName");
$mail->addAddress($Email);
$mail->addReplyTo('noreply@gmail.com');

$mail->isHTML(true);
$mail->Subject=('From name');
$Message1=" sent from = $Fname  <br> mail adress = $Email<br>";
$Message1 .= $MGS;
$mail->Body=$Message1;

if(!$mail->send())
{
	echo"<p class='errormsg'>Send Unsuccess</p>";	
}
else
{
	echo"<p class='errormsg'>Send success</p>";	
}
?>