<?php

use League\Csv\Writer;

$remark = 'requested';
if (isset($_GET['remark'])) {
    $remark = $_GET['remark'];
} else {
    $remark = 'requested';
}
$db = new Dbobjects;
$member_ctrl = new Member_ctrl;
$request_list = $member_ctrl->all_withdrawal_cancelled_list($db);
// return;

$sql = "SELECT * FROM credits WHERE status = 'paid' AND remark = '$remark' ORDER BY calculated_on DESC;";
if ($remark == 'cancelled') {
    $sql = "SELECT * FROM credits WHERE status = 'cancelled' AND remark = '$remark' ORDER BY calculated_on DESC;";
}
$unpaid_cmsn = $db->show($sql);


import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
if (!authenticate()) {
    die('Login required');
}
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <!-- <h1 class="mt-4">Dashboard</h1> -->
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active mycl">All withdrawal requests</li>
                </ol>

                <div class="container">
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div id="res"></div>
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Trans. ID</th>
                                        <th>Country</th>
                                        <th>Payee Username</th>
                                        <th>Payee Email</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Point</th>
                                        <th>Amount</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $csv_main_data = [];
                                    foreach ($request_list as $cms) {
                                        $cms = obj($cms);
                                        $sql = "SELECT * FROM pk_user WHERE pk_user.id = $cms->transacted_to;";
                                        $payeedata = $db->showOne($sql);
                                        $payee = null;
                                        if ($payeedata) {
                                            $payee = obj($payeedata);
                                            // for csv
                                            $csvdata['transaction_id'] = $cms->trn_num;
                                            $csvdata['payee_id'] = $payee->id;
                                            $csvdata['payee_email'] = $payee->email;
                                            $csvdata['username'] = $payee->username;


                                            $bank_account = null;
                                            $bank_name = null;
                                            $swift_code = null;
                                            $iban = null;
                                            $country_code = null;
                                            $bank_country_name = null;
                                            $flag = null;
                                            $jsnob = $payee->jsn;
                                            $jsn = json_decode($jsnob);
                                            if (isset($jsn->banks)) {
                                                $bank_account = isset($jsn->banks[0]->bank_account) ? $jsn->banks[0]->bank_account : null;
                                                $bank_name = isset($jsn->banks[0]->bank_name) ? $jsn->banks[0]->bank_name : null;
                                                $swift_code = isset($jsn->banks[0]->swift_code) ? $jsn->banks[0]->swift_code : null;
                                                $iban = isset($jsn->banks[0]->iban) ? $jsn->banks[0]->iban : null;
                                                $country_code = isset($jsn->banks[0]->country_code) ? $jsn->banks[0]->country_code : null;
                                                $bank_country_name = isset($jsn->banks[0]->country_name) ? $jsn->banks[0]->country_name : null;
                                                $flag = isset(getCurrency($country_code)['flag']) ? getCurrency($country_code)['flag'] : null;

                                                $csvdata['bank_name'] = $bank_name;
                                                $csvdata['bank_account'] = $bank_account;
                                                $csvdata['iban'] = $iban;
                                                $csvdata['swift_code'] = $swift_code;
                                                $csvdata['country'] = $bank_country_name;
                                            }
                                        }
                                        $csvdata['amount'] = $cms->amount;
                                        $csvdata['paid_on'] = $cms->updated_at;
                                        $csvdata['status'] = getTextFromCode($cms->status, TRN_STATUS);
                                        $csvdata['remark'] = $cms->remark;

                                    ?>
                                        <tr>
                                            <td><?php echo $cms->id; ?></td>
                                            <td><?php if ($flag) : ?>
                                                    <img src="data:image/png;base64,<?php echo $flag; ?>" alt="flag">
                                                    <?php echo $bank_country_name; ?>
                                                <?php else : ?>
                                                    Country Not updated
                                                <?php endif; ?>

                                            </td>
                                            <td><?php echo $payee ? $payee->username : "NA"; ?></td>
                                            <td><?php echo $payee ? $payee->email : "NA"; ?></td>
                                            <td><?php echo $cms->created_at; ?></td>
                                            <td><?php echo getTextFromCode($cms->status, TRN_STATUS); ?></td>
                                            <td><?php echo $cms->amount; ?></td>
                                            <td><?php echo $cms->real_amt; ?></td>
                                            <td><?php echo $cms->remark; ?></td>
                                        </tr>
                                    <?php
                                        $csv_main_data[] =    $csvdata;
                                    }


                                    ?>

                                </tbody>
                            </table>
                            <a href="/<?php echo home; ?>/wallet-paid.csv" download>Download CSV</a>
                        </div>
                        <?php
                        if (count($csv_main_data) > 0) {
                            $filePath = 'wallet-paid.csv';
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

        </main>
        <?php import("apps/view/inc/footer-credit.php"); ?>
    </div>
</div>
<?php
import("apps/view/inc/footer.php");
?>