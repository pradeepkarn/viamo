<?php

use League\Csv\Writer;

import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
$level = new Member_ctrl;
$db = new Dbobjects;
// $bonus_list = $level->list_direct_bonus($db,$myid=USER['id']);
$all_cmsn = $level->lifetime_commission($db, $myid = USER['id']);
$bonus_paid = $level->debited_amount($db, $myid = USER['id']);
$net_cmsn = $level->net_balance_minus_requested_balance($db, $myid = USER['id']);

// $totalWithDrawal = $level->get_all_withdrawal_amt_sum($db,$myid=USER['id']);
// $free_to_paid = $allcmsn-$totalWithDrawal;
// myprint($context);
// $db = new Model('pk_user');
// $total_user = $db->index(ord: "DESC", limit: 10000);
// $tp = count($total_user);
$cp = isset($context['data']->current_page) ? $context['data']->current_page : 0;
$tp = isset($context['data']->total_cmsn) ? $context['data']->total_cmsn : 1;
// myprint($context);
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <!-- <h1 class="mt-4">Dashboard</h1> -->
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active">My Commissions</li>
                </ol>
                <div class="row justify-content-center mb-5">
                    <div class="col-3">
                        <div class="fnbox text-center">
                            <h3>Lifetime Points</h3>
                            <h4><?php echo $all_cmsn; ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="fnbox text-center">
                            <h3>Free to Request</h3>
                            <h4><?php echo $net_cmsn; ?></h4>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Withdraw</button>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="fnbox text-center">
                            <h3>Point redeemed</h3>
                            <h4><?php echo $bonus_paid; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <form method="get" action="/<?php echo home; ?>/my-commissions/">
                            <div class="d-flex">
                                <input value="<?php echo isset($_GET['q']) ? $_GET['q'] : null; ?>" type="search" name="q" placeholder="Search from server" class="form-control">
                                <button type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="custom-pagination my-3">
                    <?php
                    $pg = isset($_GET['page']) ? $_GET['page'] : 1;
                    $tp = $tp; // Total pages
                    $current_page = $cp; // Assuming first page is the current page
                    $link = "/my-commissions/"; // Set your link here

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
                <div class="container">


                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Transacted By</th>
                                        <!-- <th>Member Level</th> -->
                                        <th>Transacted TO</th>
                                        <!-- <th>Ring</th> -->

                                        <th>PV</th>
                                        <th>Point</th>
                                        

                                      
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php


                                    $csv_main_data = [];
                                    if (authenticate() == true) {
                                        $level = new Member_ctrl;
                                        $cmsns = $level->bonus_list($db, $myid = USER['id']);
                                    }
                                    $cmsns = isset($context['data']->commissions) ? $context['data']->commissions : $cmsns;
                                    foreach ($cmsns as $value) {
                                        $satus_text = getTextFromCode($code = $value['status'], $arr = TRN_STATUS);
                                        $sponser = sponser_username($value['transacted_to']);
                                        $orderbyusername = sponser_username($value['transacted_by']);

                                        $csvdata['order by'] =  $orderbyusername;
                                        // $csvdata['pv in order'] =  $value['pv'];
                                        // $csvdata['rv in order'] =  $value['rv'];
                                        $csvdata['paid to'] =  $sponser;
                                        // $csvdata['ring'] =  $value['ring'];
                                        $csvdata['commission paid'] =  $value['amount'];
                                        // $csvdata['direct bonus paid'] =  $value['direct_bonus'];
                                        // $csvdata['rv paid'] =  $value['rank_advance'];
                                        $csvdata['date'] =  $value['created_at'];
                                       
                                    ?>
                                        <tr>
                                            <th><?php echo $value['id']; ?></th>

                                            <!-- <th><?php //echo $orderbyusername; 
                                                        ?></th> -->

                                            <th><?php echo $sponser; ?></th>
                                            <!-- <th><?php //echo getTextFromCode($value['amount'],MEMBER_LEVEL); ?></th> -->
                                            <th><?php echo $orderbyusername ; ?></th>


                                            <th><?php echo $value['purchase_amt']; ?></th>
                                            <th><?php echo $value['amount']; ?></th>
                                            


                                            <th><?php echo $value['created_at']; ?></th>
                                           

                                        </tr>
                                    <?php
                                        $csv_main_data[] = $csvdata;
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- <a href="/<?php echo home; ?>/csvdata/commissions/commission-paid.csv" download>Download CSV</a> -->
                            <?php


                            if (count($csv_main_data) > 0) {
                                $filePath = 'csvdata/commissions/commission-paid.csv';
                                // Create a new CSV writer instance
                                $csv = Writer::createFromPath($filePath, 'w');
                                $headers = array_keys($csv_main_data[0]);
                                // Insert headers as the first row in the CSV file
                                $csv->insertOne($headers);
                                // Insert the data along with headers into the CSV file
                                $csv->insertAll($csv_main_data);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                window.addEventListener('load', function() {
                    var inputElement = document.getElementsByClassName("datatable-input")[0];
                    if (inputElement) {
                        inputElement.placeholder = "Search on below table";
                    }
                });
            </script>
        </main>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Withdrawl Amount</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <?php
                    $my_username = null;

                    if (isset($_SESSION['user_id'])) {
                        $userid = $_SESSION['user_id'];
                        $invite = getData("pk_user", $userid);
                        $my_username = $invite != false ? $invite['username'] : null;
                    }
                    ?>
                    <div class="modal-body">
                        <div id="res"></div>
                        <form id="withdraw-amt" action="/<?php echo home; ?>/req-redeem">
                            <label for="">Select bank</label>
                            <select class="my-2 form-select" name="bank">
                                <?php
                                $jsn = USER['jsn'];
                                $obj = $jsn ? json_decode($jsn) : json_decode('[]');
                                $bank_exists = false;
                                if (isset($obj->banks)) {
                                    foreach ($obj->banks as $key => $bnk) {
                                        $bank_exists = true;
                                ?>
                                        <option value='<?php echo json_encode($bnk); ?>'><?php echo $bnk->iban; ?></option>
                                <?php }
                                }
                                ?>
                            </select>
                            <label for="">Enter Point</label>
                            <input type="number" name="point_out" value="10" min="0" scope="any" class="form-control">
                            <input type="hidden" name="my_id" value="<?php echo $myid; ?>" min="0" scope="any" class="form-control">
                            <?php
                            if ($bank_exists) : ?>
                                <button id="submit-withdraw" type="button" class="btn btn-primary my-3">Confirm</button>
                            <?php else : ?>
                                <a class="btn btn-primary my-2" href="/<?php echo home; ?>/profile">Add Bank Details First</a>
                            <?php endif; ?>

                        </form>
                        <?php pkAjax_form("#submit-withdraw", "#withdraw-amt", "#res"); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php import("apps/view/inc/footer-credit.php"); ?>
    </div>
</div>
<?php
import("apps/view/inc/footer.php");
?>