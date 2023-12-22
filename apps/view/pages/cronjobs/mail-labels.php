<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mg = 1;
$db = new Dbobjects;
$force_send = false;
if (isset($context['orderid'])) {
    $ordid = $context['orderid'];
    $invoices = $db->show("select id, invoice, country from payment where payment.id='$ordid' and invoice is NOT NULL order by id desc limit 150");
    $force_send = true;
} else {
    $force_send = false;
    $invoices = $db->show("select id, invoice, country from payment where invoice is NOT NULL order by id desc limit 150");
}
foreach ($invoices as $key => $inv) {
    $sql = "select logistic_email as to_mail from countries where name = '{$inv['country']}'";
    $logistic = $db->showOne($sql);
    $to_mail = $logistic['to_mail'];
    $lebel_name = "label-{$inv['invoice']}.pdf";
    $media_path = "/media/docs/labels/$lebel_name";
    $file_path = RPATH . $media_path;
    if ($to_mail != '' && file_exists($file_path)) {
        if (!checkLog($db = $db, $path = $media_path, $mg = $mg) || $force_send === true) {
            try {
                $mailObj = new PHPMailer(true);
                $mail = php_mailer($mailObj);
                $mail->setFrom(email, SITE_NAME);
                $mail->addAddress($to_mail, $lebel_name);
                // Attachments
                $mail->addAttachment($file_path, $lebel_name);    // Add attachments
                // Content
                $mail->isHTML(true);                                    // Set email format to HTML
                $mail->Subject = "Lieferschein Order {$inv['id']}";
                $mail->Body    = "Please find attachment for order: {$inv['id']}";
                // Send the email
                set_time_limit(120);
                if ($mail->send()) {
                    saveLog($db = $db, $from_mail = email, $to_mail = $to_mail, $path = $media_path, $mg = $mg);
                    echo "Sent";
                }
            } catch (Exception $e) {
                echo "Not sent";
            }
        }
    }
}


function saveLog($db, $from_mail, $to_mail, $path, $mg = 1)
{
    try {
        $created_at = date('Y-m-d H:i:s');
        $pdo = $db->pdo;
        $sql = "INSERT INTO 
        mail_logs (path, created_at, from_mail, to_mail, mail_group) 
        VALUES (:path, :created_at, :from_mail, :to_mail, :mail_group)";
        $params = [
            ':path' => $path,
            ':created_at' => $created_at,
            ':from_mail' => $from_mail,
            ':to_mail' => $to_mail,
            ':mail_group' => $mg
        ];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (PDOException $e) {
        return false; // Or throw the exception again if you want to propagate it
    }
    return true; // Return true if the operation is successful
}
function checkLog($db, $path, $mg)
{
    return $db->showOne("SELECT id FROM mail_logs WHERE path = '$path' AND mail_group = '$mg'");
}
