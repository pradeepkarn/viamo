<form class="material-form" id="place-item-form" action="/<?php echo home; ?>/public/place-order-ajax" method="POST">
          <div class="row mb-3">
            <div class="col-2">
              <div class="nav_tabs1">
                <button id="mollieBtn" type="button" class="btn tablinks2" onclick="openPayment(event, 'mollie')"><img style="height: 20px; width:auto;" src="/<?php echo home; ?>/media/img/mollie.png" width="100px" alt="" srcset=""> mollie</button>
                <button id="btBtn" type="button" class="btn tablinks2" onclick="openPayment(event, 'transfer')"><i class="fa-sharp fa-light fa-money-check"></i> Bank transfer (Prepay)</button>
              </div>
            </div>

            <div class="col-5">
              <div id="mollie" class="tabcontent2">
                <input checked id="mollieRadio" type="radio" name="payment_mode" value="mollie">
                <img style="height: 50px; width:auto;" src="/<?php echo home; ?>/media/img/mollie.png" width="100px" alt="" srcset="">
                <p class="mollie_cl mt-2">Online payment system<br> credit cards, Sofort / Klarna (payment via invoice), EPS, and much more.</p>
              </div>
              <div id="transfer" class="tabcontent2">
                <input id="btRadio" type="radio" name="payment_mode" value="Bank_transfer">
                <?php
                $banklist = get_banks_by_country();
                echo count($banklist['banks']) ? "{$banklist['banks'][0]}" : null;
                ?>
              </div>

            </div>
          </div>
          <input style="display: none;" <?php if (isset($_SESSION['use_point'])) {
                                          echo $_SESSION['use_point'] ? "checked" : null;
                                        } ?> id="checkbox2" name="redeem_point" type="checkbox">
          <input type="hidden" name="point" value="<?php echo $redeem_point; ?>">
          <input type="hidden" name="vdamt" value="<?php echo $vdamt; ?>">
          <input type="hidden" name="total_amount" value="<?php echo $total_amt; ?>">
          <input type="hidden" name="total_gm" value="<?php echo $total_gm; ?>">
          <input type="hidden" name="shipping_cost" value="<?php echo $shpcost; ?>">

          <input type="hidden" name="pv" value="<?php if (isset($pv)) echo $pv ?>">
        </form>
        <div class="row mb-3">
          <div class="col-6">
            <div id="check"></div>
            <button id="placeord_btn" class="col-md-3 btn btn-info">Make Payment</button>
          </div>
          <div id="spinn" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <?php
          pkAjax_form("#placeord_btn", "#place-item-form", "#check");
          ajaxActive('#spinn');
          ajaxDeactive("#placeord_btn");
          ?>

          <p class="again_payment">IF THERE IS AN ERROR IN THE FOLLOWING WINDOW, PLEASE CLICK "MAKE PAYMENT" AGAIN.</p>
        </div>