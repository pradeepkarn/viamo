<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
if (!authenticate()) {
    die('Login required');
}
?>
<style>
    /* Media query for mobile devices */
    @media only screen and (max-width: 768px) {
        .nav_tabs {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .btn {
            width: 100%;
            margin-bottom: 5px;
        }
    }
</style>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <!-- <h1 class="mt-4">Dashboard</h1> -->
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active mycl">My Profile</li>
                </ol>

                <div class="container">
                    <div class="row mb-4">

                        <div class="col-md-12">
                            <div class="spl-box">
                                <div class="card">
                                    <div class="card-header">
                                        <p class="my-pr">Profiles</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="nav_tabs">
                                            <button class="btn tablinks1" onclick="openProfile(event, 'account')" id="defaultOpen1">All Data</button>
                                            <button class="btn tablinks1" onclick="openProfile(event, 'personaldata')" id="defaultOpen2"><i class="bi bi-person"></i> Delivery Address</button>
                                            <button class="btn tablinks1" onclick="openProfile(event, 'documents')" id="defaultOpen3"><i class="bi bi-person"></i> KYC</button>
                                            <button class="btn tablinks1" onclick="openProfile(event, 'bankaccounts')" id="defaultOpen4"><i class="bi bi-person"></i> Bank Accounts</button>
                                            <button class="btn tablinks1" onclick="openProfile(event, 'security')" id="defaultOpen5"><i class="bi bi-person"></i> Password</button>
                                        </div>

                                        <div class="row mt-3 mb-3">
                                            <div id="account" class="tabcontent1">
                                                <div class="col-md-12">
                                                    <?php
                                                    // $slaeCtrl = new Sales_ctrl;
                                                    // $sale = $slaeCtrl->get_sale_volume();
                                                    // $shrdata = $slaeCtrl->distribute_share_by_pool($total_sale = 10000);
                                                    // myprint($shrdata);

                                                    $uctrl = new User_ctrl;

                                                    $am_i_active = $uctrl->am_i_active(USER['id'])['active'];

                                                    // $pvctrl = new Pv_ctrl;
                                                    // $im_active = false;
                                                    // $dtt = $pvctrl->my_tree(USER['id'],1,$am_i_active);
                                                    // $dtts = $pvctrl->calculate_sum($dtt,1,10);
                                                    // // myprint($dtts);


                                                    ?>
                                                    <h5>Basic Info</h5>
                                                    <div class="box_r">
                                                        <div id="update-result"></div>
                                                        <form action="/<?php echo home; ?>/update-personal-data" id="update-p-data-form" method="post">
                                                            <!-- Form starts -->
                                                            <div class="row">
                                                                <div class="col-md-8 new_boxn">
                                                                    <h6 class="m-0">id:</h6>
                                                                </div>
                                                                <div class="col-md-4 new_boxn">
                                                                    <span><?php echo USER['id']; ?></span>
                                                                </div>
                                                                <div class="col-md-8 new_boxn">
                                                                    <h6 class="m-0">Username:</h6>
                                                                </div>
                                                                <div class="col-md-4 new_boxn">
                                                                    <span><?php echo USER['username']; ?></span>
                                                                </div>
                                                                <div class="col-md-8 new_boxn">
                                                                    <h6 class="m-0">Sponser Username:</h6>
                                                                </div>
                                                                <div class="col-md-4 new_boxn">
                                                                    <span><?php echo sponser_username(USER['ref']); ?></span>
                                                                </div>


                                                                <div class="col-md-12">
                                                                    <label for="">Company name</label>
                                                                    <input type="text" name="company_name" value="<?php echo USER['company_name']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">First name</label>
                                                                    <input type="text" name="first_name" value="<?php echo USER['first_name']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Last name</label>
                                                                    <input type="text" name="last_name" value="<?php echo USER['last_name']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="">Address</label>
                                                                    <input type="text" name="address" value="<?php echo USER['address']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">City</label>
                                                                    <input type="text" name="city" value="<?php echo USER['city']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Zip code</label>
                                                                    <input type="text" name="zipcode" value="<?php echo USER['zipcode']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Country</label>
                                                                    <select name="country_code" class="form-select">
                                                                        <?php
                                                                        // $json_data = file_get_contents("./jsondata/country.json");
                                                                        // $countries = json_decode($json_data);
                                                                        $plobj = new Model('countries');
                                                                        $countries = $plobj->index();
                                                                        foreach ($countries as $cnt) :
                                                                            $cnt = obj($cnt);
                                                                        ?>
                                                                            <option <?php echo USER['country_code'] == $cnt->code ? 'selected' : null; ?> value="<?php echo $cnt->code; ?>"><?php echo $cnt->name; ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Phone Number</label>
                                                                    <input type="text" name="mobile" value="<?php echo USER['mobile']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="">Email</label>
                                                                    <input type="text" readonly name="email" value="<?php echo USER['email']; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-md-8 new_boxn">
                                                                    <h6 class="m-0">Status:</h6>
                                                                </div>
                                                                <div class="col-md-4 new_boxn">
                                                                    <span class="text-success"><?php echo USER['is_active'] == 1 ? 'Active' : 'In active'; ?></span>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <button id="update-p-data-btn" type="button" class="btn btn-primary">Update</button>
                                                                    <?php pkAjax_form("#update-p-data-btn", "#update-p-data-form", "#update-result"); ?>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <!-- Form end -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div id="personaldata" class="tabcontent1">
                                                    <h5>Shipping Address</h5>
                                                    <?php $addrs = get_my_primary_address(USER['id']);
                                                    if ($addrs) {

                                                        // Split the name into an array using spaces as the delimiter
                                                        $nameParts = explode(' ', $addrs->name);

                                                        // Extract the first element as the first name
                                                        $firstName = array_shift($nameParts);

                                                        // Combine the remaining parts as the last name
                                                        $lastName = implode(' ', $nameParts);

                                                    ?>


                                                        <form class="material-form pt-2" id="update-address" action="/<?php echo home; ?>/update-address-ajax" method="POST">
                                                            <div class="form-group row mb-4">
                                                                <div class="col-md-12">
                                                                    <input class="form-control valid" type="text" required="" placeholder="Company" name="company" value="<?php echo $addrs->company; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mb-4">
                                                                <div class="col-md-6">
                                                                    <input type="hidden" name="id" value="<?php echo $addrs->id; ?>">
                                                                    <input class="form-control valid" type="text" required="" placeholder="" name="first_name" value="<?php echo $firstName; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="" name="last_name" value="<?php echo $lastName; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row mb-4">
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="Street" name="street" value="<?php echo $addrs->street; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="Street Number" name="street_num" value="<?php echo $addrs->street_num; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mb-4">
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="Postal Code" name="zipcode" value="<?php echo $addrs->zipcode; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="City" name="city" value="<?php echo $addrs->city; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div>


                                                            <div class="form-group row mb-4">

                                                                <div class="col-md-12 my-3">
                                                                    <label for="">Country</label>
                                                                    <select name="country_code" class="form-select">
                                                                        <?php
                                                                        // $json_data = file_get_contents("./jsondata/country.json");
                                                                        // $countries = json_decode($json_data);
                                                                        $plobj = new Model('countries');
                                                                        $countries = $plobj->index();
                                                                        foreach ($countries as $cnt) :
                                                                            $cnt = obj($cnt);
                                                                        ?>
                                                                            <option <?php echo $addrs->country_code == $cnt->code ? 'selected' : null; ?> value="<?php echo $cnt->code; ?>"><?php echo $cnt->name; ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>

                                                            </div>

                                                            <div class="form-group row mb-4">
                                                                <div class="col-md-2">
                                                                    <input class="form-control valid" placeholder="ISD Code" type="text" name="isd_code" value="<?php echo $addrs->isd_code; ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="number" required="" placeholder="Mobile" name="mobile" data-validation-required-message="Das ist ein Pflichtfeld" value="<?php echo $addrs->mobile; ?>" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-12">
                                                                    <input class="form-control valid" placeholder="Email" type="text" name="email" readonly value="<?php echo USER['email']; ?>">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 mb-4">
                                                            </div>


                                                        </form>

                                                        <div id="up_add"></div>
                                                        <button id="up_add_btn" class="btn btn-primary kyc_btn mt-3"><a href="">Save</a></button>
                                                        <?php pkAjax_form("#up_add_btn", "#update-address", "#up_add"); ?>
                                                    <?php } else {  ?>
                                                        <button class="btn btn-primary kyc_btn mt-3" onclick="openProfile(event, 'deliveryaddress')"> Set or add primary address</button>
                                                    <?php   }
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div id="deliveryaddress" class="tabcontent1">
                                                    <div class="deliver_add">
                                                        <h5>delivery addresses</h5>
                                                        <!-- Button trigger modal -->
                                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">New shipping-address</button>
                                                    </div>
                                                    <form class="material-form pt-2">
                                                        <div class="form-group row">
                                                            <div class="col-md-12">

                                                                <div class="form-group my-3" style="height: 200px; overflow-y:scroll; background-color:white;">
                                                                    <?php
                                                                    $addrs = new Model('address');
                                                                    $myaddress_list = $addrs->filter_index(['user_id' => $_SESSION['user_id']]);
                                                                    foreach ($myaddress_list as $ad) {
                                                                        $ad = (object) $ad;
                                                                    ?>

                                                                        <input <?php echo $ad->address_type == 'primary' ? 'checked' : null; ?> type="radio" class="addrs-data" name="pr_address" value="<?php echo $ad->id; ?>">
                                                                        <?php echo $ad->address_type != "" ? "(<b>$ad->address_type</b>)" : null; ?> <?php echo $ad->city; ?> - <?php echo $ad->state; ?>, <?php echo $ad->name; ?> <?php echo $ad->zipcode; ?> <br>

                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div id="res"></div>
                                                            </div>
                                                        </div>
                                                        <button id="cnfrmadrs-btn" class="btnmodal btn-primary kyc_btn mt-3"><a href="">Save</a></button>
                                                        <?php pkAjax("#cnfrmadrs-btn", "/primary-address-ajax", ".addrs-data", "#res"); ?>
                                                    </form>



                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form class="material-form" id="address_form" action="/<?php echo home; ?>/new-address-ajax" method="POST">
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <label for="">Name</label>
                                                                                <input type="text" class="form-control my-2 valid" placeholder="" name="ad_name">
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="">Address Name</label>
                                                                                <input type="text" class="form-control my-2 valid" placeholder="" name="address">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <label for="">ISD Code</label>
                                                                                <input class="form-control my-2 valid" placeholder="Search phone Code" type="text" id="phonesrch" name="key_ctry_code">
                                                                                <div id="res-code">
                                                                                    <select name="country_code" class="form-select">
                                                                                        <?php
                                                                                        $json_data = file_get_contents("./jsondata/phonecode.json");
                                                                                        $items = json_decode($json_data, true);
                                                                                        if (count($items) != 0) {
                                                                                            foreach ($items as $item) {
                                                                                        ?>

                                                                                                <option selected value="<?= $item['dial_code']; ?>"><?= $item['dial_code']; ?></option>
                                                                                        <?php
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                                <?php pkAjax("#phonesrch", "/country-code-search-ajax", "#phonesrch", "#res-code", 'keyup'); ?>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="">Mobile</label>
                                                                                <input type="number" class="form-control my-2 valid" placeholder="" name="mobile">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-2">
                                                                            <div class="col">
                                                                                <label for="">City</label>
                                                                                <input type="text" class="form-control my-2 valid" placeholder="" name="city">
                                                                            </div>

                                                                            <div class="col">
                                                                                <label for="">State</label>
                                                                                <input type="text" class="form-control my-2 valid" placeholder="" name="state">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <label for="">Street(Optional)</label>
                                                                                <input type="text" class="form-control my-2 valid" placeholder="" name="street">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <label for="">Country</label>
                                                                                <input class="form-control my-2 valid" type="text" placeholder="Search country" id="cntrysrch" name="key_ctry">
                                                                                <div id="res-cntr">
                                                                                    <select name="country" class="form-control">
                                                                                        <?php
                                                                                        $json_data = file_get_contents("./jsondata/country.json");
                                                                                        $items = json_decode($json_data, true);
                                                                                        if (count($items) != 0) {
                                                                                            foreach ($items as $item) {
                                                                                        ?>

                                                                                                <option value="<?= $item['code']; ?>"><?= $item['name']; ?></option>
                                                                                        <?php
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                                <?php pkAjax("#cntrysrch", "/country-search-ajax-profile", "#cntrysrch", "#res-cntr", 'keyup'); ?>

                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="">Zipcode</label>
                                                                                <input type="text" class="form-control my-2 valid" placeholder="" name="zipcode">
                                                                            </div>
                                                                        </div>

                                                                    </form>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <div id="uplad"></div>
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="button" id="myadd_btn" class="btn btn-primary">Save changes</button>
                                                                    <?php pkAjax_form("#myadd_btn", "#address_form", "#uplad"); ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>



                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="documents" class="tabcontent1">
                                                    <div class="card">

                                                        <div class="card-body">
                                                            <div class="row mt-3 mb-3">
                                                                <div class="col-md-12">
                                                                    <div class="card-header">
                                                                        <h5>identity document</h5>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div id="res-kyc"></div>
                                                                        <p>In order for us to be able to process your payout we need to verify your identity. Please upload this document here. After successful verification of your payout should be available in a few days.</p>
                                                                        <p>Documents we accept for identity verification:<br>
                                                                            passport, identity card</p>
                                                                        <form id="uploadKycForm" action="/<?php echo home; ?>/upload-kyc-ajax">
                                                                            <div class="my-3 d-flex justify-between gap-2">
                                                                                <!-- <div>
                                                                                    <label for="nid_num">NUMBER</label>
                                                                                    <input type="text" name="nid_num" class="form-control">
                                                                                </div> -->
                                                                                <div>
                                                                                    <label for="nid_doc">FILE</label>
                                                                                    <input accept="application/pdf" type="file" name="nid_doc" class="form-control w-100">
                                                                                </div>
                                                                                <div>
                                                                                    <label for="">Action</label>
                                                                                    <button id="uploadKycBtn" type="button" class="btn btn-primary">Upload</button>
                                                                                </div>
                                                                            </div>

                                                                        </form>
                                                                        <?php pkAjax_form("#uploadKycBtn", "#uploadKycForm", "#res-kyc"); ?>
                                                                        <a class="upl_txt" href="/<?php echo home; ?>/media/docs/profiles/<?php echo USER['nid_doc']; ?>">VIEW</a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 pt-3">
                                                                    <div class="card-header">
                                                                        <h5>Proof of address</h5>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <p>In order for us to be able to process your Payout we need to verify your address. Please upload this document here. After successful verification of your payout should be available in a few days.</p>
                                                                        <p>Documents we accept for identity verification:<br>
                                                                            passport, identity cardUtility Bill (Electric, Mobile Phone, Internet, ...)</p>
                                                                        <form id="uploadKycAddreesForm" action="/<?php echo home; ?>/upload-kyc-ajax">
                                                                            <div class="my-3 d-flex justify-between gap-2">
                                                                                <!-- <div>
                                                                                    <label for="address_num">NUMBER</label>
                                                                                    <input type="text" name="nid_num" class="form-control">
                                                                                </div> -->
                                                                                <div>
                                                                                    <label for="address_doc">FILE</label>
                                                                                    <input accept="application/pdf" type="file" name="address_doc" class="form-control w-100">
                                                                                </div>
                                                                                <div>
                                                                                    <label for="">Action</label>
                                                                                    <button id="uploadKycAddreesBtn" type="button" class="btn btn-primary">Upload</button>

                                                                                </div>
                                                                            </div>

                                                                        </form>
                                                                        <?php pkAjax_form("#uploadKycAddreesBtn", "#uploadKycAddreesForm", "#res-kyc"); ?>
                                                                        <a class="upl_txt" href="/<?php echo home; ?>/media/docs/profiles/<?php echo USER['address_doc']; ?>">VIEW</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary kyc_btn mt-3"><a href="/<?php echo home; ?>/kyc-upload">KYC upload</a></button>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="bankaccounts" class="tabcontent1">
                                                    <h5>Bank account</h5>
                                                    <?php
                                                    $bank_account = null;
                                                    $bank_name = null;
                                                    $swift_code = null;
                                                    $iban = null;
                                                    $country_code = null;
                                                    $bank_country_name = null;
                                                    $jsn = USER['jsn'];
                                                    $jsn = json_decode($jsn);
                                                    if (isset($jsn->banks)) {
                                                        $bank_account = isset($jsn->banks[0]->bank_account) ? $jsn->banks[0]->bank_account : null;
                                                        $bank_name = isset($jsn->banks[0]->bank_name) ? $jsn->banks[0]->bank_name : null;
                                                        $swift_code = isset($jsn->banks[0]->swift_code) ? $jsn->banks[0]->swift_code : null;
                                                        $iban = isset($jsn->banks[0]->iban) ? $jsn->banks[0]->iban : null;
                                                        $country_code = isset($jsn->banks[0]->country_code) ? $jsn->banks[0]->country_code : null;
                                                        $bank_country_name = isset($jsn->banks[0]->country_name) ? $jsn->banks[0]->country_name : null;
                                                    }
                                                    // myprint($jsn);
                                                    ?>
                                                    <div id="res-update-bank-account"></div>
                                                    <form class="material-form pt-2" id="update_bank_details_form" method="post" action="/<?php echo home; ?>/save-bank-account-ajax">
                                                        <div class="form-group row mb-4">
                                                            <div class="col-md-6">
                                                                <label for="">Name of Account Holder</label>
                                                                <input type="text" name="bank_account" value="<?php echo $bank_account; ?>" class="form-control" placeholder="Bank Account Number">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="">Bank Name</label>
                                                                <input type="text" name="bank_name" value="<?php echo $bank_name; ?>" class="form-control" placeholder="Bank Name">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="">IBAN</label>
                                                                <input type="text" name="iban" value="<?php echo $iban; ?>" class="form-control" placeholder="IBAN">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="">Swift Code</label>
                                                                <input type="text" name="swift_code" value="<?php echo $swift_code; ?>" class="form-control" placeholder="Swift code">
                                                            </div>
                                                            <!-- <div class="col-md-12">
                                                                    <textarea name="bank_account" placeholder="Provide your bank account details" rows="5" class="form-control"></textarea>
                                                                </div> -->
                                                            <div class="col">
                                                                <input class="form-control my-2 valid" type="text" placeholder="Search and select the country" id="cntrbnksrh" name="key_ctry">
                                                                <div id="bank-cntry">
                                                                    <select name="country" class="form-select">
                                                                        <?php
                                                                        $json_data = file_get_contents("./jsondata/country.json");
                                                                        $items = json_decode($json_data, true);
                                                                        if (count($items) != 0) {
                                                                            foreach ($items as $item) {
                                                                        ?>
                                                                                <option <?php echo $country_code == $item['code'] ? 'selected' : null; ?> value="<?= $item['code']; ?>"><?= $item['name']; ?></option>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <?php pkAjax("#cntrbnksrh", "/country-search-ajax-profile", "#cntrbnksrh", "#bank-cntry", 'keyup'); ?>

                                                            </div>
                                                        </div>
                                                        <!-- <div class="form-group row mb-4">
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="account holder" name="name" data-validation-required-message="Das ist ein Pflichtfeld" aria-required="true" aria-invalid="false">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input class="form-control valid" type="text" required="" placeholder="bank name" name="name" data-validation-required-message="Das ist ein Pflichtfeld" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div> -->

                                                        <!-- <div class="form-group row mb-4">
                                                                <div class="col-md-7">
                                                                    <input class="form-control valid" type="text" required="" placeholder="IBAN (Just the number without spaces)" name="name" data-validation-required-message="Das ist ein Pflichtfeld" aria-required="true" aria-invalid="false">
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input class="form-control valid" type="text" required="" placeholder="SWIFT / BIC (Just the number without spaces)" name="name" data-validation-required-message="Das ist ein Pflichtfeld" aria-required="true" aria-invalid="false">
                                                                </div>
                                                            </div> -->
                                                        <button id="update_bank_details_btn" class="btn btn-primary kyc_btn mt-3">Update</button>
                                                    </form>
                                                    <?php pkAjax_form("#update_bank_details_btn", "#update_bank_details_form", "#res-update-bank-account"); ?>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div id="security" class="tabcontent1">
                                                    <h5>Change password</h5>
                                                    <div id="pass-change"></div>
                                                    <form id="change-pass-form" action="/<?php echo home; ?>/change-password-ajax" class="material-form pt-2">
                                                        <div class="form-group row mb-4">
                                                            <div class="col-md-6">
                                                                <label for="">Old Password</label>
                                                                <input class="form-control valid" type="password" name="old_pass">
                                                                <label for="">New Password</label>
                                                                <input class="form-control valid" type="password" name="new_pass">
                                                                <label for="">Confirm Password</label>
                                                                <input class="form-control valid" type="password" name="cnf_pass">
                                                            </div>
                                                        </div>
                                                        <button id="change-pass-btn" class="btn btn-primary kyc_btn mt-3">Save</button>
                                                    </form>
                                                    <?php pkAjax_form("#change-pass-btn", "#change-pass-form", "#pass-change"); ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <?php
                                $wallet = obj(liveWallet($userid = USER['id']));
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