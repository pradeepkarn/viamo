<?php

use PHPMailer\PHPMailer\PHPMailer;

class Order_ctrl
{
    public function place()
    {
        $req = (object) ($_POST);
        $net_amt = 0;
        if ($req) {
            $arr = null;
            // $placeOrder = new Model('payment');
            $addrs = get_my_primary_address(USER['id']);
            if ($addrs == false) {
                $_SESSION['msg'][] = 'No primary address found';
                return false;
            }
            if (!isset($_POST['payment_mode'])) {
                $_SESSION['msg'][] = 'Please select payment mode';
                return false;
            }
            if ($_POST['payment_mode'] == '') {
                echo js_alert('Please select payment mode');
                return;
            }
            $level = new Member_ctrl;
            $dbobj = new Dbobjects;
            $vctrl = new Voucher_ctrl;
            $con = $dbobj->dbpdo();
            $con->beginTransaction();
            $sql = "select SUM(price*qty) as total_amt, SUM(pv*qty) as total_pv, SUM(rv*qty) as total_rv, SUM(direct_bonus*qty) as total_db from customer_order where status = 'cart' and payment_id='0' and user_id = {$_SESSION['user_id']}";
            if (count($dbobj->show($sql)) == 0) {
                $_SESSION['msg'][] = 'Your cart is empty!';
                return;
            }
            $total_gm = 0;
            $cart_list = (object) ($dbobj)->show("select * from customer_order where status = 'cart' and user_id = '{$_SESSION['user_id']}'");
            foreach ($cart_list as $cv) :
                $cv = (object) $cv;
                $item = (object) ($dbobj)->showOne("select id,jsn from item where id = '$cv->item_id'");
                $phpobj = json_decode($item->jsn);
                $gm = 0;
                foreach ($phpobj->items as $pkey => $prd) {
                    $prod = (object) ($dbobj)->showOne("select id,qty,unit from item where id = '$prd->item'");
                    $gm += calculate_gram($prod, $cv->qty * $prd->qty);
                    $total_gm += $gm;
                }
            endforeach;
            $shipping_cost = calculate_shipping_cost(db: $dbobj, gram: $total_gm, ccode: $addrs->country_code);
            if (!isset($req->total_gm) || !isset($req->shipping_cost)) {
                $_SESSION['msg'][] = 'Shiping not defined';
                $con->rollback();
                return false;
            }
            if (!($total_gm == $req->total_gm && $shipping_cost == $req->shipping_cost)) {
                $_SESSION['msg'][] = "Shiping cost mismatched, try again $shipping_cost = $req->shipping_cost";
                $con->rollback();
                return false;
            }
            try {
                $total_amt = $dbobj->show($sql)[0]['total_amt'];
                $total_pv = $dbobj->show($sql)[0]['total_pv'];
                $total_rv = $dbobj->show($sql)[0]['total_rv'];
                $total_db = $dbobj->show($sql)[0]['total_db'];




                $vdamt = 0;
                $vchrjson = null;
                if (isset($_SESSION['voucher_code'])) {
                    if ($_SESSION['voucher_code'] != "") {
                        $vchr = $vctrl->get_voucher($code = $_SESSION['voucher_code'], $amt = $total_amt);
                        $vchrjson = json_encode($vchr);
                        if ($vchr) {
                            $vdamt = $vchr->discount;
                            $bonus_percentagge = round(((($total_db / $total_amt) * 100) - $vchr->value), 2);
                            if ($bonus_percentagge <= 0) {
                                $bonus_percentagge = 0;
                            }
                            $total_db = round((($total_amt) * ($bonus_percentagge / 100)), 2);
                            // $_SESSION['msg'][] = "bns perc $bonus_percentagge % db $total_db";
                        }
                    }
                }

                if (!(floatval($vdamt) == floatval($req->vdamt))) {
                    $_SESSION['msg'][] = "Check voucher amount";
                    $con->rollback();
                    return false;
                }
                // return;
                $arr['amount'] = $total_amt;
                $arr['shipping_cost'] = $req->shipping_cost;
                $arr['total_gm'] = $total_gm;
                $arr['pv'] = $total_pv;
                $arr['rv'] = $total_rv;
                $arr['direct_bonus'] = $total_db;
                $arr['point_used'] = 0;
                $arr['voucher_amt'] = $vdamt;
                $arr['voucher_jsn'] = $vchrjson;
                $arr['discount_by_bpt'] = 0;
                $point = $level->net_balance_minus_requested_balance($db = $dbobj, $myid = USER['id']);
                $commission = true;
                if (isset($req->redeem_point)) {
                    // if ($point != $req->point) {
                    //     $_SESSION['msg'][] = 'Point missmatched';
                    //     $con->rollback();
                    //     return false;
                    // }
                    if ($total_amt <= $point) {
                        $commission = false;
                        $arr['point_used'] = $total_amt;
                        $arr['discount_by_bpt'] = $total_amt;
                    } else if ($total_amt >= $point && $point >= 0) {
                        $commission = false;
                        $arr['point_used'] = $point;
                        $arr['discount_by_bpt'] = $point;
                    }
                }
            } catch (PDOException $e) {
                return false;
            }
            if (
                $addrs->name == '' ||
                $addrs->mobile == '' ||
                $addrs->city == '' ||
                $addrs->country == '' ||
                $addrs->zipcode == '' ||
                $addrs->isd_code == ''
            ) {
                $_SESSION['msg'][] = 'Please check your primary address and make sure you have entered all the details';
                return false;
            }
            // $dbobj->tableName = "payment";
            $arr['user_id'] = $_SESSION['user_id'];
            $arr['name'] = $addrs->name;
            $arr['mobile'] = $addrs->mobile;
            $arr['address'] = $addrs->address_name;
            $arr['city'] = $addrs->city;
            $arr['state'] = isset($addrs->state) ? $addrs->state : null;
            $arr['country'] = $addrs->country;
            $arr['zipcode'] = $addrs->zipcode;
            $arr['isd_code'] = $addrs->isd_code;
            $arr['payment_method'] = sanitize_remove_tags($_POST['payment_mode']);
            // $arr['pv'] = 0;

            $arr['unique_id'] = uniqid();

            $arr['status'] = $_POST['payment_mode'] == 'init';

            $arr['updated_at'] = $_POST['payment_mode'] == 'Bank_transfer' ? null : date('Y-m-d H:i:s');

            try {
                $redeempt = 0;
                $dbobj->tableName = "payment";
                $dbobj->insertData = $arr;
                $redeempt = $arr['point_used'];
                // myprint($dbobj->insertData);
                // echo $dbobj->create_sql();
                $ordernum = $arr['unique_id'];
                $pay = $dbobj->create();
                $net_amt = round(($arr['amount'] + $arr['shipping_cost'] - $arr['discount_by_bpt'] - $arr['voucher_amt']), 2);
                // return;
                if (intval($pay)) {
                    $level = new Member_ctrl;
                    $db = $dbobj;
                    $trnArr['transactedTo'] = USER['id'];
                    $trnArr['transactedBy'] = USER['id'];
                    // if ($commission == false) {
                    if ($redeempt > 0) {
                        $trnArr['transactedTo'] = USER['id'];
                        $trnArr['transactedBy'] = USER['id'];
                        $trnArr['purchase_amt'] = round($total_amt, 2);
                        $trnArr['amount'] =  $redeempt;
                        $trnArr['real_amt'] =  $redeempt;
                        $trnArr['trnNum'] = $ordernum;
                        $trnArr['status'] = 1; // 1: Active, 2: cancelled  
                        $trnArr['trnGroup'] = 5; // 1:pv commissions, 2: direct bonus
                        $trnArr['trnType'] = 2; // 1: Credit, 2: debit
                        $level->save_trn_data($db, $trnArr);
                    }
                }
                if (intval($pay) && $arr['status'] == 'paid') {
                    $invid = generate_invoice_id($dbobj);
                    update_inv_if_not($pay, $invid, $dbobj);
                    // credit commission on paid update member as well
                    $this->send_direct_bonus($db, $buyer = USER, $ordernum, $total_amt, $total_db, $redeempt, $level);
                    // $net_amt = round(($arr['amount']+$arr['shipping_cost']-$arr['discount_by_bpt']-$arr['voucher_amt']),2);
                    $arr = null;
                }
                #################### Direct Bonus end #######################
                $con->commit();
            } catch (PDOException $th) {
                $_SESSION['msg'][] = $th;
                // echo $th;
                $pay = false;
                $con->rollback();
            }
            if (intval($pay)) {
                $dbobj = new Dbobjects;
                $dbobj->tableName = 'customer_order';
                // Filter user cart
                $dbobj->filter(array('status' => 'cart', 'user_id' => $_SESSION['user_id']));
                // Update cart with payment data
                $dbobj->insertData['payment_id'] = $pay;
                $dbobj->insertData['status'] = 'paid';
                $dbobj->insertData['updated_at'] = date('Y-m-d H:i:s');
                // execute payment data
                $dbobj->update();
                // $_SESSION['msg'][] = 'Order placed';
                $my_email = null;

                if (isset($_SESSION['user_id'])) {
                    $userid = $_SESSION['user_id'];
                    $invite = getData("pk_user", $userid);
                    $my_email = $invite != false ? $invite['email'] : null;
                }
                $shpadrs = get_my_primary_address($userid = USER['id']);
                $bank = get_invoice_address($country_code = $shpadrs->country_code)->bank;
                new_order_email(obj([
                    'email' => $my_email,
                    'order_id' => $pay,
                    'order_amt' => $total_amt,
                    'net_amt' => $net_amt,
                    'shipping_cost' => $req->shipping_cost,
                    'bank_account' => $bank
                ]));

                return (object) array(
                    'orderid' => $ordernum,
                    'payment_method' => sanitize_remove_tags($_POST['payment_mode'])
                );
            } else {
                $_SESSION['msg'][] = 'Order not placed';
                echo js_alert(msg_ssn(return: true));
                return false;
            }
        }
    }
    public function confirm_order_status($id, $dataObj)
    {
        $level = new Member_ctrl;
        $db = new Dbobjects;
        $updated_at = date('Y-m-d H:i:s');
        $pmt = getData('payment', $id);
        // $pvctrl = new Pv_ctrl;
        // $pvctrl->db = new Dbobjects;
        // $pvctrl->save_commissions($purchaser_id = $pmt['user_id'], $order_id = $id, $pv = $pmt['pv'], $rv = $pmt['rv'], $pmt['direct_bonus']);
        $upadeted = (new Model('payment'))->update($id, ['status' => 'paid', 'info' => $dataObj->info, 'updated_at' => $updated_at]);
        if ($upadeted) {
            // genaret invoice number
            if ($db->showOne("select id from payment where status='paid' and payment.id='$id'")) {
                $invid = generate_invoice_id($db);
                update_inv_if_not($id = $id, $invid, $db);
            }
            // genaret invoice number end
            $total_amt = $pmt['amount'];
            $total_db = $pmt['direct_bonus'];
            $ordernum = $pmt['unique_id'];
            $level = new Member_ctrl;
            $db = (new Dbobjects);
            $ref = $db->showOne("SELECT * FROM pk_user WHERE pk_user.id = (SELECT ref FROM pk_user WHERE pk_user.id = '{$pmt['user_id']}')");
            if ($ref) {
                $trnArr = null;
                $trnArr['transactedTo'] = $ref['id'];
                $trnArr['transactedBy'] = $pmt['user_id'];
                // $trnArr['purchase_amt'] = round($total_amt, 2);
                $refuser = $ref;
                $refuser = $refuser ? obj($refuser) : null;
                $buyer['id'] = $pmt['user_id'];
                $buyer['ref'] = $ref['id'];
                // $membercnt = $level->count_direct_partners($db, $myid = 1);
                if ($refuser) {
                    $this->send_direct_bonus($db, $buyer, $ordernum, $total_amt, $total_db, $redeempt = $pmt['discount_by_bpt'], $level);
                }
            }
            return true;
        }
        return false;
    }
    public function delet_order_and_cart($id)
    {

        $db = new Dbobjects;
        $conn = $db->connect();
        $conn->beginTransaction();
        try {
            // fetch pending payment data
            $sql = "select id,status,invoice from payment where payment.id = $id and status = 'pending' and (invoice IS NULL OR invoice = '')";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $pmt = $stmt->fetch(PDO::FETCH_OBJ);
            if ($pmt) {
                // Deleet single payment
                $sql_cart_dlt = "delete from customer_order where customer_order.payment_id = $pmt->id";
                $stmt_cart_dlt = $conn->prepare($sql_cart_dlt);
                if ($stmt_cart_dlt->execute()) {
                    $_SESSION['msg'][] = 'Cart product deleted';
                }
                // delete payment after deleting cart
                $sql_pmt_dlt = "delete from payment where payment.id = $id";
                $stmt_pmt_dlt = $conn->prepare($sql_pmt_dlt);
                if ($stmt_pmt_dlt->execute()) {
                    $_SESSION['msg'][] = 'Order deleted';
                }
                $conn->commit();
                return true;
            } else {
                $_SESSION['msg'][] = 'No Order found';
                $conn->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $_SESSION['msg'][] = 'Order not deleted, db error';
            $conn->rollBack();
            return false;
        }
    }
    function init_payment($db, $uid, $pmt)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        $db->get(['unique_id' => $uid]);
        $db->insertData['pmt_id'] = $pmt->id;
        $db->insertData['payment_method'] = 'mollie';
        return $db->update();
    }
    function get_order_by_pmt_id($db, $pmtid)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        return $db->get(['pmt_id' => $pmtid]);
    }
    function get_order_by_unique_id($db, $uid)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        return $db->get(['unique_id' => $uid]);
    }

    function update_payment_data(object $db, string $status, object $pmt)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        $db->get(['pmt_id' => $pmt->id]);
        $db->insertData['pmt_data'] = json_encode($pmt);
        $db->insertData['payment_method'] = 'mollie';
        $db->insertData['status'] = $status;
        return $db->update();
    }
    function send_direct_bonus($db, array $buyer, $ordernum, $total_amt, $total_db, $redeempt, $level)
    {
        ################# Direct bonus #####################
        if ($buyer['id'] != 1) {
            $trnArr['transactedBy'] = $buyer['id'];
            // $trnArr = null;
            $refuser = $db->showOne("SELECT * FROM pk_user WHERE pk_user.id = (SELECT ref FROM pk_user WHERE pk_user.id = '{$trnArr['transactedBy']}')");
            $cmsn = 0;
            $refuser = $refuser ? obj($refuser) : null;
            if ($refuser) {
                // add bonus
                $partial_amt = round(($total_amt - $redeempt), 2);
                $direct_bonus = round((($partial_amt / $total_amt) * $total_db), 2);
                if ($direct_bonus > 0) {
                    $trnArr['transactedTo'] = $buyer['ref'];
                    $trnArr['transactedBy'] = $buyer['id'];
                    $trnArr['purchase_amt'] = round($partial_amt, 2);
                    $cmsn = round($direct_bonus, 2);
                    $trnArr['amount'] =  $cmsn;
                    $trnArr['trnNum'] = $ordernum;
                    $trnArr['status'] = 1; // 1: Active, 2: cancelled  
                    $trnArr['trnGroup'] = 2; // 1:pv commissions, 2: direct bonus
                    $trnArr['trnType'] = 1; // 1: Credit, 2: debit
                    $level->save_trn_data($db, $trnArr);
                }
            }
        }
        if (isset(USER['ref'])) {
            $level->update_level_by_direct_partners_count($db, $myid = USER['ref']);
            $level->update_level_by_purchase($db, $myid = USER['ref']);
        }

        #################### Direct Bonus end #######################
    }

    // function change_total_bonus_on_voucher($vctrl, $vcode, $total_amt, $total_db)
    // {

    //     if ($vcode != "") {
    //         $vchr = $vctrl->get_voucher($code = $vcode, $amt = $total_amt);
    //         $vchrjson = json_encode($vchr);
    //         if ($vchr) {
    //             $vdamt = $vchr->discount;
    //             $bonus_percentagge = round(((($total_db / $total_amt) * 100) - $vchr->value), 2);
    //             if ($bonus_percentagge <= 0) {
    //                 $bonus_percentagge = 0;
    //             }
    //             $total_db = round((($total_amt) * ($bonus_percentagge / 100)), 2);
    //         }
    //     }
    //     return $total_db;
    // }
}
