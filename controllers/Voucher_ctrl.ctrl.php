<?php
class Voucher_ctrl
{
    function create()
    {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'create-voucher') {
                if (isset($_POST['code'], $_POST['valid_upto'], $_POST['voucher_group'], $_POST['value'])) {
                    $db = new Dbobjects;
                    $db->tableName = "vouchers";

                    $arr['code'] = sanitize_remove_tags($_POST['code']);
                    $code_exist = $db->filter($arr);
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
                        $arr['valid_from'] = date('Y-m-d');
                        $arr['valid_upto'] = isset($_POST['valid_upto']) ? $_POST['valid_upto'] : null;
                    }
                    $arr['value'] = floatval($_POST['value']);
                    $arr['voucher_group'] = $_POST['voucher_group'];
                    try {
                        $db->insertData = $arr;
                        $db->create();
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
}
