<?php

use PHPMailer\PHPMailer\PHPMailer;
$baseuri = BASE_URI;
$link_gr = "<a href='/$baseuri/login'>Login to your VIAMO-World-Backoffice.</a>";
$link_en = "<a href='/$baseuri/login'>Hier in das VIAMO-World-Backoffice einloggen.</a>";
// $from_email = email;
// $subject = "Welcome to Domswiss";

// // Set the email headers
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
  
  <p>Wir freuen uns, dass du dich der VIAMO-Community angeschlossen hast!</p>
  
  <p>Starte jetzt mit uns durch und schreibe mit uns eine einzigartige Geschichte</p>
  <p>Wenn du moechtest, dann gebe diese wirklich einmalige Chance an Menschen weiter. Ganz besonders, wenn diese Menschen zu unserem Leitbild JA sagen koennen:</p>
  <ul>
    <li>Gesundheit erhalten</li>
    <li>Finanzielle Unabhaengigkeit erreichen</li>
    <li>Soziale Vernetzung und Freundschaften leben</li>
  </ul>
  
  <p>Hier kannst du dich in das VIAMO-Backoffice einloggen: </p>
  <p><strong>$link_gr</strong></p>
  <p>Username: $context->username</p>
  <p>Password: $context->password</p>
  
  <p>Dein VIAMO-World-Team</p>
  <p>SPONSOR POWER AG</p>
  <p>Schmiedgasse 6</p>
  <p>CH-9100 Herisau</p>
  <p>www.viamo.world</p>
  <p>Mail: <a href="mailto:support@viamo.world">support@viamo.world</a> </p>

  <hr>
  
  <h2>Hello  $context->first_name</h2>
  
  <p>We are pleased that you have joined the VIAMO community! </p>
  
  <p>Get started with us now and write a unique story with us</p>
  <p>If you want, pass this truly unique opportunity on to people. Especially when these people can say YES to our mission statement:</p>
  <ul>
    <li>Maintain health</li>
    <li>Achieve financial independence</li>
    <li>Live social networking and friendships</li>
  </ul>
  
  <p>You can log in to the VIAMO back office here: </p>
  <p><strong>$link_en</strong></p>
  <p>Username: $context->username</p>
  <p>Password: $context->password</p>
  
  <p>Dein VIAMO-World-Team</p>
  <p>SPONSOR POWER AG</p>
  <p>Schmiedgasse 6</p>
  <p>CH-9100 Herisau</p>
  <p>www.viamo.world</p>
  <p>Mail: <a href="mailto:support@viamo.world">support@viamo.world</a> </p>
</body>
</html>

MSG;

$mail = php_mailer(new PHPMailer());
$mail->isHTML(true);
$mail->Subject = 'Congratulations!!!';
$mail->Body = $message;
$mail->setFrom(email, SITE_NAME."-Registration");  
if (!email_has_valid_dns($context->email)) {
  // $mail->addAddress($context->partner_email, $context->partner_email);
  $_SESSION['msg'][] = "Invalid email, mail not sent to customer: $context->email";
}
$mail->addAddress($context->email, $context->email);

emaillog($msg = "============== Email sending task start ===============");

try {
  $mail->send();
  $msg = "| Success: Registration Email sent to partner successfully {$context->email}";
  emaillog($msg);
  return;
} catch (ErrorException $e) {
  $msg = "| Failed: Registration Email not sent {$context->email}";
  emaillog($msg);
  return;
}
emaillog($msg = "================== End ==================\n"); 
// $eml =  array('email' => $context->email);

// emaillog($msg = "============== Email sending task start ===============");

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