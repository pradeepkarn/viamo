<?php
$context = obj($context);

$invoice = $context->payment['invoice'];
$invid = $invoice;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping-Label-<?php echo $invid; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 190mm;
            height: 266mm;
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
            <img class="logo" src="<?php echo BASE_URI; ?>/static/assets/img/img7.jpg" alt="Logo">
            <div class="address">
                <!-- Your company address goes here -->
                <?php echo $invoice_address; ?>
            </div>
            <hr>
            <div class="from-to">
                <div class="address-block">
                    <b>Ship to:</b>
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
                    <b>Ship from:</b>
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
                <table class="table">
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
            <div class="contact-info">
                <!-- Your contact information goes here -->
                <?php
                echo $user->mobile . "<br>";
                echo $user->email;
                ?>
            </div>
            <hr>
            <div class="footer">
                <!-- Additional information or footer content goes here -->
            </div>
        </div>
        <div class="container">
            <img class="logo" src="<?php echo BASE_URI; ?>/static/assets/img/img7.jpg" alt="Logo">
            <div class="address">
                <!-- Your company address goes here -->
                <?php echo $invoice_address; ?>
            </div>
            <hr>
            <div class="from-to">
                <div class="address-block">
                    <b>Ship to:</b>
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
                    <b>Ship from:</b>
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
                <table class="table">
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
            <div class="contact-info">
                <!-- Your contact information goes here -->
                <?php
                echo $user->mobile . "<br>";
                echo $user->email;
                ?>
            </div>
            <hr>
            <div class="footer">
                <!-- Additional information or footer content goes here -->
                <?php
                echo $delv_info;
                ?>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php echo $invoice_address; ?>
                        Email: <a href="mailto:support@viamo.world">support@viamo.world</a>
                    </div>
                </div>
                <!-- Footer -->
                <div class="text-center my-5">
                    <p class="text-1"><strong>NOTE :</strong> This is computer generated receipt and does not require physical signature.</p>
                    <div class="btn-group btn-group-sm d-print-none">
                        <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print</a>
                    </div>
                    <button style="margin: 10px;" id="download-btn">Download</button>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.getElementById('download-btn').addEventListener('click', function() {
            var element = document.getElementById('content');

            if (element) {
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
                data.append("label", pdfBlob, 'label-<?php echo $invoice; ?>.pdf');
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/<?php echo home; ?>/upload-pdf', true);
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