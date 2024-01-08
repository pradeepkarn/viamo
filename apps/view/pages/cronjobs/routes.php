<?php
$url = $url = explode("/", $_SERVER["QUERY_STRING"]);
$namespace = $url[0];
$home = home;
if ("{$url[0]}/{$url[1]}" == "{$namespace}/mail-shipping-labels") {
  import("apps/view/pages/cronjobs/mail-labels.php");
  return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/mail-single-shipping-label") {
  if (isset($_GET['orderid'])) {
    import("apps/view/pages/cronjobs/mail-labels.php",['orderid'=>$_GET['orderid']]);
  }
  return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/generate-invoices-pdf") {
  import("apps/view/pages/cronjobs/generate-invoices-pdf.php");
  return;
}