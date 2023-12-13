<?php

use PHPMailer\PHPMailer\PHPMailer;
$sitename = SITE_NAME;
$context->order_amt = round($context->order_amt,2);
$baseuri = BASE_URI;
$link_en = "<a href='$baseuri/login'>Login to your VIAMO-World-Backoffice.</a>";
$link_gr = "<a href='$baseuri/login'>Hier in das VIAMO-World-Backoffice einloggen.</a>";
$message = <<<MSG
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation</title>
</head>

<body>
  <div style="max-width: 100%; margin: 0 auto; font-family: Arial, sans-serif; line-height: 1.6; font-size:12px">
    <h2>Vielen Dank fuer die Bestellung!</h2>
    <p>Deine Bestellung mit der Nummer <strong>$context->order_id</strong> wurde erfolgreich verbucht.</p>
    <p>Gesamtsumme der betraegt <strong>Euro $context->order_amt</strong></p>
    <p>Bitte ueberweise die Gesamtsumme auf das folgende Konto:</p>
    $context->bank_account
    <p>Bitte bei Zahlungsreferenz "<strong>Order Nr. $context->order_id</strong>" angeben</p>
   
    <p>$link_gr</p> 
    <p>Powervolle Gruesse dein</p>
    <p>$sitename - World of Energy</p>
    <p>Backoffice-Team</p>

  <hr>
  
    <h2>Thank you for your order!</h2>
    <p>Your order with the number <strong>$context->order_id</strong> has been successfully processed.</p>
    <p>Total amount of the order is: <strong>Euro $context->order_amt</strong></p>
    <p>Please transfer the total amount to the following bank account:</p>
    $context->bank_account
    <p>Please use "<strong>Order Nr. $context->order_id</strong>" as payment reference.</p>

    <p>$link_en</p>
   <p>Powerful geetings from your </p>
    <p>$sitename - World of Energy</p>
    <p>Backoffice-Team</p>
</div>

</body>

</html>

MSG;

$mail = php_mailer(new PHPMailer());
$mail->isHTML(true);
$mail->Subject = $sitename.': Neue Bestellung';
$mail->Body = $message;
$mail->setFrom(orderemail, SITE_NAME . "-New Order");
if (!email_has_valid_dns($context->email)) {
  $_SESSION['msg'][] = "Invalid email, mail not sent to customer: $context->email";
}
$mail->addAddress($context->email, $context->email);

if (!email_has_valid_dns($context->email)) {
  $_SESSION['msg'][] = "Invalid email, mail not sent to customer: $context->email";
}
$mail->addAddress($context->email, $context->email);

emaillog($msg = "============== Order Email sending task start ===============");

try {
  $mail->send();
  $msg = "| Success: Order email sent to partner successfully {$context->email}";
  emaillog($msg);
  emaillog($msg = "================== End ==================\n");
  return true;
} catch (ErrorException $e) {
  $msg = "| Failed: Order email not sent {$context->email}";
  emaillog($msg);
  emaillog($msg = "================== End ==================\n");
  return false;
}

