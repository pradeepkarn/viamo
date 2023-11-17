<?php
class Withdrawal_ctrl
{
  function redeem_request()
  {
    if (!authenticate()) {
      msg_set('You are not logged in');
      return false;
    }
    if (isset($_POST['my_id'], $_POST['point_out'], $_POST['bank'])) {
      if (intval($_POST['my_id']) != USER['id']) {
        msg_set('You are not authorized to do this activity');
        return false;
      }
      $myid = intval($_POST['my_id']);
      $db = new Dbobjects;
      $old_request = $db->showOne("select id from transactions where trn_type=2 and status='0' and trn_group=3 and transacted_to = '$myid'");
      //  stop and check if any old withdrawal request is pending 
      if ($old_request) {
        msg_set('Your last request is pending let it be completed then you can request again for remaining point');
        return false;
      }
      // start transaction
      $new_req_point = floatval($_POST['point_out']);
      if ($new_req_point < 1) {
        msg_set("Point must be greater than equal to 1");
        return false;
      }
      $pdo = $db->conn;
      $pdo->beginTransaction();
      $member_ctrl = new Member_ctrl;
      // $old_req_point = $member_ctrl->requested_amount($db,$myid);
      // $net_balance_point = $member_ctrl->net_commission($db,$myid);
      $net_balance_point = $member_ctrl->net_balance_minus_requested_balance($db, $myid);
      $point_will_be = $net_balance_point -  $new_req_point;
      if ($point_will_be < 0) {
        msg_set("Point must be lower or equal to than your net point balance. \\n ( Your current balance = $net_balance_point )");
        return false;
      }
      try {
        $db->tableName = 'transactions';
        $arr = null;
        $arr['transacted_to'] = $myid;
        $arr['transacted_by'] = $myid;
        $arr['purchase_amt'] = 0;
        $arr['amount'] = $new_req_point; //request point
        $arr['trn_num'] = uniqid('WTHDR');
        $arr['status'] = 0; //pending
        $arr['trn_group'] = 3; //with drawal
        $arr['trn_type'] = 2; //debit
        $arr['jsn'] = $_POST['bank'];
        $arr['real_amt'] = round(($new_req_point*(5/6)),2);
        $arr['req_for'] = 1; //money conversion
        $db->insertData = $arr;
        $db->create();
        $pdo->commit();
        $arr = null;
        msg_set('Request submitted');
        return true;
      } catch (PDOException $th) {
        echo $th;
        $pdo->rollback();
        return false;
      }
    } else {
      msg_set('Bank details, redeem point are required');
      return false;
    }
  }

  function admin_confirm_request()
  {
    if (!authenticate()) {
      msg_set('You are not logged in');
      return false;
    }
    if (!is_superuser()) {
      msg_set('You are not an admin');
      return false;
    }
    if (isset($_POST['credit_id'])) {
      $id = intval($_POST['credit_id']);
      $db = new Dbobjects;
      $old_request = $db->showOne("select * from transactions where transactions.id = '$id' and trn_type='2' and trn_group='3'");
      //  stop and check if any old withdrawal request is not pending 
      if (!$old_request) {
        msg_set('Request not found');
        return false;
      }
      $pdo = $db->conn;
      $pdo->beginTransaction();
      try {
        $db->tableName = 'transactions';
        $db->pk($id);
        $arr = null;
        $arr['remark'] = isset($_POST['info'])?sanitize_remove_tags($_POST['info']):null;
        $arr['status'] = 1; //approved
        $db->insertData = $arr;
        $db->update();
        $pdo->commit();
        $arr = null;
        msg_set('Request confirmed');
        return true;
      } catch (PDOException $th) {
        $pdo->rollback();
        msg_set('Request failed');
        return false;
      }
    } else {
      msg_set('Invalid request');
      return false;
    }
  }
  function admin_cancel_request()
  {
    if (!authenticate()) {
      msg_set('You are not logged in');
      return false;
    }
    if (!is_superuser()) {
      msg_set('You are not an admin');
      return false;
    }
    if (!isset($_POST['info'])) {
      msg_set('Cancellation reason is required');
      return false;
    }
    if (empty($_POST['info'])) {
      msg_set('Please provide a reason');
      return false;
    }
    if (isset($_POST['credit_id'])) {
      $id = intval($_POST['credit_id']);
      // echo $id;
      $db = new Dbobjects;
      $old_request = $db->showOne("select * from transactions where transactions.id = '$id' and trn_type='2' and trn_group='3'");
      //  stop and check if any old withdrawal request is not pending 
      if (!$old_request) {
        msg_set('Request not found');
        return false;
      }
      
      $pdo = $db->conn;
      $pdo->beginTransaction();
      try {
        $db->tableName = 'transactions';
        $old = $db->pk($id);
        // myprint($old);
        $arr = null;
        $arr['remark'] = sanitize_remove_tags($_POST['info']);
        $arr['status'] = 2; //cancelled
        $db->insertData = $arr;
        $db->update();
        $pdo->commit();
        $arr = null;
        // echo $db->sql;
        msg_set('Request Cancelled');
        return true;
      } catch (PDOException $th) {
        // echo $th;
        $pdo->rollback();
        msg_set('Request failed');
        return false;
      }
    } else {
      msg_set('Invalid request');
      return false;
    }
  }
}
