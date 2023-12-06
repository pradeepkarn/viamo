<?php

class Payment
{
    public $mollie;
    public $ordctrl;
    public $db;
    function __construct()
    {
        $this->mollie = new \Mollie\Api\MollieApiClient();
        $this->mollie->setApiKey("test_nRGudNxPEEMrWmPaRnTdVqKB6M4BjR");
        $this->ordctrl = new Order_ctrl;
        $this->db = new Dbobjects;
    }
    function create($unique_id)
    {
        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "54.00"
            ],
            "description" => "My first API payment",
            "redirectUrl" => BASE_URI . "/orders",
            "webhookUrl"  => BASE_URI . "/webhook",
            "metadata" => [
                "order_id" => $unique_id,
            ],
        ]);
        // save payment id in related order
        $this->ordctrl->init_payment($db = $this->db, $uid = $unique_id, $pmt = $payment);
        header("Location: " . $payment->getCheckoutUrl(), true, 303);
    }
    function webhook()
    {
        try {

            $payment = $this->mollie->payments->get($_POST["id"]);
            $order = $this->ordctrl->get_order_by_pmt_id($this->db, $payment->id);
            $orderId = $payment->metadata->order_id;

            database_write($orderId, $payment->status);

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
                /*
                * The payment is paid and isn't refunded or charged back.
                * At this point you'd probably want to start the process of delivering the product to the customer.
                */
                $this->ordctrl->update_payment_data($this->db, $status = "paid", $pmt = $payment);
            } elseif ($payment->isOpen()) {
                /*
         * The payment is open.
         */
                $this->ordctrl->update_payment_data($this->db, $status = "open", $pmt = $payment);
            } elseif ($payment->isPending()) {
                /*
         * The payment is pending.
         */
                $this->ordctrl->update_payment_data($this->db, $status = "pending", $pmt = $payment);
            } elseif ($payment->isFailed()) {
                /*
         * The payment has failed.
         */
                $this->ordctrl->update_payment_data($this->db, $status = "failed", $pmt = $payment);
            } elseif ($payment->isExpired()) {
                /*
         * The payment is expired.
         */
                $this->ordctrl->update_payment_data($this->db, $status = "expired", $pmt = $payment);
            } elseif ($payment->isCanceled()) {
                /*
         * The payment has been canceled.
         */
                $this->ordctrl->update_payment_data($this->db, $status = "cancelled", $pmt = $payment);
            } elseif ($payment->hasRefunds()) {
                /*
         * The payment has been (partially) refunded.
         * The status of the payment is still "paid"
         */
                $this->ordctrl->update_payment_data($this->db, $status = "refunded", $pmt = $payment);
            } elseif ($payment->hasChargebacks()) {
                /*
         * The payment has been (partially) charged back.
         * The status of the payment is still "paid"
         */
                $this->ordctrl->update_payment_data($this->db, $status = "chargeback", $pmt = $payment);
            }
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            echo "API call failed: " . htmlspecialchars($e->getMessage());
            $this->ordctrl->update_payment_data($this->db, $status = "dead", $pmt = $payment);
        }
    }
}
