<?php
$url = $url = explode("/", $_SERVER["QUERY_STRING"]);
$namespace = $url[0];
if ("{$url[0]}/{$url[1]}" == "{$namespace}/shop") {
    import("apps/view/pages/public/products.php");
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/add-to-cart-ajax") {
    $cart = new PubCart_ctrl;
    $cart->add_or_remove($action = 'add');
    echo RELOAD;
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/remove-from-cart-ajax") {
    $cart = new PubCart_ctrl;
    $cart->add_or_remove($action = 'remove');
    echo RELOAD;
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/payment") {
    import("apps/view/pages/public/payment.php");
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/purchase-decrease-qty-ajax") {
    if (!isset($_SESSION['guest_id'])) {
        echo js_alert('Session expired');
        return;
    }

    $cart = new PubCart_ctrl;
    $cart->add_or_remove($action = 'remove');
    echo RELOAD;
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/purchase-increase-qty-ajax") {
    if (!isset($_SESSION['guest_id'])) {
        echo js_alert('Session expired');
        return;
    }
    $cart = new PubCart_ctrl;
    $cart->add_or_remove($action = 'add');
    echo RELOAD;
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/signup-ajax") {
    // myprint($_POST);
    // return;
    $userid = usersignup();
    if (intval($userid)) {
      createAddess($userid = $userid, $post = $_POST);
      $user = (object)(getData('pk_user', $userid));
      if (isset($_SESSION['guest_id'])) {
        $db=new Dbobjects;
        $db->tableName="customer_order";
        $carr['user_id']=$_SESSION['guest_id'];
        $carr['status']="cart";
        $db->filter($carr);
        $db->insertData['user_id']=$userid;
        $db->update();
      }
      $obj = new stdClass;
      $obj->partner_email = null;
      $obj->partner_fname = null;
      $obj->partner_lname = null;
      if (intval($user->ref) > 0) {
        $referby = (object)(getData('pk_user', $user->ref));
        $obj->partner_email = $referby->email;
        $obj->partner_fname = $referby->first_name;
        $obj->partner_lname = $referby->last_name;
      }
      $obj->email = $_POST['email'];
      $obj->username = $user->username;
      $obj->first_name = $user->first_name;
      $obj->last_name = $user->last_name;
      $obj->mobile = $user->mobile;
      $obj->password = $_POST['password'];
      // Send signup email template
      send_sign_up_email($obj);
      // template end
      echo js_alert(msg_ssn(return: true));
      echo go_to("payment");
      return;
    } else {
      msg_ssn();
      return;
    }
    exit;
  }