<?php

use PHPMailer\PHPMailer\PHPMailer;

// $from_email = email;
// $subject = "Welcome to Domswiss";

// Set the email headers
// $headers = "MIME-Version: 1.0" . "\r\n";
// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
// $headers .= "From: $from_email" . "\r\n";
$message = <<<MSG
<!DOCTYPE html>
<html>
<head>
  <title>Welcome to VIAMO</title>
</head>
<body>
<h2>Hallo $context->first_name</h2>
  
  <p>Herzlichen Glueckwunsch - ein neues Mitglied hat sich bei dir registriert: </p>
  <p>Username: $context->username</p>
  <p>Name: $context->name</p>
  <p>Mailadresse: $context->email</p>
  <p>Telefon: $context->mobile</p>
  
  
  <p>Bitte setze dich mit dem neuen Mitglied in Verbindung und unterstütze, damit die VIAMO-Community schnell weiter wachsen kann.</p><br>

  <p>Dein VIAMO-World-Team</p>
  <p>SPONSOR POWER AG</p>
  <p>Schmiedgasse 6</p>
  <p>CH-9100 Herisau</p>
  <p>www.viamo.world</p>
  <p>Mail: <a href="http://support@viamo.world">support@viamo.world</a> </p>

  <hr>
  
  <h2>Hello $context->first_name</h2>
  
  <p>Congratulations - a new member has registered with you: </p>
  <p>Username: $context->username</p>
  <p>Name: $context->name</p>
  <p>Email address: $context->email</p>
  <p>Phone: $context->mobile</p>
  
  
  <p>Please contact the new member and support so that the VIAMO community can continue to grow quickly.</p><br>

  <p>Your VIAMO World team</p>
  <p>SPONSOR POWER AG</p>
  <p>Schmiedgasse 6</p>
  <p>CH-9100 Herisau</p>
  <p>www.viamo.world</p>
  <p>Email: <a href="http://support@viamo.world">support@viamo.world</a> </p>
  
</body>
</html>

MSG;

$mail = php_mailer(new PHPMailer());
$mail->isHTML(true);
$mail->Subject = 'Congratulations!!!';
$mail->Body = $message;
$mail->setFrom(email, SITE_NAME."-Partner registration");  
if (!email_has_valid_dns($context->email)) {
  // $mail->addAddress($context->partner_email, $context->partner_email);
  $_SESSION['msg'][] = "Invalid email, mail not sent to customer: $context->email";
}
$mail->addAddress($context->email, $context->email);

emaillog($msg = "============== $context->email Email sending task start ===============");

try {
  $mail->send();
  $msg = "| Success: Partner Registration Email sent to partner successfully {$context->email}";
  emaillog($msg);
  return;
} catch (ErrorException $e) {
  $msg = "| Failed: Partner Registration Email not sent {$context->email}";
  emaillog($msg);
  return;
}
emaillog($msg = "================== End ==================\n"); 
// $eml =  array('email' => $context->email);



// $vl =   (object) $eml;
// $to = $vl->email;
// try {
//   if (mail($to, $subject, $message, $headers)) {
//     $msg = "| Success: Registration Email sent successfully {$to}";
//     emaillog($msg);
//   } else {
//     $msg = "| Failed: Registration Email not sent {$to}";
//     emaillog($msg);
//   }
// } catch (\Throwable $th) {
//   $msg = "| Error: $th while sending $to";
//   emaillog($msg);
// }

// emaillog($msg = "================== End ==================\n"); 

// mail($to, $subject, $message, $headers);
// echo $message;