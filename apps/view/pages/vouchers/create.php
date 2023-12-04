<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <!-- <h1 class="mt-4">Dashboard</h1> -->
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active">Add voucher</li>
                </ol>

                <div class="container">


                    <div class="row justify-content-center">
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
                                        <div class="mb-3">
                                            <label for="discountAmount" class="form-label">Discount Type</label> <br>
                                            Percentage : <input checked type="radio" name="voucher_group" value="1"> 
                                            Flate : <input type="radio" name="voucher_group" value="2">
                                        </div>
                                        <div class="mb-3">
                                            <label for="discountAmount" class="form-label">Value</label>
                                            <input type="number" name="value" class="form-control" id="discountAmount" placeholder="Enter discount amount">
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="always_valid" class="form-check-input" id="alwaysValid">
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

                                            // Add event listener to checkbox
                                            alwaysValidCheckbox.addEventListener('change', function() {
                                                // Toggle visibility of date range fields based on checkbox state
                                                dateRangeFields.style.display = alwaysValidCheckbox.checked ? 'none' : 'block';
                                            });
                                        });
                                    </script>
                                    </body>

                                    </html>

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

<?php
import("apps/view/inc/footer.php");
?>