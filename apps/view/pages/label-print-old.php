<?php
$context = obj($context);
// myprint($context);
$invoice = $context->payment['invoice'];
$invid = $invoice;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping-Label-<?php echo $invid; ?></title>
    <link href="<?php echo BASE_URI; ?>/static/bs532/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
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
    <div>
        <div class="container" style="height: 297mm;">
            <div class="row mt-3">
                <div class="col-md-4 mx-auto text-center">
                    <img style="width: 150px;" id="logo" src="<?php echo BASE_URI; ?>/static/assets/img/img7.jpg" title="Koice" alt="Koice" />
                </div>
            </div>
            <div class="row my-2">
                <div class="col-md-12 text-center">
                    <?php echo $invoice_address; ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <b>Ship to:</b> <br>
                    <?php echo $shpadrs->company!=""?"{$shpadrs->company}<br>":null; ?> 
                    <?php echo trim($shpadrs->first_name)!=''?$shpadrs->first_name:$user->first_name; ?>
                    <?php echo trim($shpadrs->last_name)!=''?$shpadrs->last_name:$user->last_name; ?> <br>
                    <?php echo $shpadrs->street . " " . $shpadrs->street_num; ?> <br>
                    <?php echo $shpadrs->zipcode; ?> 
                    <?php echo $shpadrs->city; ?> <br>
                    <?php echo $shpadrs->country; ?>

                </div>
                <div class="col-3 text-end">
                    <b>Ship from:</b>
                    <?php echo $invoice_address; ?>

                </div>
                <div class="col-3 text-end">
                    <p>
                        INV-<?php echo $invid; ?> <br>
                        DATE-<?php echo date('Y-m-d H:i:s'); ?> <br>
                        ORD-<?php echo $pmtid; ?>
                    </p>

                </div>

            </div>
            <div class="row mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>Product ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_amt = 0;
                        $total_pv = 0;
                        $i = 1;


                        foreach ($context->cart as $itm) {
                            $cv = obj($itm);
                            $pkg = obj($cv->package);
                            $j = 1;
                            foreach ($cv->products as $pr) {
                                $itemname = select_col('item', $pr->item, 'name');
                                $pid = select_col('item', $pr->item, 'product_id');
                        ?>
                                <tr>
                                    <td><?php echo $j; ?></td>
                                    <td><?php echo $pid; ?></td>
                                    <td><?php echo $itemname; ?></td>
                                    <td><?php echo $pr->qty * $pkg->qty; ?> unit</td>
                                </tr>
                            <?php $j++;
                            } ?>

                        <?php $i++;
                        } ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <?php
                        echo $user->mobile . "<br>";
                        echo $user->email;
                        ?>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <?php echo $invoice_address; ?>
                </div>
            </div>
        </div>

        <div class="container" style="height: 297mm;">
            <div class="row">
                <div class="col-md-4 mx-auto text-center">
                    <img style="width: 150px;" id="logo" src="<?php echo BASE_URI; ?>/static/assets/img/img7.jpg" title="Koice" alt="Koice" />
                </div>
            </div>
            <div class="row my-2">
                <div class="col-md-12 text-center">
                    <?php echo $invoice_address; ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <b>Ship to : </b> <br>
                    <?php echo $shpadrs->company!=""?"{$shpadrs->company}<br>":null; ?> 
                    <?php echo trim($shpadrs->first_name)!=''?$shpadrs->first_name:$user->first_name; ?>
                    <?php echo trim($shpadrs->last_name)!=''?$shpadrs->last_name:$user->last_name; ?> <br>
                    <?php echo $shpadrs->street . " " . $shpadrs->street_num; ?> <br>
                    <?php echo $shpadrs->zipcode; ?> 
                    <?php echo $shpadrs->city; ?> <br>
                    <?php echo $shpadrs->country; ?>
                </div>
                <div class="col-3 text-end">
                    <b>Ship from:</b>
                    <?php echo $invoice_address; ?>

                </div>

                <div class="col-3 text-end">
                    <p>
                        INV-<?php echo $invid; ?> <br>
                        DATE-<?php echo date('Y-m-d H:i:s'); ?> <br>
                        ORD-<?php echo $pmtid; ?>
                    </p>

                </div>


            </div>
            <div class="row mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>Product ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_amt = 0;
                        $total_pv = 0;
                        $i = 1;


                        foreach ($context->cart as $itm) {
                            $cv = obj($itm);
                            $pkg = obj($cv->package);
                            $j = 1;
                            foreach ($cv->products as $pr) {
                                $itemname = select_col('item', $pr->item, 'name');
                                $pid = select_col('item', $pr->item, 'product_id');
                        ?>
                                <tr>
                                    <td><?php echo $j; ?></td>
                                    <td><?php echo $pid; ?></td>
                                    <td><?php echo $itemname; ?></td>
                                    <td><?php echo $pr->qty * $pkg->qty; ?> unit</td>
                                </tr>
                            <?php $j++;
                            } ?>

                        <?php $i++;
                        } ?>


                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <?php
                        echo $user->mobile . "<br>";
                        echo $user->email;
                        ?>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <?php
                    echo $delv_info;
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <?php echo $invoice_address; ?>
                    Email: <a href="mailto:<?php echo email; ?>"><?php echo email; ?></a>
                </div>
            </div>
            <!-- Footer -->
            <div class="text-center my-5">
                <p class="text-1"><strong>NOTE :</strong> This is computer generated receipt and does not require physical signature.</p>
                <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print</a> <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-download"></i> Download</a> </div>
            </div>
        </div>
    </div>

    
</body>

</html>