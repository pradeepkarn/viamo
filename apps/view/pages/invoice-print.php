<?php
// import("apps/view/inc/header.php");
?>
<?php
$context = obj($context);
// myprint($context);
$user_id = $context->payment['user_id'];
$invoice = $context->payment['invoice'];
$pmtid = $context->payment['id'];
$shadrs = obj(get_shipping_address($user_id));
$user = obj(getData('pk_user', $user_id));

$shpadrs = get_my_primary_address($userid = $user_id);
$invData = get_invoice_address($country_code = $shpadrs->country_code);
$invoice_address = $invData->office;
$delv_info = $invData->delv_info;
$trns = new Dbobjects;
$con = $trns->dbpdo();
$con->beginTransaction();

try {
    $invid = generate_invoice_id($trns);
    update_inv_if_not($id = $pmtid, $invid, $trns);
    $con->commit();
} catch (PDOException $th) {
    $con->rollback();
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INVOICE-<?php echo $invoice != "" ? $invoice : $invid; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 190mm;
            height: 100%;
            margin: 10mm auto;
            border: 1px solid #000;
            padding: 20px;
        }

        .logo {
            height: 90px;
            margin: 0 auto;
            display: block;
        }

        .address {
            text-align: center;
            /* margin-top: 10px; */
        }

        .from-to {
            display: flex !important;
            justify-content: space-between !important;
            margin-top: 10px;
        }

        .from-to .address-block {
            width: 30%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        tr{
            font-size: 10px;
        }
        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .contact-info {
            text-align: center;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
        }

        .footer .del-info {
            font-size: 10px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> -->
</head>
<?php

$user_id = $context->payment['user_id'];

$pmtid = $context->payment['id'];
$shadrs = obj(get_shipping_address($user_id));
$user = obj(getData('pk_user', $user_id));

$shpadrs = get_my_primary_address($userid = $user_id);
$invData = get_invoice_address($country_code = $shpadrs->country_code);
$invoice_address = $invData->office;
$bank = $invData->bank;
$delv_info = $invData->delv_info;
// myprint($shadrs);

// myprint($context->payment);
?>

<body>
    <div id="content">
        <div class="container">
            <img class="logo" src="/<?php echo home; ?>/static/assets/img/img7.jpg" alt="Logo">
            <div class="address">
                <!-- Your company address goes here -->
                <?php echo $invoice_address; ?>
            </div>
            <hr>
            <div class="from-to">
                <div class="address-block">
                    <b>Invoiced to:</b>
                    <p>
                        <!-- Shipping address goes here -->
                        <?php echo $shpadrs->company != "" ? "{$shpadrs->company}<br>" : null; ?>
                        <?php echo trim($shpadrs->first_name) != '' ? $shpadrs->first_name : $user->first_name; ?>
                        <?php echo trim($shpadrs->last_name) != '' ? $shpadrs->last_name : $user->last_name; ?> <br>
                        <?php echo $shpadrs->street . " " . $shpadrs->street_num; ?> <br>
                        <?php echo $shpadrs->zipcode; ?>
                        <?php echo $shpadrs->city; ?> <br>
                        <?php echo $shpadrs->country; ?>
                    </p>
                </div>
                <div class="address-block">
                    <b>Pay to:</b>
                    <!-- Your invoice address goes here -->
                    <?php echo $invoice_address; ?>
                </div>
                <div class="address-block">
                    <b> DETAILS:</b>
                    <p>
                        INV-<?php echo $invid; ?> <br>
                        DATE-<?php echo date('Y-m-d H:i:s'); ?> <br>
                        ORD-<?php echo $pmtid; ?>
                    </p>
                </div>
            </div>
            <div>
                <table class="table mb-0">
                    <thead class="card-header">
                        <tr>
                            <td><strong>Pos</strong></td>
                            <td><strong>Product ID</strong></td>
                            <td><strong>Product Name</strong></td>
                            <td><strong>Unit Price W/O Tax</strong></td>
                            <td><strong>QTY</strong></td>
                            <td><strong>Amt W/O Tax</strong></td>
                            <td><strong>Tax</strong></td>
                            <td class="text-end"><strong>Amount WOT</strong></td>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $total_amt_wot = 0;
                        $total_amt = 0;
                        $total_pv = 0;


                        $j = 1;


                        $cntry = new Model('countries');
                        $delivery_cntry_code = $shpadrs->country_code;
                        $cntr = $cntry->filter_index(['code' => $delivery_cntry_code]);
                        $min_tax = 0;
                        $max_tax = 0;
                        $vdamt = $context->payment['voucher_amt'];
                        $discount = round($context->payment['discount_by_bpt'], 2);
                        foreach ($context->cart as $itm) {
                            $cv = obj($itm);

                            $pkg = obj($cv->package);
                            // print_r($cv->products);
                            foreach ($cv->products as $pr) {
                                $suppliment = $pr->is_suppliment;
                                // $suppliment = select_col('item', $pr->item, 'suppliment');
                                // if (count($cntr) > 0) {
                                //     $cn = obj($cntr[0]);
                                //     $tax = $suppliment ?  $cn->min_tax : $cn->max_tax;
                                // } else {
                                //     $current_cntry = 'CH';
                                //     $tax = 0;
                                // }
                                $tax = $pr->tax_value;
                                // $itemname = select_col('item', $pr->item, 'name');
                                $itemname = $pr->item_name;
                                // $pid = select_col('item', $pr->item, 'product_id');
                                $pid = $pr->product_id;
                                // $amt = round(((($pr->net_price * ($tax / 100)) + $pr->net_price) * $pr->qty), 2) * $pkg->qty;
                                $amt = round(($pr->mrp * $pr->qty), 2) * $pkg->qty;
                                // $amt_wot = round(($pr->net_price * $pr->qty), 2) * $pkg->qty;
                                $price_wot =  round((($pr->mrp / (100 + $tax)) * 100), 2);
                                $amt_wot =  round(($price_wot * $pr->qty * $pkg->qty), 2);
                                $total_amt += $amt;
                                $total_amt_wot += $amt_wot;

                                if ($suppliment) {
                                    $ntprce = ($pr->mrp / (100 + $tax)) * 100;
                                    $max_tax += round(((($ntprce * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                    // $max_tax += round(((($pr->net_price * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                } else {
                                    $ntprcemin = ($pr->mrp / (100 + $tax)) * 100;
                                    // $min_tax += round(((($pr->net_price * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                    $min_tax += round(((($ntprcemin * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                }
                                if ($suppliment) {
                                    $ntprce = ($pr->mrp / (100 + $tax)) * 100;
                                    $tax_value = round(((($ntprce * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                } else {
                                    $ntprce = ($pr->mrp / (100 + $tax)) * 100;
                                    $tax_value = round(((($ntprce * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                }
                                // $qtys[] = $pr->qty;
                        ?>
                                <tr>
                                    <td><?php echo $j; ?></td>
                                    <td><?php echo $pid; ?></td>
                                    <td><?php echo $itemname; ?></td>
                                    <td><?php echo $price_wot; ?></td>
                                    <td><?php echo $pr->qty * $pkg->qty; ?> unit</td>
                                    <td><?php echo $amt_wot; ?></td>
                                    <td><?php echo $tax_value; ?></td>
                                    <td class="text-end"><?php echo $amt_wot; ?></td>
                                </tr>
                            <?php $j++;
                            } ?>

                        <?php
                        }
                        $net_amt = 0;
                        $total_amt_wot = round($total_amt_wot, 2);
                        $total_amt = round($total_amt, 2);
                        $net_amt = round($total_amt - $discount, 2);
                        ?>

                    </tbody>

                    <tr>
                        <td colspan="6"></td>
                        <td colspan="1" class="text-end border-bottom-0"><strong>Total WOT(+):</strong></td>
                        <td colspan="1" class="text-end border-bottom-0"><?php echo $total_amt_wot; ?></td>
                    </tr>



                    <tfoot class="border-dark">
                        <?php
                        if (count($cntr) > 0) {
                            $incntr = obj($cntr[0]);
                        }
                        ?>

                        <tr>
                            <th colspan="6" rowspan="7"></th>
                            <th class="text-end">
                                <?php if ($max_tax > 0) { ?>
                                    Tax (<?php echo $incntr->max_tax; ?>)%(+) =
                                <?php } ?>
                            </th>
                            <th class="text-end">
                                <?php if ($max_tax > 0) { ?>
                                    <?php echo $max_tax; ?>
                                <?php } ?>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end">
                                <?php if ($min_tax > 0) { ?>
                                    Tax (<?php echo $incntr->min_tax; ?>)%(+) =
                                <?php } ?>
                            </th>
                            <th class="text-end">
                                <?php if ($min_tax > 0) { ?>
                                    <?php echo $min_tax; ?>
                                <?php } ?>

                            </th>
                        </tr>


                        <tr>
                            <th class="text-end">Voucher(-) =</th>
                            <th class="text-end"><?php echo $vdamt; ?></th>
                        </tr>
                        <tr>
                            <th class="text-end">Discount(-) =</th>
                            <th class="text-end"><?php echo $discount; ?></th>
                        </tr>
                        <tr>

                            <th class="text-end">Net(+) =</th>
                            <th class="text-end"><?php echo $net = ($total_amt - $vdamt) - $discount; ?></th>
                        </tr>
                        <tr>

                            <th class="text-end">Shipping cost(+) =</th>
                            <th class="text-end"><?php echo $context->payment['shipping_cost']; ?></th>
                        </tr>
                        <tr>

                            <th class="text-end">Cash/Card(-) =</th>
                            <th class="text-end">&#8364; <?php echo $context->payment['shipping_cost'] + $net; ?></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <div class="contact-info">
                <!-- Your contact information goes here -->
                <?php
                echo $user->isd_code . " " . $user->mobile . "<br>";
                echo $user->email;
                ?>
            </div>
            <hr>
            <div class="footer">
                <!-- Additional information or footer content goes here -->

                <div class="row">
                    <div class="col-md-12 text-center">
                        Email: <a href="mailto:support@viamo.world">support@viamo.world</a>
                    </div>
                </div>
                <div class="del-info">
                    <?php
                    echo $delv_info;
                    ?>

                </div>
                <!-- Footer -->
                <div class="text-center my-5">
                    <p class="text-1"><strong>NOTE :</strong> This is computer generated receipt and does not require physical signature.</p>
                    <div class="btn-group btn-group-sm d-print-none">
                        <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print</a>
                    </div>
                    <button style="margin: 10px;" id="download-btn">Download PDF</button>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('content');

            if (el) {
                var opt = {
                    margin: 1,
                    filename: 'label-<?php echo $invoice; ?>.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                }
            }
            sendFileToserver(el, opt);
        });

        document.getElementById('download-btn').addEventListener('click', function() {
            var element = document.getElementById('content');

            if (element) {
                var opt = {
                    margin: 1,
                    filename: 'invoice-<?php echo $invoice; ?>.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };

                html2pdf().set(opt).from(element).save();

            } else {
                console.error("Content element not found!");
            }
        });

        function sendFileToserver(element, opt) {
            html2pdf().from(element).set(opt).toPdf().output('datauristring').then(function(pdfAsString) {
                var arr = pdfAsString.split(',');
                pdfAsString = arr[1];
                // Convert data URI to blob
                var byteCharacters = atob(pdfAsString);
                var byteNumbers = new Array(byteCharacters.length);
                for (var i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                var byteArray = new Uint8Array(byteNumbers);
                var pdfBlob = new Blob([byteArray], {
                    type: 'application/pdf'
                });

                // Create FormData and append the blob
                var data = new FormData();
                data.append("invoice", pdfBlob, 'invoice-<?php echo $invoice; ?>.pdf');
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/<?php echo home; ?>/upload-invoice-pdf', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        console.log('PDF successfully uploaded to the server.');
                    }
                };
                xhr.send(data);
            })
        }
    </script>
</body>

</html>