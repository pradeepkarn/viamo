<?php

use PHPMailer\PHPMailer\PHPMailer;

class Order_ctrl
{
    public function place()
    {
        $req = (object) ($_POST);
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
            $shipping_cost = calculate_shipping_cost(db: $dbobj, gram: $total_gm, ccode: USER['country_code']);
            if (!isset($req->total_gm) || !isset($req->shipping_cost)) {
                $_SESSION['msg'][] = 'Shiping not defined';
                $con->rollback();
                return false;
            }
            if (!($total_gm == $req->total_gm && $shipping_cost == $req->shipping_cost)) {
                $_SESSION['msg'][] = 'Shiping cost mismatched, try again';
                $con->rollback();
                return false;
            }


            try {
                $total_amt = $dbobj->show($sql)[0]['total_amt'];
                $total_pv = $dbobj->show($sql)[0]['total_pv'];
                $total_rv = $dbobj->show($sql)[0]['total_rv'];
                $total_db = $dbobj->show($sql)[0]['total_db'];

                $arr['amount'] = $total_amt;
                $arr['shipping_cost'] = $shipping_cost;
                $arr['total_gm'] = $total_gm;
                $arr['pv'] = $total_pv;
                $arr['rv'] = $total_rv;
                $arr['direct_bonus'] = $total_db;
                $arr['point_used'] = 0;
                $arr['discount_by_point'] = 0;
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
                    } else if ($total_amt >= $point && $point > 0) {
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
                $addrs->address_name == '' ||
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
            $arr['state'] = isset($addrs->state)?$addrs->state:null;
            $arr['country'] = $addrs->country;
            $arr['zipcode'] = $addrs->zipcode;
            $arr['isd_code'] = $addrs->isd_code;
            $arr['payment_method'] = sanitize_remove_tags($_POST['payment_mode']);
            // $arr['pv'] = 0;

            $arr['unique_id'] = uniqid();

            $arr['status'] = $_POST['payment_mode'] == 'Bank_transfer' ? "pending" : 'paid';
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
                // return;
                if (intval($pay) && $arr['status'] == 'paid') {
                    $invid = generate_invoice_id($dbobj);
                    update_inv_if_not($pay, $invid, $dbobj);
                    // $pvctrl = new Pv_ctrl;
                    // $pvctrl->db = $dbobj;
                    // $pvctrl->save_commissions($purchaser_id = $_SESSION['user_id'], $order_id = $pay, $pv = $total_pv, $rv = $total_rv, $total_db);
                    ################# Direct bonus #####################
                    $level = new Member_ctrl;
                    $db = $dbobj;
                    $trnArr['transactedTo'] = USER['id'];
                    $trnArr['transactedBy'] = USER['id'];
                    if ($commission == false) {
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
                    } else {
                        if (USER['id'] != 1) {
                            $trnArr = null;
                            $refuser = $db->showOne("SELECT * FROM pk_user WHERE pk_user.id = (SELECT ref FROM pk_user WHERE pk_user.id = '{$trnArr['transactedBy']}')");
                            $cmsn = 0;
                            $refuser = $refuser ? obj($refuser) : null;
                            // $membercnt = $level->count_direct_partners($db, $myid = 1);
                            if ($refuser) {
                                $trnArr['transactedTo'] = USER['ref'];
                                $trnArr['transactedBy'] = USER['id'];
                                $trnArr['purchase_amt'] = round($total_amt, 2);
                                $cmsn = round($total_db, 2);
                                $trnArr['amount'] =  $cmsn;
                                $trnArr['trnNum'] = $ordernum;
                                $trnArr['status'] = 1; // 1: Active, 2: cancelled  
                                $trnArr['trnGroup'] = 2; // 1:pv commissions, 2: direct bonus
                                $trnArr['trnType'] = 1; // 1: Credit, 2: debit
                                $level->save_trn_data($db, $trnArr);
                            }
                        }
                    }
                    $arr = null;
                    $level->update_level_by_direct_partners_count($db, $myid = USER['ref']);
                    $level->update_level_by_purchase($db, $myid = USER['ref']);
                }
                #################### Direct Bonus end #######################
                $con->commit();
            } catch (PDOException $th) {
                $_SESSION['msg'][] = $th;
                echo $th;
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
                $_SESSION['msg'][] = 'Order placed';
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
                    'bank_account' => $bank
                ]));

                return true;
            } else {
                $_SESSION['msg'][] = 'Order not placed';
                return false;
            }
        }
    }
    public function confirm_order_status($id, $dataObj)
    {
        $level = new Member_ctrl;
        $updated_at = date('Y-m-d H:i:s');
        $pmt = getData('payment', $id);
        // $pvctrl = new Pv_ctrl;
        // $pvctrl->db = new Dbobjects;
        // $pvctrl->save_commissions($purchaser_id = $pmt['user_id'], $order_id = $id, $pv = $pmt['pv'], $rv = $pmt['rv'], $pmt['direct_bonus']);
        $upadeted = (new Model('payment'))->update($id, ['status' => 'paid', 'info' => $dataObj->info, 'updated_at' => $updated_at]);
        if ($upadeted) {
            $total_amt = $pmt['amount'];
            $total_db = $pmt['direct_bonus'];
            $ordernum = $pmt['unique_id'];
            $level = new Member_ctrl;
            $db = (new Dbobjects);
            $ref = $db->showOne("SELECT * FROM pk_user WHERE pk_user.id = (SELECT ref FROM pk_user WHERE pk_user.id = '{$pmt['user_id']}')");
            $trnArr = null;
            $trnArr['transactedTo'] = $ref['id'];
            $trnArr['transactedBy'] = $pmt['user_id'];
            $trnArr['purchase_amt'] = round($total_amt, 2);
            $refuser = $ref;
            $refuser = $refuser ? obj($refuser) : null;
            $cmsn = 0;
            // $membercnt = $level->count_direct_partners($db, $myid = 1);
            if ($refuser) {
                if ($pmt['point_used'] == 0) {
                    $cmsn = round($total_db, 2);
                    $trnArr['amount'] =  $cmsn;
                    $trnArr['trnNum'] = $ordernum;
                    $trnArr['status'] = 1; // 1: Active, 2: cancelled  
                    $trnArr['trnGroup'] = 2; // 1:pv commissions, 2: direct bonus
                    $trnArr['trnType'] = 1; // 1: Credit, 2: debit
                    $level->save_trn_data($db, $trnArr);
                }
            }
            $level->update_level_by_direct_partners_count($db, $myid = $ref['ref']);
            $level->update_level_by_purchase($db, $myid = $ref['ref']);
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
}
