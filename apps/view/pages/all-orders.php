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
                            <div class="row my-2">
                                <div class="col-md-4">
                                    <form method="get" action="/<?php echo home; ?>/all-orders/">
                                        <div class="d-flex">
                                            <input value="<?php echo isset($_GET['q']) ? $_GET['q'] : null; ?>" type="search" name="q" placeholder="Search from server" class="form-control">
                                            <button type="submit">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">

                                <table class="table table-bordered border-primary">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>PV</th>
                                            <th>Details</th>
                                            <th>status</th>
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
                                            $order = $userObj->index($ord = 'DESC', $limit = 5, $change_order_by_col = "created_at");
                                        }
                                        if (isset($_GET['q'])) {
                                            $keyword = sanitize_remove_tags(trim($_GET['q']));
                                            $db = new Dbobjects;
                                            $sql = "SELECT payment.* FROM payment
                                            JOIN pk_user ON payment.user_id = pk_user.id
                                            WHERE 
                                                payment.id LIKE '%$keyword%'
                                                OR payment.invoice LIKE '%$keyword%'
                                                OR payment.unique_id LIKE '%$keyword%'
                                                OR payment.user_id LIKE '%$keyword%'
                                                OR payment.name LIKE '%$keyword%'
                                                OR pk_user.username LIKE '%$keyword%'
                                                OR pk_user.email LIKE '%$keyword%'
                                                ORDER BY id DESC LIMIT 20;
                                            ";
                                            $order = $db->show($sql);
                                        }
                                        if (isset($_GET['page'])) {
                                            $ctx = getOrders($req = $_GET, $data_limit = 5);
                                            $order = $ctx->rows;
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
                                                <th>
                                                    <?php echo $username; ?>
                                                    <hr>
                                                    <?php echo $email; ?>

                                                    <table class="cost-details table table-bordered">

                                                        <tr class="text-end">
                                                            <td>Amount(+) </td>
                                                            <td><?php echo number_format($value['amount'], 2, '.', ''); ?></td>
                                                        </tr>
                                                        <tr class="text-end">
                                                            <td>V. Disc.(-) </td>
                                                            <td> <?php echo number_format($value['voucher_amt'], 2, '.', ''); ?></td>
                                                        </tr>
                                                        <tr class="text-end">
                                                            <td>Discount(-) </td>
                                                            <td> <?php echo number_format($value['discount_by_bpt'], 2, '.', ''); ?></td>
                                                        </tr>
                                                        <tr class="text-end">
                                                            <td>Net(+) </td>
                                                            <td><?php echo $net = number_format(($value['amount'] - $value['voucher_amt']) - ($value['discount_by_bpt']), 2, '.', ''); ?></td>
                                                        </tr>
                                                        <tr class="text-end">
                                                            <td>Shipping(+) </td>
                                                            <td><?php echo number_format($value['shipping_cost'], 2, '.', ''); ?></td>
                                                        </tr>
                                                        <tr class="text-end">
                                                            <td>Card(-) </td>
                                                            <td><?php echo number_format(round(($net + $value['shipping_cost']), 2), 2, '.', ''); ?></td>
                                                        </tr>
                                                    </table>
                                                </th>

                                                <th><?php echo $value['status']; ?></th>
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
                                                                        <div id="paid-status-res"></div>
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
                                                                            pkAjax("#mark-this-paid{$value['id']}", "/mark-this-order-status-ajax", ".pmt{$value['id']}", "#paid-status-res");
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
                                                    $label_exist = file_exists(RPATH . "/media/docs/labels/label-{$value['invoice']}.pdf");
                                                    if ($label_exist) : ?>
                                                        <a class="my-1" target="_blank" href="/<?php echo home; ?>/cronjobs/mail-single-shipping-label/?orderid=<?php echo $value['id']; ?>">Force send mail</a>
                                                        <a class="btn btn-warning my-1" target="_blank" href="http:/<?php echo MEDIA_URL; ?>/docs/labels/label-<?php echo $value['invoice']; ?>.pdf">PDF</a>
                                                    <?php endif; ?>
                                                    <?php if ($value['status'] == 'paid' && $value['invoice'] != '') : ?>
                                                        <a class="btn btn-primary my-1" target="_blank" href="http:/<?php echo home; ?>/label-print/?orderid=<?php echo $value['id']; ?>">Web</a>
                                                    <?php else :
                                                        echo "<i class='fas fa-arrow-left'></i> Create Invoice First";
                                                    ?>
                                                    <?php endif; ?>
                                                </th>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                            <div class="custom-pagination my-3">
                                <?php
                                $cp = isset($ctx->current_page) ? $ctx->current_page : 0;
                                $tp = isset($ctx->rows_count) ? $ctx->rows_count : 5;
                                $pg = isset($_GET['page']) ? $_GET['page'] : 1;
                                $tp = $tp; // Total pages
                                $current_page = $cp; // Assuming first page is the current page
                                $link = "/all-orders/"; // Set your link here

                                // Calculate start and end page numbers to display
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($start_page + 4, $tp);

                                // Show first page button if not on the first page
                                if ($current_page > 1) {
                                    echo '<a class="first-button" href="/' . home . $link . '?page=1">&laquo;</a>';
                                }

                                // Show ellipsis if there are more pages before the start page
                                if ($start_page > 1) {
                                    echo '<span>...</span>';
                                }

                                // Display page links within the range
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    $active_class = ($pg == $i) ? "btn btn-primary" : null;
                                    echo '<a class="' . $active_class . '" href="/' . home . $link . '?page=' . $i . '"><span style="position:relative; top:-5px;">' . $i . '</span></a>';
                                }

                                // Show ellipsis if there are more pages after the end page
                                if ($end_page < $tp) {
                                    echo '<span>...</span>';
                                }

                                // Show last page button if not on the last page
                                if ($current_page < $tp) {
                                    echo '<a class="last-button" href="/' . home . $link . '?page=' . $tp . '">&raquo;</a>';
                                }
                                ?>
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