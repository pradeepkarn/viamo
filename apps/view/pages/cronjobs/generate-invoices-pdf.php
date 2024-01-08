<?php

use FontLib\Table\Type\head;

$mg = 1;
$db = new Dbobjects;
$invoices = $db->show("select id, invoice from payment where payment.status='paid' and invoice is NOT NULL order by id desc limit 100");
foreach ($invoices as $key => $inv) {
    $invoice_name = "invoice-{$inv['invoice']}.pdf";
    $media_path = "/media/docs/invoices/$invoice_name";
    $file_path = RPATH . $media_path;
    if (!file_exists($file_path)) {
        header("Location:/".home."/my-orders/?orderid={$inv['id']}");
        return;
    }
}
