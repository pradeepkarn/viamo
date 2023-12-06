<?php

class Payment
{
    public $mollie;
    function __construct()
    {
        $this->mollie = new \Mollie\Api\MollieApiClient();
        $this->mollie->setApiKey("test_nRGudNxPEEMrWmPaRnTdVqKB6M4BjR");
    }
    function create($unique_id)
    {
        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00"
            ],
            "description" => "My first API payment",
            "redirectUrl" => BASE_URI . "/orders",
            "webhookUrl"  => BASE_URI . "/webhook",
            "metadata" => [
                "order_id" => $unique_id,
            ],
        ]);
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
            } elseif ($payment->isOpen()) {
                /*
         * The payment is open.
         */
            } elseif ($payment->isPending()) {
                /*
         * The payment is pending.
         */
            } elseif ($payment->isFailed()) {
                /*
         * The payment has failed.
         */
            } elseif ($payment->isExpired()) {
                /*
         * The payment is expired.
         */
            } elseif ($payment->isCanceled()) {
                /*
         * The payment has been canceled.
         */
            } elseif ($payment->hasRefunds()) {
                /*
         * The payment has been (partially) refunded.
         * The status of the payment is still "paid"
         */
            } elseif ($payment->hasChargebacks()) {
                /*
         * The payment has been (partially) charged back.
         * The status of the payment is still "paid"
         */
            }
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            echo "API call failed: " . htmlspecialchars($e->getMessage());
        }
    }
}
