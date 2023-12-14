<?php
if (!$_SESSION['guest_id']) {
  die("Login first");
}
import("apps/view/inc/header.php");
import("apps/view/pages/public/inc/navbar.php");
$vctrl = new Voucher_ctrl;
if (isset($_POST['point_action'])) {
  $_SESSION['use_point'] = isset($_POST['use_point']) ? true : false;
}
if (isset($_POST['voucher_action'], $_POST['voucher_code'])) {
  $code = sanitize_remove_tags($_POST['voucher_code']);
  $vchr = $vctrl->check_voucher($code = $code);
  if ($vchr) {
    $_SESSION['voucher_code'] = $code;
  } else {
    $_SESSION['voucher_code'] = null;
    msg_set("Invalid voucher code", 'vchr');
  }
}
$db = new Dbobjects;
$level = new Member_ctrl;
$point = $level->net_balance_minus_requested_balance($db, $myid = $_SESSION['guest_id']);
$addrs = get_my_primary_address($userid = $_SESSION['guest_id']);
$addrs = new stdClass;
$addrs->ccode = MY_COUNTRY;
// myprint($addrs);
?>
<div>
  <?php //import("apps/view/inc/sidebar.php"); 
  ?>
  <div class="mt-5">
    <main>
      <div class="container">
        <h1 class="mt-4">Payment</h1>
        <ol class="breadcrumb mb-4 mypop">
          <li class="breadcrumb-item active">Payment</li>
          <?php
          ?>
        </ol>

        <div class="table-responsive">
          <table class="table table-bordered" style="font-size: 14px;">
            <thead class="table-light">
              <tr>
                <th width="10%" scope="col">Photo</th>
                <th width="30%" scope="col">Product</th>
                <th width="8%" scope="col">PV</th>
                <!-- <th width="8%" scope="col">RV</th> -->
                <!-- <th width="8%" scope="col">Direct Bonus</th> -->
                <th width="11%" scope="col">Price € (excluding tax)</th>
                <th width="10%" scope="col">Tax % </th>
                <th width="10%" scope="col">Quantity</th>

                <th width="10%" scope="col">Price (with tax)</th>
                <th width="10%" scope="col" class="text-end">Amount</th>

              </tr>
            </thead>
            <div id="res"></div>
            <?php
            $myordersObj = new Model('customer_order');
            $cart_list = $myordersObj->filter_index(array('status' => 'cart', 'user_id' => $_SESSION['guest_id']));
            ?>
            <?php
            $total_amt = 0;
            $total_pv = 0;
            $total_db = 0;
            $total_gm = 0;
            foreach ($cart_list as $cv) :
              $cv = (object) $cv;
              $item = (object) getData('item', $cv->item_id);
              $phpobj = json_decode($item->jsn);
              $gm = 0;
              // myprint($phpobj->items);
              foreach ($phpobj->items as $pkey => $prd) {
                $prod = (object) (new Dbobjects)->showOne("select id,qty,unit from item where id = '$prd->item'");
                $gm += calculate_gram($prod, $cv->qty * $prd->qty);
                $total_gm += $gm;
              }

              $price_with_tax = round($cv->price);
              $price_without_tax = ($price_with_tax / (100 + $cv->tax)) * 100;
              $amt = round(($cv->qty * $cv->price), 2);
              $total_rv = round(($cv->qty * $cv->rv), 2);
              $total_amt += $amt;
              $total_pv += round(($cv->qty * $cv->pv), 2);
              $total_db += round(($cv->qty * $cv->direct_bonus), 2);

            ?>
              <tbody>

                <tr>
                  <th scope="row"><img src="/<?php echo home; ?>/media/upload/items/<?php echo $item->image; ?>" width="80px" alt="" srcset=""></th>
                  <td><?php echo $item->name; ?></td>
                  <td><?php echo $cv->pv; ?></td>
                  <!-- <td><?php //echo $cv->rv; 
                            ?></td> -->
                  <!-- <td><?php // echo $cv->direct_bonus; 
                            ?></td> -->
                  <td><?php echo round($price_without_tax, 2); ?></td>
                  <td><?php echo $cv->tax; ?></td>

                  <td>
                    <div class="input-group product_data mb-3" style="width: 130px;">
                      <!-- <input type="hidden" class="qty<?php //echo $cv->id; 
                                                          ?>" name="cart_id" value="<?php //echo $cv->id; 
                                                                                    ?>">
                      <input type="hidden" class="qty<?php //echo $cv->id; 
                                                      ?>" name="price" value="<?php //echo $cv->price; 
                                                                              ?>"> -->
                      <input type="hidden" class="qty-dec<?php echo $cv->id; ?>" name="item_id" value="<?php echo $cv->item_id; ?>">
                      <input type="hidden" class="qty-dec<?php echo $cv->id; ?>" name="action" value="remove">
                      <input type="hidden" class="qty-inc<?php echo $cv->id; ?>" name="item_id" value="<?php echo $cv->item_id; ?>">
                      <input type="hidden" class="qty-inc<?php echo $cv->id; ?>" name="action" value="add">
                      <button id="decrease-btn<?php echo $cv->id; ?>" class="input-group-text decrement-btn">-</button>
                      <input readonly type="text" class="form-control text-center input-qty bg-white qty<?php echo $cv->id; ?>" aria-label="Amount (to the nearest dollar)" name="qty" value="<?php echo $cv->qty; ?>">
                      <button id="increament-btn<?php echo $cv->id; ?>" class="input-group-text increment-btn">+</button>
                    </div>
                    <?php
                    pkAjax("#decrease-btn{$cv->id}", "/public/purchase-decrease-qty-ajax", ".qty-dec{$cv->id}", "#res");
                    pkAjax("#increament-btn{$cv->id}", "/public/purchase-increase-qty-ajax", ".qty-inc{$cv->id}", "#res");
                    ?>
                  </td>

                  <td><?php echo $price_with_tax; ?></td>
                  <td class="text-end"><?php echo $amt; ?></td>

                </tr>
                <tr>

                </tr>
              </tbody>
            <?php endforeach; ?>


            <tbody>
              <tr class="text-end">
                <td colspan="1"></td>
                <td colspan=""><?php echo $total_pv; ?></td>

                <td class="text-right" colspan="4"></td>
                <td colspan="1">Total Amount = </td>
                <td><?php echo $total_amt; ?> </td>
              </tr>

              <tr class="text-end">
                <td colspan="4"></td>
                <th colspan="2">
                  <form method="post" id="discount_on_point" action="">
                    <div class="d-flex gap-2 align-items-center">
                      <div>
                        <input value="<?php echo isset($_SESSION['voucher_code']) ? $_SESSION['voucher_code'] : null; ?>" placeholder="voucher code" name="voucher_code" id="voucher_code" type="text" class="form-control">
                        <input name="voucher_action" type="hidden">
                      </div>
                      <div>
                        <button class="btn btn-primary btn-sm" type="submit">Redeem</button>
                      </div>
                    </div>
                    <div><?php msg_ssn('vchr'); ?></div>
                  </form>
                </th>
                <th>Discount (-) =</th>
                <th>
                  <?php
                  $vdamt = 0;
                  if (isset($_SESSION['voucher_code'])) {
                    if ($_SESSION['voucher_code'] != "") {
                      $vchr = $vctrl->get_voucher($code = $_SESSION['voucher_code'], $amt = $total_amt);
                      if ($vchr) {
                        $vdamt = $vchr->discount;
                      }
                      if ($total_amt > $vdamt && $vdamt > 0) {
                        $total_amt  = $total_amt - $vdamt;
                      } else {
                        msg_set("Total Amount must be greater than voucher value", 'vchr');
                        $vdamt = 0;
                      }
                    }
                  }
                  echo $vdamt;
                  ?>
                </th>
              </tr>

              <tr class="text-end">
                <td colspan="5"></td>
                <th>
                  <form method="post" id="discount_on_point" action="">
                    <?php echo $point; ?> Point <input <?php if (isset($_SESSION['use_point'])) {
                                                          echo $_SESSION['use_point'] ? "checked" : null;
                                                        } ?> name="use_point" onclick="toggleCheckboxes()" id="checkbox1" type="checkbox">
                    <input name="point_action" type="hidden">
                    <button class="btn btn-primary btn-sm" type="submit">Redeem</button>
                  </form>
                </th>
                <th>Discount (-) =</th>
                <th>
                  <?php
                  // $point = 0;
                  // myprint($cv);
                  $redeem_point = 0;
                  if (isset($_SESSION['use_point'])) {
                    if ($_SESSION['use_point'] === true) {
                      if ($total_amt <= $point) {
                        $redeem_point = $total_amt;
                        $total_amt = 0;
                      } else if ($total_amt > $point && $point > 0) {
                        $total_amt  = $total_amt - $point;
                        $redeem_point = $point;
                      }
                    }
                  }
                  echo $redeem_point; ?>
                </th>
              </tr>

              <tr class="text-end">
                <td colspan="6"></td>
                <th>
                  Net amount =
                </th>
                <th>
                  <?php echo $total_amt; ?>
                </th>
              </tr>
              <tr class="text-end">
                <td colspan="4"></td>
                <td colspan="">Weight = </td>
                <td colspan=""><?php echo $total_gm; ?> gm</td>
                <td colspan="1">Shipping Cost =</td>
                <td><?php $shpcost = calculate_shipping_cost(db: $db, gram: $total_gm, ccode: MY_COUNTRY);
                    //$addrs->country_code;
                    echo  $shpcost; ?>
                </td>
              </tr>
              <tr class="text-end">
                <td colspan="6"></td>
                <th>Final Amount = </th>
                <th><?php echo $total_amt + $shpcost; ?></th>
              </tr>
            </tbody>
          </table>
          <!-- </div> -->
        </div>


        <hr style="padding: 0px 20px; width: 100%; margin-left: -4px; border-top: 1px dotted rgba(0,0,0,0.6); margin-top: 15px; margin-bottom: 15px;">

        <div class="row">
          <div class="col">
            <h5 class="card-title ship_add">Shipping Address
              <span class="ms-2">Please check the shipping address before ordering!</span>
            </h5>
          </div>

        </div>
        <?php 
        import("apps/view/pages/public/inc/checkout-form.php"); 
        ?>

       

      </div>
    </main>


    <?php import("apps/view/inc/footer-credit.php"); ?>
  </div>
</div>

<script>
  function toggleCheckboxes() {
    var checkbox1 = document.getElementById('checkbox1');
    var checkbox2 = document.getElementById('checkbox2');

    // If checkbox1 is checked, uncheck checkbox2 and vice versa
    if (checkbox1.checked === true) {
      checkbox2.checked = true;
    }
    if (checkbox1.checked === false) {
      checkbox2.checked = false;
    }

  }


  var mollieBtn = document.getElementById('mollieBtn');
  var btRadio = document.getElementById('btRadio');

  var btBtn = document.getElementById('btBtn');
  var mollieRadio = document.getElementById('mollieRadio');

  mollieBtn.addEventListener('click', () => {
    mollieRadio.checked = true;
  })
  btBtn.addEventListener('click', () => {
    btRadio.checked = true;
  })
</script>

<?php
$userObj = new Model('pk_user');
$allusers = $userObj->index();
// myprint($allusers);
$arr = null;
$arr['user_group'] = "admin";
$allusers_filtered = $userObj->filter_index($assoc_arr = $arr, $ord = 'DESC', $limit = 9999999, $change_order_by_col = "");
// myprint($allusers_filtered);

$adrObj = new Model('address');
$arr = null;
// $arr['user_id'] = 1;
// $arr['address_type'] = "primary";
// $arr['mobile'] = 39459809;
// $arr['city'] = "New Delhi";
// $adrObj->store($arr);
// $adrObj->update($id=1,$arr);
// $adrObj->destroy($id=1);


// import("apps/view/components/form.php");

import("apps/view/inc/footer.php");
