<?php
class Voucher_ctrl
{
    public $db;
    function __construct()
    {
        $this->db = new Dbobjects;
    }
    function create()
    {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'create-voucher') {
                if (isset($_POST['code'], $_POST['valid_upto'], $_POST['voucher_group'], $_POST['value'])) {
                    $this->db->tableName = "vouchers";

                    $arr['code'] = sanitize_remove_tags($_POST['code']);
                    $code_exist = $this->db->filter($arr);
                    if ($code_exist) {
                        msg_set("code is already exists please choose another");
                        return false;
                    }
                    $arr['created_by'] = USER['id'];
                    if (isset($_POST['always_valid'])) {
                        $arr['always_valid'] = true;
                    } else {
                        if (!isset($_POST['valid_upto'])) {
                            msg_set("Please choose valid upto date");
                            return false;
                        }
                        if (empty($_POST['valid_upto'])) {
                            msg_set("Invalid date");
                            return false;
                        }
                        $arr['always_valid'] = false;
                        $arr['valid_from'] = date('Y-m-d');
                        $arr['valid_upto'] = isset($_POST['valid_upto']) ? $_POST['valid_upto'] : null;
                    }
                    if ($_POST['value']>20) {
                        msg_set("Value must not be greater than");
                        return false;
                    }
                    $arr['value'] = floatval($_POST['value']);
                    $arr['voucher_group'] = $_POST['voucher_group'];
                    try {
                        $this->db->insertData = $arr;
                        $this->db->create();
                        msg_set("Voucher created");
                        return true;
                    } catch (PDOException $th) {
                        // throw $th;
                        msg_set("Voucher not created");
                        return false;
                    }
                } else {
                    msg_set("Missing required field");
                    return false;
                }
            }
        }
    }
    function update($myid, $vid)
    {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'update-voucher') {
                if (isset($_POST['code'], $_POST['valid_upto'], $_POST['voucher_group'], $_POST['value'])) {
                    $old = $this->details($myid, $vid);
                    if (!$old) {
                        msg_set("Voucher not found");
                        return false;
                    }
                    $this->db->tableName = "vouchers";
                    $arr['code'] = sanitize_remove_tags($_POST['code']);
                    if ($old['code'] != $arr['code']) {
                        $code_exist = $this->db->filter($arr);
                        if ($code_exist) {
                            msg_set("code is already exists please choose another");
                            return false;
                        }
                    }
                    if (isset($_POST['always_valid'])) {
                        $arr['always_valid'] = true;
                    } else {
                        if (!isset($_POST['valid_upto'])) {
                            msg_set("Please choose valid upto date");
                            return false;
                        }
                        if (empty($_POST['valid_upto'])) {
                            msg_set("Invalid date");
                            return false;
                        }
                        $arr['always_valid'] = false;
                        $arr['valid_from'] = date('Y-m-d');
                        $arr['valid_upto'] = isset($_POST['valid_upto']) ? $_POST['valid_upto'] : null;
                    }
                    if ($_POST['value']>20) {
                        msg_set("Value must not be greater than 20");
                        return false;
                    }
                    $arr['value'] = floatval($_POST['value']);
                    $arr['voucher_group'] = $_POST['voucher_group'];
                    try {
                        $this->db->insertData = $arr;
                        $this->db->pk($vid);
                        $this->db->update();
                        msg_set("Voucher updated");
                        return true;
                    } catch (PDOException $th) {
                        msg_set("Voucher not updated");
                        return false;
                    }
                } else {
                    msg_set("Missing required field");
                    return false;
                }
            }
        }
    }
    function list($myid)
    {
        return $this->db->show("select * from vouchers where is_active = 1 and created_by = '{$myid}'");
    }
    function details($myid, $vid)
    {
        return $this->db->showOne("select * from vouchers where vouchers.id='{$vid}' and is_active = 1 and created_by = '{$myid}'");
    }
    function get_voucher($code, $amt)
    {
        $v = $this->db->showOne("select * from vouchers where vouchers.code='{$code}' and is_active = 1");
        if ($v) {
            $v =  obj($v);
            if ($v) {
                $discount = 0;
                switch (strval($v->voucher_group)) {
                    case '1':
                        $discount = round(($amt * ($v->value / 100)),);
                        break;
                    case '2':
                        $discount = $v->value;
                        break;
                }
                return (object) array(
                    'id' => $v->id,
                    'discount' => $discount,
                    'code' => $v->code,
                    'value' => $v->value,
                    'voucher_group' => $v->voucher_group,
                    'calculated_on' => $amt
                );
            }
            return null;
        }
    }
    function check_voucher($code)
    {
        return $this->db->showOne("select id,code from vouchers where vouchers.code='{$code}' and is_active = 1");
    }
    function check_other_voucher($myid, $code)
    {
        return $this->db->showOne("select id,code from vouchers where vouchers.code='{$code}' and created_by != '{$myid}' and is_active = 1");
    }
    function check_my_voucher($myid, $code)
    {
        return $this->db->showOne("select id,code from vouchers where vouchers.code='{$code}' and created_by = '{$myid}' and is_active = 1");
    }
}
