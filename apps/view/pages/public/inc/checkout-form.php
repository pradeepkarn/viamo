<form id="signup-form" method="post" action="<?php echo BASE_URI; ?>/public/signup-ajax">
    <div class="row my-3">
        <div class="col-md-6 my-2">
            <label for="firstName">First Name</label>
            <input type="text" name="first_name" class="form-control" id="firstName" placeholder="Enter your first name" required>
        </div>
        <div class="col-md-6 my-2">
            <label for="lastName">Last Name</label>
            <input type="text" name="last_name" class="form-control" id="lastName" placeholder="Enter your last name" required>
        </div>
        <!-- <div class="col-md-4 my-2">
            <label for="dob">Date of Birth</label>
            <input type="date" name="birthday" class="form-control" id="dob" required>
        </div>

        <div class="col-md-2 my-2">
            <label for="dob">Gender</label>
            <select name="gender" class="form-control">
                <option value="f">Female</option>
                <option value="m">Male</option>
                <option value="o">Not Specified</option>
            </select>
        </div> -->


        <div class="col-md-6 my-2">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" id="username" placeholder="Enter your Email" required>
        </div>
        <!-- <div class="col-md-6 my-2">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control" id="username" placeholder="Enter your username" required>
        </div> -->
        <div class="col-md-3 my-2">
            <label for="username">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
        </div>
        <div class="col-md-3 my-2">
            <label for="username">Confirm Password</label>
            <input type="password" name="cnf_password" class="form-control" id="password" placeholder="Enter your password" required>
        </div>
        <div class="col-md-3 my-2">
            <label for="username">Sponsor</label>
            <input disabled type="text" class="form-control" value="<?php echo isset($_SESSION['sponserid']) ? $_SESSION['sponserid'] : null; ?>">
        </div>

        <div class="col-md-9 my-2">
            <div class="row">
                <div class="col-md-3">
                    <label for="">Searh Phone Code</label>
                    <input class="form-control valid" placeholder="Phone Code search" type="text" id="phonesrch" name="key_ctry_code">
                </div>
                <div class="col-md-3">
                    <label for="">Phone Code</label>
                    <div id="res-code">
                        <select name="country_code" class="form-select" id="">
                            <?php
                            $json_data = file_get_contents("./jsondata/phonecode.json");
                            $items = json_decode($json_data, true);
                            if (count($items) != 0) {
                                foreach ($items as $item) {
                            ?>

                                    <option <?php echo $item['dial_code'] == '+43' ? "selected" : null; ?> value="<?php echo $item['dial_code']; ?>"><?php echo $item['dial_code']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="">Phone number</label>
                    <input class="form-control valid" type="text" required="" placeholder="phone number" name="mobile" data-validation-required-message="Das ist ein Pflichtfeld" value="" aria-required="true" aria-invalid="false">
                </div>
            </div>

            <?php pkAjax("#phonesrch", "/country-code-search-ajax", "#phonesrch", "#res-code", 'keyup'); ?>
        </div>


        <div class="col-md-6 my-2">
            <div class="row">
                <div class="col-md-6">
                    <label for="">Search country</label>
                    <input class="form-control valid" placeholder="Country search" type="text" id="cntrysrch" name="key_ctry">
                </div>
                <div class="col-md-6">
                    <label for="">Country select</label>
                    <div id="res-cntr">
                        <select name="country" class="form-select">
                            <?php
                            $plobj = new Model('countries');
                            $items = $plobj->index();
                            if (count($items) != 0) {
                                foreach ($items as $item) {
                            ?>
                                    <option <?php echo $item['id']=='15'?"selected":null; ?> value="<?= $item['id']; ?>"><?= $item['name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <?php pkAjax("#cntrysrch", "/country-search-ajax", "#cntrysrch", "#res-cntr", 'keyup'); ?>
                </div>

            </div>
        </div>
        <div class="col-md-6 my-2">
            <label for="">State</label>
            <input class="form-control valid" type="text" required="" placeholder="State (optional)" name="state">
        </div>
        <div class="col-md-3 my-2">
            <label for="city">City</label>
            <input type="text" name="city" class="form-control" id="city" placeholder="Enter your city" required>
        </div>
        <div class="col-md-3 my-2">
            <label for="street">Street</label>
            <input type="text" name="street" class="form-control" id="street" placeholder="Enter your street" required>
        </div>
        <div class="col-md-6 my-2">
            <label for="streetNo">Street Number</label>
            <input type="text" name="street_num" class="form-control" id="streetNo" required>
        </div>
        <div class="col-md-6 my-2">
            <label for="zipcode">Zip Code</label>
            <input type="text" name="zipcode" class="form-control" id="zipcode" required>
        </div>
        <div class="col-md-12">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="col-m-6 my-2">
            <div id="signup-res"></div>
            <?php
            $sponserid = isset($_SESSION['sponserid']) ? $_SESSION['sponserid'] : null;
            $db = new Dbobjects;
            $ref = $db->showOne("select id from pk_user where username = '$sponserid'");
            ?>
            <?php if($ref): ?>
            <input type="hidden" name="public" value="1">
            <input type="hidden" name="ref" class="form-control" value="<?php echo $ref ? $ref['id'] : null; ?>">
            <?php endif; ?>
            <button type="button" class="btn btn-primary" id="signup-btn">
                Proceed To checkout
            </button>
        </div>

    </div>
</form>
<?php pkAjax_form("#signup-btn", "#signup-form", "#signup-res", 'click');
ajaxActive(".spinner-border");
?>