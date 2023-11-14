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
$db = $level->db;
// echo $level->count_direct_partners($db,$myid=1);
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