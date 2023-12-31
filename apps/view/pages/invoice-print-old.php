<?php
import("apps/view/inc/header.php");
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

<script>
    document.getElementsByTagName('title')[0].innerText = "INVOICE-<?php echo $invoice != "" ? $invoice : $invid; ?>";
</script>
<style>
    .p-2{
        font-size: 10px;
    }
    .electronic-info{
        font-size: 9px;
    }
</style>
<div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container my-5">



                <!-- Container -->
                <div class="container-fluid invoice-container">
                    <!-- Header -->
                    <header>
                        <div class="row">
                            <div class="col-7 text-center text-sm-start mb-3 mb-sm-0">
                                <img id="logo" src="/<?php echo STATIC_URL; ?>/assets/img/img7.jpg" width="100px" title="Koice" alt="Koice" />
                            </div>
                            <div class="col-5 text-center text-sm-end">
                                <h4 class="text-7 mb-0">Invoice</h4>
                            </div>
                        </div>
                        <hr>
                    </header>

                    <!-- Main Content -->
                    <main>
                        <div class="row">
                            <div class="col-6"><strong>Date:</strong> <?php echo date('Y-m-d', strtotime($context->payment['updated_at'])); ?> <strong>Time:</strong> <?php echo date('H:i:s', strtotime($context->payment['updated_at'])); ?></div>
                            <div class="col-6 text-end"> <strong>Invoice No:</strong> INV-<?php echo $invoice != "" ? $invoice : $invid; ?></div>

                        </div>
                        <hr>
                        <div class="row justify-content-between">

                            <div class="col-4"> <strong>Invoiced To:</strong>
                                <address>
                                    <?php echo $shpadrs->company!=''?"{$shpadrs->company}<br>":null; ?> 
                                    <?php echo $shpadrs->name; ?>  <br>
                                    <?php echo $shpadrs->street." ".$shpadrs->street_num; ?> <br>
                                    <?php echo $shpadrs->zipcode; ?> <?php echo $shpadrs->city; ?> <br>
                                    <?php echo $shpadrs->country; ?> <br>
                                </address>
                            </div>
                            <div class="col-4"> <strong>Pay To:</strong>
                                <address>
                                    <?php echo $invoice_address; ?>
                                </address>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
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
                                            $discount = round($context->payment['discount_by_bpt'],2);
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
                                                    $price_wot =  round((($pr->mrp / (100 + $tax)) * 100),2);
                                                    $amt_wot =  round(($price_wot * $pr->qty* $pkg->qty),2);
                                                    $total_amt += $amt;
                                                    $total_amt_wot += $amt_wot;

                                                    if ($suppliment) {
                                                        $ntprce = ($pr->mrp / (100 + $tax))*100;
                                                        $max_tax += round(((($ntprce * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                                        // $max_tax += round(((($pr->net_price * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                                    } else {
                                                        $ntprcemin = ($pr->mrp / (100 + $tax))*100;
                                                        // $min_tax += round(((($pr->net_price * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                                        $min_tax += round(((($ntprcemin * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                                    }
                                                    if ($suppliment) {
                                                        $ntprce = ($pr->mrp / (100 + $tax))*100;
                                                        $tax_value = round(((($ntprce * ($tax / 100))) * $pr->qty), 2) * $pkg->qty;
                                                    } else {
                                                        $ntprce = ($pr->mrp / (100 + $tax))*100;
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
                                            $total_amt_wot = round($total_amt_wot,2);
                                            $total_amt = round($total_amt,2);
                                            $net_amt = round($total_amt-$discount,2);
                                            ?>

                                        </tbody>
                                    
                                            <tr>
                                                <td colspan="7" class="text-end border-bottom-0"><strong>Total WOT(+):</strong></td>
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
                                                <th class="text-end"><?php echo $net = ($total_amt-$vdamt)-$discount; ?></th>
                                            </tr>
                                            <tr>

                                                <th class="text-end">Shipping cost(+) =</th>
                                                <th class="text-end"><?php echo $context->payment['shipping_cost']; ?></th>
                                            </tr>
                                            <tr>

                                                <th class="text-end">Cash/Card(-) =</th>
                                                <th class="text-end">&#8364; <?php echo $context->payment['shipping_cost']+$net; ?></th>
                                            </tr>
                                        </tfoot>

                                    </table>
                                    <div class="p-2">
                                        <?php
                                        echo $delv_info;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                    <!-- Footer -->
                    <footer class="text-center mt-4">
                        <p class="text-1 electronic-info"><strong>NOTE :</strong> This is computer generated receipt and does not require physical signature.</p>
                        <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none mb-5"><i class="fa fa-print"></i> Print</a> <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none mb-5"><i class="fa fa-download"></i> Download</a> </div>
                    </footer>
                </div>



            </div>

        </main>

    </div>
</div>
<?php

// use Dompdf\Dompdf;

// // URL of the HTML content you want to convert to PDF
// $htmlLink = "http://localhost/".home."/my-orders/?orderid=$pmtid";

// // Fetch the HTML content from the URL
// $html = file_get_contents($htmlLink);

// // Create a new Dompdf instance
// $dompdf = new Dompdf();

// // Load the HTML content
// $dompdf->loadHtml($html);

// // (Optional) Set paper size and orientation (default is A4 portrait)
// $dompdf->setPaper('A4', 'portrait');

// // Render the HTML as PDF
// $dompdf->render();

// // Output the PDF to the browser for download
// $dompdf->stream("INV-".$pmtid.".pdf", ['Attachment' => false]);

import("apps/view/inc/footer.php");
?>