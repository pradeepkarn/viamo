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
                    <li class="breadcrumb-item active">MyOrders</li>
                </ol>

                <div class="container">
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div id="res"></div>
                            <!-- <table id="datatablesSimple"> -->
                            <div class="table-responsive">
                                <table class="table table-bordered border-primary">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>PV</th>
                                            <!-- <th>Direct Bonus</th> -->
                                            <th>Email</th>
                                            <th>Username</th>
                                            <th>status</th>
                                            <th>In total</th>
                                            <th>Payment method</th>
                                            <th>Country</th>
                                            <th>Info</th>
                                            <th>Invoice No.</th>
                                            <th>Date</th>
                                            <th>Invoice Print</th>
                                            <th>Shipping Label</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        if (authenticate() == true) {
                                            $userObj = new Model('payment');

                                            $arr = null;
                                            $order = $userObj->index($ord = 'DESC', $limit = 9999999, $change_order_by_col = "id");
                                        }


                                        foreach ($order as $value) {
                                            // (new Cart_ctrl)->update_invoice($payment_id=$value['id'],false);
                                            $usrs = getData('pk_user', $value['user_id']);
                                            $email = $usrs['email'];
                                            $username = $usrs['username'];
                                            $pradrs = get_my_primary_address($value['user_id']);
                                            $itm = obj(getCurrency($keyword = $pradrs->country_code));
                                            $currency_name = $itm->name;
                                            $currency_flag = $itm->flag;
                                        ?>
                                            <tr>
                                                <th><?php echo $value['id']; ?></th>
                                                <th><?php echo $value['pv']; ?></th>
                                                <!-- <th><?php // echo $value['direct_bonus']; 
                                                            ?></th> -->
                                                <th><?php echo $email; ?></th>
                                                <th><?php echo $username; ?></th>
                                                <th><?php echo $value['status']; ?></th>
                                                <th><?php echo $value['amount']; ?></th>
                                                <th><?php echo $value['payment_method']; ?></th>
                                                <th>

                                                    <img src="data:image/png;base64,<?php echo $currency_flag; ?>" alt="<?php echo USER['country']; ?>">
                                                    <?php echo $currency_name; ?>
                                                </th>
                                                <th><?php echo $value['info']; ?></th>
                                                <th><?php echo $value['invoice'] ? $value['invoice'] : 'Not created'; ?></th>
                                                <th><?php echo $value['created_at']; ?></th>
                                                <th>
                                                    <?php
                                                    if ($value['status'] == 'paid') { ?>
                                                        <a class="btn btn-warning" target="_blank" href="http:/<?php echo home; ?>/my-orders/?orderid=<?php echo $value['id']; ?>">Invoice</a>
                                                    <?php  } else { ?>
                                                        <?php
                                                        if (is_superuser()) {
                                                        ?>
                                                            <div class="d-flex gap-2">
                                                                <button data-bs-target="#orderstatusmodal<?php echo $value['id']; ?>" data-bs-toggle="modal" class="btn btn-secondary">Pending</button>

                                                                <input type="hidden" class="dlt<?php echo $value['id']; ?>" name="dlt_id" value="<?php echo $value['id']; ?>">
                                                                <button type="button" id="delete-this-orderBtn<?php echo $value['id']; ?>" class="btn btn-danger">Delete</button>
                                                                <?php
                                                                pkAjax("#delete-this-orderBtn{$value['id']}", "/delete-this-order-ajax", ".dlt{$value['id']}", "#res");
                                                                ?>
                                                            </div>
                                                            <!-- Modal -->
                                                            <div class="modal" id="orderstatusmodal<?php echo $value['id']; ?>">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">

                                                                        <!-- Modal header -->
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Mark This order as paid</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>

                                                                        <!-- Modal body -->
                                                                        <div class="modal-body">
                                                                            <textarea name="info" class="pmt<?php echo $value['id']; ?> form-control my-3" placeholder="Payment info"></textarea>
                                                                            <input type="hidden" class="pmt<?php echo $value['id']; ?>" name="pmt_id" value="<?php echo $value['id']; ?>">
                                                                            <button id="mark-this-paid<?php echo $value['id']; ?>" class="btn btn-success">Mark as paid</button>
                                                                            <?php
                                                                            pkAjax("#mark-this-paid{$value['id']}", "/mark-this-order-status-ajax", ".pmt{$value['id']}", "#res");
                                                                            ?>
                                                                        </div>

                                                                        <!-- Modal footer -->
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>


                                                    <?php  } else {
                                                            echo "Pending";
                                                        }
                                                    }
                                                    ?>

                                                </th>
                                                <th>
                                                    <?php
                                                    if ($value['status'] == 'paid' && $value['invoice'] != '') { ?>
                                                        <a class="btn btn-warning" target="_blank" href="http:/<?php echo home; ?>/label-print/?orderid=<?php echo $value['id']; ?>">Print</a>
                                                    <?php } else {
                                                        echo "<i class='fas fa-arrow-left'></i> Create Invoice First";
                                                    }
                                                    ?>
                                                </th>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <?php import("apps/view/inc/footer-credit.php"); ?>
    </div>
</div>
<?php
import("apps/view/inc/footer.php");
?>