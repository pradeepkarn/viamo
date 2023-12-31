<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
$pvctrl = new Pv_ctrl;
$pvctrl->db = new Dbobjects;


################## new ##########
// $db = new Dbobjects;
// $level = new Member_ctrl;
// $level->update_level_by_direct_partners_count($db, $myid=USER['id']);
// $level->update_level_by_purchase($db, $myid=USER['id']);


$db = new Dbobjects;
$users = $db->show("select * from pk_user where is_active = 1");
$level = new Member_ctrl;
foreach ($users as $key => $u) {
  $level->update_level_by_direct_partners_count($db, $myid = $u['id']);
  $level->update_level_by_purchase($db, $myid = $u['id']);
}
################# new #################
$mmbr = new Member_ctrl;
$mypartners = $mmbr->top_members($db, $myid = USER['id']);


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
              <marquee class="marque_news mt-2" behavior="scroll" scrollamount="8" direction="left">
                Herzlich Willkommen in der VIAMO-Community. Hier ist die Aufzeichnung des Webinares zum Start der PRE-Launch-Phase:
                <a href="https://youtu.be/RPIdb7dM9Ag">https://youtu.be/RPIdb7dM9Ag</a>
              </marquee>
              <iframe width="100%" height="315" src="https://www.youtube.com/embed/RPIdb7dM9Ag" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>
        </div>

        <section>
          <div class="container px-4">
            <div class="row justify-content-center">
              <?php foreach ($mypartners as $key => $mbr) {
                $mbr = obj($mbr);
              ?>
                <div class="col-4 mb-2">
                  <div class="card text-center">
                    <div class="card-body">
                      <h5 class="card-title text-center pb-3"><?php echo $mbr->username; ?></h5>
                      <h6 class="card-subtitle mb-2 text-body-secondary">PV: <?php echo $mbr->pv; ?></h6>
                    </div>
                  </div>
                </div>
              <?php } ?>
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
                      <div class="col-md-12 col-12">
                       

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

                        <div class="row">
                          <div class="col-md">
                          <label class="control-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">To partner</font>
                          </font>
                        </label>
                            <div class="input-group copyarea">
                              <input type="text" id="p1" class="form-control" value="<?php echo BASE_URI; ?>/signup/?sponserid=<?php echo $my_username; ?>" readonly="">
                              <div class="input-group-append">
                                <span onclick="copyToClipboard('#p1')" class="input-group-text1 pointer"><i class="far fa-copy"></i></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md">
                          <label class="control-label">
                          <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">To Viamo World</font>
                          </font>
                        </label>
                            <div class="input-group copyarea">
                              <input id="vworld" type="text" class="form-control" value="https://viamo.world/shop/?sponserid=<?php echo $my_username; ?>" readonly="">
                              <div class="input-group-append">
                                <span onclick="copyToClipboard('#vworld')" class="input-group-text1 pointer"><i class="far fa-copy"></i></span>
                              </div>
                            </div>
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
            <!-- <div class="row mt-3">
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
            </div> -->



          </div>

        </section>
      </div>

    </main>
    <?php import("apps/view/inc/footer-credit.php"); ?>
  </div>
</div>

<?php

import("apps/view/inc/footer.php");
