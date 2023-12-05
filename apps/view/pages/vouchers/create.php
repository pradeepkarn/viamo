<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">

        <main>
            <style>
                .voucher-code {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 5px;
                    background-color: lightgray;
                    border: 5px dotted #f1f1f1; /* Dotted border */
                    border-radius: 5px;
                    font-size: 14px;
                    font-weight: bold;
                    color: black;
                    cursor: pointer;
                    
                }
            </style>
            <div class="container-fluid px-4">
                <!-- <h1 class="mt-4">Dashboard</h1> -->
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active">Add voucher</li>
                </ol>

                <div class="container">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Create Voucher</h4>
                                    <div id="res"></div>
                                </div>
                                <div class="card-body">
                                    <form id="create-voucher-form" action="">
                                        <div class="mb-3">
                                            <label for="voucherCode" class="form-label">Voucher Code</label>
                                            <input type="text" name="code" class="form-control" id="voucherCode" placeholder="Enter voucher code">
                                        </div>
                                        <div class="mb-3 hide">
                                            <label for="discountAmount" class="form-label">Discount Type</label> <br>
                                            Percentage : <input checked type="radio" name="voucher_group" value="1">
                                            Flate : <input type="radio" name="voucher_group" value="2">
                                        </div>
                                        <div class="mb-3">
                                            <label for="discountAmount" class="form-label">% Value</label>
                                            <input type="number" name="value" class="form-control" id="discountAmount" placeholder="Enter discount amount">
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input checked type="checkbox" name="always_valid" class="form-check-input" id="alwaysValid">
                                            <label class="form-check-label" for="alwaysValid">Always Valid</label>
                                        </div>
                                        <div class="mb-3" id="dateRangeFields">
                                            <label for="expiryDate" class="form-label">Expiry Date</label>
                                            <input type="date" name="valid_upto" class="form-control" id="expiryDate">
                                        </div>
                                        <input type="hidden" name="action" value="create-voucher">
                                        <button id="create-voucher-btn" type="button" class="btn btn-primary">Create Voucher</button>
                                    </form>
                                    <!-- Add this script after your HTML form -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Get the checkbox and date range fields
                                            const alwaysValidCheckbox = document.getElementById('alwaysValid');
                                            const dateRangeFields = document.getElementById('dateRangeFields');
                                            if (alwaysValidCheckbox.checked===true) {
                                                dateRangeFields.style.display = 'none';
                                            }
                                            // Add event listener to checkbox
                                            alwaysValidCheckbox.addEventListener('change', function() {
                                                // Toggle visibility of date range fields based on checkbox state
                                                dateRangeFields.style.display = alwaysValidCheckbox.checked ? 'none' : 'block';
                                            });
                                        });
                                    </script>


                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>My vouchers</h4>
                                    <div id="res"></div>
                                </div>
                                <div class="card-body">

                                    <table id="datatablesSimple1" class="table datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">
                                                    <div class="text-center">Code</div>
                                                </th>
                                                <th scope="col">Value</th>
                                                <th scope="col">Always Valid</th>
                                                <th scope="col">Valid from</th>
                                                <th scope="col">Valid upto</th>
                                                <th scope="col">Edit</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $vl = $context['my_vouchers'];
                                            foreach ($vl as $key => $pv) :
                                                $pv = obj($pv);

                                            ?>
                                                <tr>
                                                    <th scope="row"><?php echo $pv->id; ?></th>
                                                    <td>
                                                        <div class="voucher-code" onclick="copyText(this.id)" id="code<?php echo $pv->id; ?>"><?php echo $pv->code; ?></div>
                                                    </td>
                                                    <td><?php echo $pv->voucher_group == 2 ? "&euro;" : null; ?> <?php echo $pv->value; ?><?php echo $pv->voucher_group == 1 ? "% less on gross value" : " less on gross value"; ?></td>

                                                    <td><?php echo $pv->always_valid ? 'YES' : "NO"; ?></td>
                                                    <td><?php echo $pv->always_valid ? 'NA' : $pv->valid_upto; ?></td>
                                                    <td><?php echo $pv->always_valid ? 'NA' : $pv->valid_upto; ?></td>

                                                    <td>
                                                        <a class="btn-primary btn btn-sm" href="/<?php echo home . "/edit-voucher/?id=" . $pv->id; ?>">Edit</a>
                                                    </td>
                                                </tr>

                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>

            <?php
            pkAjax_form("#create-voucher-btn", "#create-voucher-form", "#res");
            ?>

        </main>
        <?php import("apps/view/inc/footer-credit.php"); ?>
    </div>
</div>
<script>
    function copyText(textId) {
        // Get the text to copy
        var text = document.getElementById(textId).innerText;

        // Create a temporary textarea element to perform the copy
        var tempTextarea = document.createElement("textarea");
        tempTextarea.value = text;
        document.body.appendChild(tempTextarea);

        // Select and copy the text
        tempTextarea.select();
        document.execCommand("copy");

        // Remove the temporary textarea
        document.body.removeChild(tempTextarea);

        // You can provide feedback to the user that the text has been copied
        alert("Text copied to clipboard: " + text);
    }
</script>
<?php
import("apps/view/inc/footer.php");
?>