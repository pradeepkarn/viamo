<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
$pvctrl = new Pv_ctrl;
$pvctrl->db = new Dbobjects;
$act_data = $pvctrl->am_i_active($_SESSION['user_id']);
$tree = $pvctrl->my_tree($_SESSION['user_id'], 1);
// print_r($act_data);
$im_act = $act_data['active'];
$tree_data = json_encode($tree);
echo <<<SCRPT
  <script>
    let treeData = {}
    treeData = $tree_data
  </script>
SCRPT;
?>
<div id="layoutSidenav">
  <?php import("apps/view/inc/sidebar.php"); ?>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4 mypop">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <div class="container px-4 ms-2">
          <div class="row">
            <div class="col bst_news">
              <marquee class="marque_news mt-2" behavior="scroll" scrollamount="8" direction="right">Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel maiores eos alias eius rerum qui expedita, architecto, quasi quae consequatur tenetur delectus perferendis numquam. Dolor ad laboriosam exercitationem expedita. Excepturi.</marquee>
            </div>
          </div>
        </div>

        <section>
          <div class="container px-4">
            <div class="row justify-content-center">
              <div class="col-4 mb-2">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-center pb-3">Member 1</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
                    <p class="card-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Mollitia reiciendis animi quasi eos ex ratione deleniti doloremque labore vero. Harum labore velit cum fugiat incidunt temporibus ipsa sunt! Rem, est.</p>
                  </div>
                </div>
              </div>
              <div class="col-4 mb-2">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-center pb-3">Member 2</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
                    <p class="card-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Mollitia reiciendis animi quasi eos ex ratione deleniti doloremque labore vero. Harum labore velit cum fugiat incidunt temporibus ipsa sunt! Rem, est.</p>
                  </div>
                </div>
              </div>
              <div class="col-4 mb-2">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-center pb-3">Member 3</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
                    <p class="card-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Mollitia reiciendis animi quasi eos ex ratione deleniti doloremque labore vero. Harum labore velit cum fugiat incidunt temporibus ipsa sunt! Rem, est.</p>
                  </div>
                </div>
              </div>
              <div class="col-4 mb-2">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title text-center pb-3">Total Members</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
                    <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus officia nulla ullam. Laboriosam, tempore. Cumque modi hic quae, veniam numquam, animi laboriosam vitae necessitatibus quas quod sint ad atque sit!</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section>
          <div class="container-fluid px-4">
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">
                      <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Referral Links</font>
                      </font>
                    </h4>
                    <hr>


                    <div class="form-group row">
                      <div class="col-6">
                        <label class="control-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">To partner</font>
                          </font>
                        </label>

                        <?php
                        $my_username = null;

                        if (isset($_SESSION['user_id'])) {
                          $userid = $_SESSION['user_id'];
                          $invite = getData("pk_user", $userid);
                          $my_username = $invite != false ? $invite['username'] : null;
                        }
                        ?>

                        <?php
                        if (authenticate() == true) {
                          // $acnt = new Account;
                          // $user = $acnt->getLoggedInAccount();


                          // echo js_alert(days_left());

                          $userObj = new Model('pk_user');

                          $arr = null;
                          $arr['ref'] = $_SESSION['user_id'];
                          $partner = $userObj->filter_index($assoc_arr = $arr, $ord = 'DESC', $limit = 999,              $change_order_by_col = "");
                        }


                        ?>


                        <div class="input-group copyarea">
                          <input type="text" class="form-control" value="https:/<?php echo home; ?>/signup/?sponserid=<?php echo $my_username; ?>" readonly="">
                          <div class="input-group-append">
                            <span onclick="copyToClipboard('#p1')" class="input-group-text1 pointer"><i class="far fa-copy"></i></span>
                          </div>
                        </div>


                      </div>
                      <!-- <div class="col-6">
                        <label class="control-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">To customers</font>
                          </font>
                        </label>
                        <div class="input-group copyarea">
                          <input type="text" class="form-control" value="https://domswiss.at?sponsor=jyoti" readonly="">
                          <div class="input-group-append">
                            <span onclick="copyToClipboard('#p2')" class="input-group-text1"><i class="far fa-copy pointer"></i></span>
                          </div>
                        </div>
                      </div> -->
                    </div>




                    <script>
                      $('.copyarea').click(function() {
                        var copyText = $(this).children('input');
                        copyText.select();
                        document.execCommand("copy");
                        $.toast({
                          heading: 'Erledigt!',
                          text: 'Sie haben den Link erfolgreich in die Zwischenablage kopiert!',
                          position: 'top-right',
                          loaderBg: '#00ab81',
                          icon: 'success',
                          hideAfter: 3500,
                          stack: 6
                        });
                      });

                      function copyToClipboard(element) {
                        var $temp = $("<input>");
                        $("body").append($temp);
                        $temp.val($(element).text()).select();
                        document.execCommand("copy");
                        $temp.remove();
                      }
                      // $('.copyarea').click(function() {
                      //   var copyText = $(this).children('input').get(0);
                      //   navigator.clipboard.writeText(copyText.value)
                      //     .then(function() {
                      //       $.toast({
                      //         heading: 'Erledigt!',
                      //         text: 'Link copied!',
                      //         position: 'top-right',
                      //         loaderBg: '#00ab81',
                      //         icon: 'success',
                      //         hideAfter: 3500,
                      //         stack: 6
                      //       });
                      //     })
                      //     .catch(function(error) {
                      //       console.error('Failed to copy: ', error);
                      //     });
                      // });
                    </script>

                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col">
                <h5><b>Active Days Left: </b>
                  <?php

                  if ($im_act == true) {
                    echo $act_data['data']['days_left'] . " active days left";
                  } else {
                    echo "Your account was expired";
                  }

                  ?></h5>
              </div>
            </div>



          </div>

        </section>
      </div>



    </main>



    <?php import("apps/view/inc/footer-credit.php"); ?>
  </div>
</div>



<?php

import("apps/view/inc/footer.php");
