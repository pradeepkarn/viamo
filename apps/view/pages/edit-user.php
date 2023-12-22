<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
if (!authenticate()) {
    die('Login required');
}
$user = $context['user'];
if (is_superuser()) :
    if (isset($_POST['rv']) && isset($_POST['added_to']) && isset($_POST['action'])) {
        if (intval($_POST['rv']) && intval($_POST['added_to'])) {
            $arrrv['rv'] = abs($_POST['rv']);
            $arrrv['added_to'] = intval($_POST['added_to']);
            $arrrv['added_by'] = USER['id'];
            $rvdb = new Model('rank_advance');
            $rvdb->store($arrrv);
        }
    }
    if (isset($_POST['delete_rv_id']) && isset($_POST['action'])) {
        if (intval($_POST['delete_rv_id'])) {
            $rvdb = new Model('rank_advance');
            $rvdb->destroy($_POST['delete_rv_id']);
        }
    }
    ######################################endregion  if (isset($_POST['rv']) && isset($_POST['added_to']) && isset($_POST['action'])) {
    if (isset($_POST['commission']) && isset($_POST['added_to']) && isset($_POST['action'])) {
        if (intval($_POST['commission']) && intval($_POST['added_to'])) {
            $arrrv['rv'] = abs($_POST['rv']);
            $arrrv['added_to'] = intval($_POST['added_to']);
            $arrrv['added_by'] = USER['id'];
            $arrrv['amount'] = abs(floatval($_POST['commission']));
            if ($arrrv['amount'] > 0) {
                $rvdb = new Model('extra_credits');
                $rvdb->store($arrrv);
            }
        }
    }
    if (isset($_POST['delete_amt_id']) && isset($_POST['action']) && $_POST['action'] == "cmsndlt") {
        if (intval($_POST['delete_amt_id'])) {
            $rvdb = new Model('extra_credits');
            $rvdb->destroy($_POST['delete_amt_id']);
        }
    }

endif;
$udata = obj((new User_ctrl)->my_all_commission($userid = $user['id']));
$position = $udata->position;
$cmsn_gt = $udata->cmsn_gt;
$total_paid = $udata->total_paid;
$total_unpaid = $udata->total_unpaid;
$rv_sum = $udata->rv_gt;
// $last_date = last_active_date($user_id = $user['id']);
// $tree  = my_tree($ref = $user['id'], 1, $last_date);
// $depth = 1;
// $treeLength = count($tree);
// $calc = calculatePercentageSum($data = $tree, $depth, $treeLength, $user['id']);
// $sum = $calc['sum'];
// $rv_sum = $calc['rv_sum'] + my_rv_and_admin_rv($user_id = $user['id'], $dbobj = null);
// $jsonData = json_encode($tree, JSON_PRETTY_PRINT);
// $file = "jsondata/trees/tree_" . $user['id'] . '.json';
// file_put_contents($file, $jsonData);
// $db = new Model('credits');
// $crarr['user_id'] = $user['id'];
// $crarr['status'] = 'lifetime';
// $already = $db->filter_index($crarr);
// if (count($already) > 0) {
//     $crid = obj($already[0]);
//     $crarr['amt'] = $sum;
//     $db->update($id = $crid->id, $crarr);
// } else {
//     $crarr['amt'] = $sum;
//     $db->store($crarr);
// }
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <!-- <h1 class="mt-4">Dashboard</h1> -->
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active mycl">Profile</li>
                </ol>

                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <div class="spl-box">
                                <div class="new-box">
                                    <img src="/<?php echo home; ?>/media/img/user-blank.png" class="img-circle" width="150px" alt="" srcset="">
                                    <h4 class="mt-2 mypl"><?php echo $user['username']; ?></h4>
                                    <h6 class="mypl1 mb-3"><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></h6>
                                    <p class="mypl1">ID: <span><?php echo $user['id']; ?></span></p>
                                    <hr class="mypr">
                                </div>

                            </div>
                            <!-- <div class="card">
                                <div class="card-body">
                                    <form action="" method="post">
                                        <div class="row m">
                                            <div class="col-md-6 my-3">
                                                <input  min="0" placeholder="Commission" type="text" scope="any" name="commission" class="form-control">
                                                <input type="hidden" name="added_to" value="<?php //echo $user['id']; 
                                                                                            ?>">
                                                <input type="hidden" name="action" value="add_cmsn">
                                            </div>
                                            <div class="col-md-6 my-3">
                                                <button type="submit" class="btn btn-primary">Add commission</button>
                                            </div>
                                        </div>
                                    </form> 
                                </div>
                            </div> -->
                            <div class="row mb-4">

                                <div class="col-md-12">
                                    <style>
                                        /* Apply styles to the editable rows */
                                        .table tbody tr {
                                            background-color: #B3B3B3;
                                        }

                                        .table tbody tr:hover {
                                            background-color: #FFFCCC;
                                            cursor: pointer;
                                            /* Hover background color */
                                        }
                                    </style>
                                    <h4>Delivery addresses:</h4>
                                    <div id="res"></div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered border-primary">
                                            <thead>
                                                <tr>
                                                    <th>Street</th>
                                                    <th>Street Num</th>
                                                    <th>City</th>
                                                    <th>Zip</th>
                                                    

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $db = new Dbobjects;
                                                $delvadrs = $db->show("select * from address where user_id = '{$user['id']}'");
                                                // myprint($delvadrs);
                                                foreach ($delvadrs as $key => $da) :
                                                    $da = obj($da);
                                                ?>
                                                    <tr data-address-id="<?= $da->id; ?>">
                                                        <td class="editable" data-field="street" contenteditable="true"><?= $da->street; ?></td>
                                                        <td class="editable" data-field="street_num" contenteditable="true"><?= $da->street_num; ?></td>
                                                        <td class="editable" data-field="city" contenteditable="true"><?= $da->city; ?></td>
                                                        <td class="editable" data-field="zipcode" contenteditable="true"><?= $da->zipcode; ?></td>
                                                        
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <script>
                                        $(document).ready(function() {
                                            // Handle click-to-edit
                                            $('.editable').on('click', function() {
                                                $(this).attr('contenteditable', true).focus();
                                            });
                                            // Handle saving changes via AJAX
                                            $('.editable').on('blur', addressUpdate);
                                        });

                                        function addressUpdate() {
                                            const addressId = $(this).closest('tr').data('address-id');
                                            const field = $(this).data('field');
                                            const value = $(this).text();

                                            // AJAX request to update the value in the database
                                            $.ajax({
                                                url: '/<?php echo home; ?>/update-address', // Change this to your server-side script
                                                method: 'POST',
                                                data: {
                                                    addressId: addressId,
                                                    field: field,
                                                    value: value
                                                },
                                                success: function(response) {
                                                    $("#res").html(response);
                                                }
                                            });

                                            $(this).attr('contenteditable', false);
                                        }
                                    </script>

                                    </script>
                                </div>

                            </div>


                        </div>
                        <div class="col-md-7">
                            <a href="/<?php echo home; ?>/all-users" class="btn btn-dark">Back</a>
                            <div id="res"></div>

                            <div class="row">
                                <!-- form -->
                                <form class="row g-3" id="update-profile-form" method="post" action="/<?php echo home; ?>/update-profile-by-admin-ajax">
                                    <div class="col-md-12 my-2">
                                        <label for="inputEmail4" class="form-label">Email</label>
                                        <input type="email" name="email" value="<?php echo $user['email']; ?>" class="form-control" id="inputEmail4">
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="fname" class="form-label">Firstname</label>
                                        <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" class="form-control" id="fname">
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="lname" class="form-label">Lastname</label>
                                        <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" class="form-control" id="lname">
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="inputUsername" class="form-label">Username</label>
                                        <input type="text" name="username" value="<?php echo $user['username']; ?>" class="form-control" id="inputUsername">
                                    </div>
                                    <div class="col-md-6 my-2">
                                        <label for="comp" class="form-label">Company</label>
                                        <input type="text" name="company_name" value="<?php echo $user['company_name']; ?>" class="form-control" id="comp">
                                    </div>

                                    <div class="col-md-6 my-2">
                                        <div id="postioncont"></div>
                                        <label for="">Sponser</label>
                                        <style>
                                            /* Apply the height to the select within #isdCodeSearchContainer */
                                            .select2-selection--single,
                                            #mobileInput {
                                                height: 35px !important;
                                                width: 100% !important;
                                                margin-top: 10px;
                                            }
                                        </style>
                                        <select id="positions" class="form-select mb-2">
                                            <?php
                                            $db = new Dbobjects;
                                            $users = $db->show("select id, username from pk_user where id != '{$user['id']}' AND is_active=1");;
                                            $usrrr = [];
                                            foreach ($users as $key => $usr) {
                                            ?>
                                                <option value="<?php echo $usr['id']; ?>"><?php echo $usr['username']; ?></option>
                                            <?php }
                                            $jsnusers = json_encode($users);
                                            ?>
                                        </select>
                                        <script>
                                            $(document).ready(function() {
                                                let prevIsdCode = "<?php echo $user['ref']; ?>";
                                                if (prevIsdCode) {
                                                    $('#positions').val(prevIsdCode);
                                                }
                                                // Initialize Select2 on the ISD code search input
                                                $('#positions').select2({
                                                    placeholder: 'Search Sponser',
                                                    data: <?php echo $jsnusers; ?>
                                                });
                                                // Handle search functionality
                                                $('#positions').on('change', function() {
                                                    var selectedCode = $(this).val();
                                                    // Add the selected value to the form data
                                                    $("#postioncont").html('<input type="hidden" name="ref" value="' + selectedCode + '">');
                                                });
                                            });
                                        </script>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="">Address</label>
                                        <input type="text" name="address" value="<?php echo $user['address']; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">City</label>
                                        <input type="text" name="city" value="<?php echo $user['city']; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Zip code</label>
                                        <input type="text" name="zipcode" value="<?php echo $user['zipcode']; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">ISD</label>
                                        <input type="text" name="isd_code" value="<?php echo $user['isd_code']; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Mobile</label>
                                        <input type="text" name="mobile" value="<?php echo $user['mobile']; ?>" class="form-control">
                                    </div>


                                    <div class="col-md-6 my-2">
                                        <label for="inputPassword4" class="form-label">Password</label>
                                        <input type="password" autocomplete="off" name="password" value="<?php echo $user['password']; ?>" class="form-control" id="inputPassword4">
                                        <input type="hidden" name="userid" value="<?php echo $user['id']; ?>">
                                        <input type="checkbox" name="change_password">
                                    </div>
                                    <div class="col-12 my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" name="checkmeout" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Check me out
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button id="updateprofile" type="button" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                                <?php pkAjax_form("#updateprofile", "#update-profile-form", "#res"); ?>



                                <!-- form end-->
                                <!-- <form action="" method="post">
                                    <div class="row m">
                                        <div class="col-md-6 my-3">
                                            <input min="0" placeholder="RV" type="number" name="rv" class="form-control">
                                            <input type="hidden" name="added_to" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="add_rv">
                                        </div>
                                        <div class="col-md-6 my-3">
                                            <button type="submit" class="btn btn-primary">Add on</button>
                                        </div>
                                    </div>
                                </form> -->
                                <div class="row mb-4">
                                    <div class="col-lg-12">
                                        <!-- <table id="datatablesSimple" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Trans. ID</th>
                                                    <th>RV</th>
                                                    <th>Date</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Trans. ID</th>
                                                    <th>RV</th>
                                                    <th>Date</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                $db = new Dbobjects;
                                                $sql = "select * from rank_advance where added_to = {$user['id']} and status = 'confirmed'";
                                                $rvs = $db->show($sql);
                                                $total_rv = 0;
                                                ?>
                                                <?php foreach ($rvs as $rvarr) {
                                                    $rv = obj($rvarr);
                                                    $total_rv += $rv->rv;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $rv->id; ?></td>
                                                        <td><?php echo $rv->rv; ?></td>
                                                        <td><?php echo $rv->created_at; ?></td>
                                                        <td>
                                                            <form action="" method="post">
                                                                <input type="hidden" name="delete_rv_id" value="<?php echo $rv->id; ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <button class="btn btn-sm btn-danger">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                            </tbody>
                                        </table>
                                        <h4>Total RV by admin : <?php echo $total_rv; ?></h4>
                                        <h3>Total RV = <?php echo $rv_sum; ?></h3> -->

                                    </div>
                                </div>
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