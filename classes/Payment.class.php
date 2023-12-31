<?php

class Payment
{
    public $mollie;
    public $db;
    function __construct()
    {
        $this->mollie = new \Mollie\Api\MollieApiClient();

        $this->mollie->setApiKey(MOLLIE_TEST_KEY);
    }
    function create(object $obj)
    {
        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "$obj->amt"
            ],
            "description" => "$obj->description",
            "redirectUrl" => BASE_URI . "/orders",
            "webhookUrl"  => BASE_URI . "/webhook",
            "metadata" => [
                "order_id" => $obj->uid,
            ],
        ]);
        // save payment id in related order
        $this->init_payment($db = $this->db, $uid = $obj->uid, $pmt = $payment);
        header("Location: " . $payment->getCheckoutUrl(), true, 303);
    }
    function webhook()
    {
        try {
            $payment = $this->mollie->payments->get($_POST["id"]);
            $orderId = $payment->metadata->order_id;

            database_write($orderId, $payment->status);

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
                /*
                * The payment is paid and isn't refunded or charged back.
                * At this point you'd probably want to start the process of delivering the product to the customer.
                */
                $this->update_payment_data($this->db, $status = "paid", $pmt = $payment);
               
            } elseif ($payment->isOpen()) {
                /*
         * The payment is open.
         */
                $this->update_payment_data($this->db, $status = "open", $pmt = $payment);
            } elseif ($payment->isPending()) {
                /*
         * The payment is pending.
         */
                $this->update_payment_data($this->db, $status = "pending", $pmt = $payment);
            } elseif ($payment->isFailed()) {
                /*
         * The payment has failed.
         */
                $this->update_payment_data($this->db, $status = "failed", $pmt = $payment);
            } elseif ($payment->isExpired()) {
                /*
         * The payment is expired.
         */
                $this->update_payment_data($this->db, $status = "expired", $pmt = $payment);
            } elseif ($payment->isCanceled()) {
                /*
         * The payment has been canceled.
         */
                $this->update_payment_data($this->db, $status = "cancelled", $pmt = $payment);
            } elseif ($payment->hasRefunds()) {
                /*
         * The payment has been (partially) refunded.
         * The status of the payment is still "paid"
         */
                $this->update_payment_data($this->db, $status = "refunded", $pmt = $payment);
            } elseif ($payment->hasChargebacks()) {
                /*
         * The payment has been (partially) charged back.
         * The status of the payment is still "paid"
         */
                $this->update_payment_data($this->db, $status = "chargeback", $pmt = $payment);
            }
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            echo "API call failed: " . htmlspecialchars($e->getMessage());
            $this->update_payment_data($this->db, $status = "dead", $pmt = $payment);
        }
    }

    // orders
    function init_payment($db, $uid, $pmt)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        $db->get(['unique_id' => $uid]);
        $db->insertData['pmt_id'] = $pmt->id;
        $db->insertData['payment_method'] = 'mollie';
        return $db->update();
    }
    function get_pay_amount($db, $uid)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        $pmt = $db->get(['unique_id' => $uid]);
        if ($pmt) {
            $net_amount =  $pmt['amount'] - $pmt['voucher_amt'] - $pmt['discount_by_bpt'] + $pmt['shipping_cost'];
            return (object) array(
                'obj' => $pmt,
                'amt' => $net_amount
            );
        }
        return 0;
    }
    function update_payment_data(object $db, string $status, object $pmt)
    {
        // $db = new Dbobjects;
        $db->tableName = "payment";
        $pmt = $db->get(['pmt_id' => $pmt->id]);
        $db->insertData['pmt_data'] = json_encode($pmt);
        $db->insertData['payment_method'] = 'mollie';
        $db->insertData['status'] = $status;
        $db->insertData['updated_at'] = date('Y-m-d H:i:s');
        if (strtolower($status) == 'paid') {
            // generate invoice
            $invid = generate_invoice_id($db);
            update_inv_if_not($id = $pmt->id, $invid, $db);
            // generate invoice end
            $ord_ctrl = new Order_ctrl;
            $level = new Member_ctrl;
            $ref = $db->showOne("SELECT * FROM pk_user WHERE pk_user.id = (SELECT ref FROM pk_user WHERE pk_user.id = '{$pmt['user_id']}')");
            $buyer['id'] = $pmt['user_id'];
            $buyer['ref'] = $ref['id'];
            $ord_ctrl->send_direct_bonus($db, $buyer, $ordernum = $pmt['unique_id'], $total_amt = $pmt['amount'], $total_db = $pmt['direct_bonus'], $redeempt = $pmt['discount_by_bpt'], $level);
        }
        return $db->update();
    }
}
