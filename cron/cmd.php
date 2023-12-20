<?php
// exit;
if (!defined("direct_access")) {
    define("direct_access", 1);
}
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../includes/class-autoload.inc.php");
require_once 'vendor/autoload.php';
import('functions.php');
import('/tcpdf/tcpdf.php');

$lbp = new Label_ctrl;
$lbp->generate_pdf($orderid=9);

exit;

$level = new Member_ctrl;


$data = '[
    {
        "id": 372,
        "ring": 1,
        "username": "winner",
        "is_active": false,
        "pv": 100,
        "tree": [
            {
                "id": 373,
                "ring": 2,
                "username": "alice@123",
                "is_active": false,
                "pv": 100,
                "tree": [
                    {
                        "id": 376,
                        "ring": 3,
                        "username": "okumu1",
                        "is_active": false,
                        "pv": 100,
                        "tree": []
                    }
                ]
            }
        ]
    },
    {
        "id": 312,
        "ring": 1,
        "username": "ssewante",
        "is_active": false,
        "pv": 100,
        "tree": []
    },
    {
        "id": 291,
        "ring": 1,
        "username": "minsa",
        "is_active": false,
        "pv": 100,
        "tree": []
    },
    {
        "id": 277,
        "ring": 1,
        "username": "major",
        "is_active": false,
        "pv": 100,
        "tree": []
    },
    {
        "id": 276,
        "ring": 1,
        "username": "jmuhumuza",
        "is_active": false,
        "pv": 100,
        "tree": []
    },
    {
        "id": 266,
        "ring": 1,
        "username": "wanjy96",
        "is_active": false,
        "pv": 100,
        "tree": []
    },
    {
        "id": 174,
        "ring": 1,
        "username": "pam",
        "is_active": false,
        "pv": 100,
        "tree": []
    },
    {
        "id": 173,
        "ring": 1,
        "username": "gogetter",
        "is_active": false,
        "pv": 100,
        "tree": []
    }
]';

// $totalpv = $level->calculateTotalPV(json_decode($data,true));
$db = new Dbobjects;
// echo $totalpv;
// $level->save_pv_commissions(($db));
$level->save_diamond_commissions($db);
// $tree =  json_encode($tree,JSON_PRETTY_PRINT);
// file_put_contents('tree.json',$tree);
exit;

