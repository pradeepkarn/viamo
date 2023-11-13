<?php
// exit;
if (!defined("direct_access")) {
    define("direct_access", 1);
}
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../includes/class-autoload.inc.php");
require_once 'vendor/autoload.php';
import('functions.php');

$level = new Member_ctrl;
$db =  new Dbobjects;
$pdo = $db->conn;
$arr = null;
$pdo->beginTransaction();
$arr['transactedTo'] = 12345;
$arr['transactedBy'] = 67890;
$arr['amount'] = 100.50;
$arr['trnNum'] = "ABC123";
$arr['status'] = 1; // Active
$arr['trnGroup'] = 1; // PV commission
$arr['trnType'] = 1; // Credit
try {
    $level->save_trn_data($db, $arr);
    $pdo->commit();
} catch (PDOException $th) {
    echo $th;
    $pdo->rollBack();
}
$arr = null;



exit;
$level = new Member_ctrl;
$tree = $level->update_my_level(147);
echo msg_ssn(var:'upgrade_msg',return:true);
// print_r($tree);
exit;
// $tree = $level->my_tree($ref=1, $depth = 1);
// $tree = json_encode($tree,JSON_PRETTY_PRINT);
// file_put_contents('tree.json',$tree);

exit;