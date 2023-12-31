<?php
class Member_ctrl
{
    // public $db;
    public $firstDay;
    public $lastDay;
    public $currentDate;
    function __construct()
    {
        $this->currentDate = new DateTime();
        $this->firstDay = date('Y-m-01 00:00:00');
        $this->lastDay = date('Y-m-t 23:59:59');
    }
    function count_direct_partners($db, $myid)
    {
        $sql = "select COUNT(id) as partner_count from pk_user where ref = '$myid' and is_active=1";
        return $db->showOne($sql)['partner_count'];
    }
    function save_trn_data($db, array $arrdata)
    {
        $obj = obj($arrdata);
        if (
            isset($obj->transactedTo, $obj->transactedBy, $obj->amount, $obj->trnNum, $obj->status, $obj->trnGroup, $obj->trnType)
        ) {
            $real_amt = isset($obj->realAmt) ? $obj->realAmt : 0;
            // $ref_active = $this->check_active($db,$obj->transactedTo);
            $sqlExists = "select id from transactions where trn_num='$obj->trnNum' and trn_group='$obj->trnGroup';";
            $if_exists = $db->showOne($sqlExists);
            $sql = "INSERT INTO transactions (transacted_to, transacted_by, purchase_amt,amount, real_amt,trn_num, status, trn_group, trn_type)
                VALUES ('$obj->transactedTo', '$obj->transactedBy', '$obj->purchase_amt', '$obj->amount', '$real_amt','$obj->trnNum', '$obj->status', '$obj->trnGroup', '$obj->trnType')";
            if (!$if_exists) {
                $db->execSql($sql);
            }
        } else {
            // Handle the case where not all required properties are set
            throw new Exception("Not all required properties are set.");
        }
    }
    function update_level_by_direct_partners_count($db, $myid)
    {
        $current_level = $db->showOne("select member_level as current_level from pk_user where pk_user.id='$myid'");
        $count = $this->count_direct_partners($db, $myid);
        $clv  = 0;
        if ($current_level) {
            $clv = $current_level['current_level'];
        }
        switch (strval($clv)) {
            case '1':
                if ($count >= 20) {
                    if ($db->execSql("update pk_user set member_level = '2' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to super vip";
                        return true;
                    }
                } else {
                    return false;
                }
                break;
            case '2':
                if ($count >= 60) {
                    if ($db->execSql("update pk_user set member_level = '3' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to royal vip";
                        return true;
                    }
                } else {
                    return false;
                }
                break;
            case '3':
                if ($count >= 180) {
                    if ($db->execSql("update pk_user set member_level = '4' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to diamond vip";
                        return true;
                    }
                } else {
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }
    function update_level_by_purchase($db, $myid)
    {
        $current_level = $db->showOne("select member_level as current_level from pk_user where pk_user.id='$myid'");
        $clv  = 0;
        if ($current_level) {
            $clv = $current_level['current_level'];
        }
        switch (strval($clv)) {
            case '0':
                // Upgrade to vip
                // check perosons below me at least three people purchases if yes then update level to 1
                $sql = "SELECT 
                payment.user_id AS buyer_id, pk_user.member_level
                FROM payment 
                JOIN pk_user ON pk_user.id = payment.user_id
                WHERE payment.user_id IN (SELECT pk_user.id FROM pk_user WHERE pk_user.ref='$myid') 
                AND payment.status = 'paid' 
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '') 
                AND payment.updated_at >= '$this->firstDay' 
                AND payment.updated_at <= '$this->lastDay'
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '');";
                $purchases = $db->show($sql);
                $count = count($purchases);
                if ($count >= 3) {
                    // print_r($purchases);
                    if ($db->execSql("update pk_user set member_level = '1' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to vip";
                        return true;
                    }
                }
                $_SESSION['upgrade_msg'][] = "$myid Not upgraded to any level";
                return false;
                break;
            case '1':
                // upgrade to Super VIP
                // check 3 vip members level=1 below level to 2
                // $sql = "SELECT count(id) as vip_count FROM pk_user WHERE pk_user.ref='$myid' and member_level = '{$current_level}';";

                $sql = "SELECT 
                payment.user_id AS buyer_id, pk_user.member_level
                FROM payment 
                JOIN pk_user ON pk_user.id = payment.user_id
                WHERE payment.user_id IN (SELECT pk_user.id FROM pk_user WHERE pk_user.ref='$myid' AND pk_user.member_level='1') 
                AND payment.status = 'paid' 
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '') 
                AND payment.updated_at >= '$this->firstDay' 
                AND payment.updated_at <= '$this->lastDay'
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '');";
                $purchases = $db->show($sql);
                $vip_count = count($purchases);

                // $vip_count = $db->showOne($sql)['vip_count'];
                if ($vip_count >= 3) {
                    if ($db->execSql("update pk_user set member_level = '2' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to super vip";
                        return true;
                    }
                } else {
                    $_SESSION['upgrade_msg'][] = "$myid Not upgraded from vip";
                    return false;
                }
                break;
            case '2':
                // upgrade to Royal VIP
                // check 3 Royal VIP members level=2 below level to 3
                // $sql = "SELECT count(id) as super_vip_count FROM pk_user WHERE pk_user.ref='$myid' and member_level = '{$current_level}';";


                $sql = "SELECT 
                payment.user_id AS buyer_id, pk_user.member_level
                FROM payment 
                JOIN pk_user ON pk_user.id = payment.user_id
                WHERE payment.user_id IN (SELECT pk_user.id FROM pk_user WHERE pk_user.ref='$myid' AND pk_user.member_level='2') 
                AND payment.status = 'paid' 
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '') 
                AND payment.updated_at >= '$this->firstDay' 
                AND payment.updated_at <= '$this->lastDay'
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '');";
                $purchases = $db->show($sql);
                $super_vip_count = count($purchases);
                // $super_vip_count = $db->showOne($sql)['super_vip_count'];
                if ($super_vip_count >= 3) {
                    if ($db->execSql("update pk_user set member_level = '3' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to royal vip";
                        return true;
                    }
                } else {
                    $_SESSION['upgrade_msg'][] = "$myid Not upgraded super vip";
                    return false;
                }
                break;
            case '3':
                // upgrade to diamond VIP
                // check 3 vip members level=3 below level to 4
                // $sql = "SELECT count(id) as royal_vip_count FROM pk_user WHERE pk_user.ref='$myid' and member_level = '{$current_level}';";

                $sql = "SELECT 
                payment.user_id AS buyer_id, pk_user.member_level
                FROM payment 
                JOIN pk_user ON pk_user.id = payment.user_id
                WHERE payment.user_id IN (SELECT pk_user.id FROM pk_user WHERE pk_user.ref='$myid' AND pk_user.member_level='3') 
                AND payment.status = 'paid' 
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '') 
                AND payment.updated_at >= '$this->firstDay' 
                AND payment.updated_at <= '$this->lastDay'
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '');";
                $purchases = $db->show($sql);
                $royal_count = count($purchases);
                // $royal_count = $db->showOne($sql)['royal_vip_count'];

                if ($royal_count >= 3) {
                    if ($db->execSql("update pk_user set member_level = '4' where id = '{$myid}'")) {
                        $_SESSION['upgrade_msg'][] = "$myid Upgraded to diamond vip";
                        return true;
                    }
                } else {
                    $_SESSION['upgrade_msg'][] = "$myid Not upgrade from royal";
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }
    function order_details($db, $user_id)
    {
        $sql_sum = "SELECT SUM(amount) AS purchase, SUM(pv) AS total_pv FROM payment WHERE user_id = '$user_id' AND point_used='0' AND status = 'paid' AND (invoice IS NOT NULL AND invoice <> '') AND updated_at >= '$this->firstDay' AND updated_at <= '$this->lastDay';";
        return $db->show($sql_sum)[0];
    }
    function check_active($db, $user_id)
    {
        $days = 31;
        $today = date('Y-m-d H:i:s');
        $sql = "SELECT pv, ($days-DATEDIFF('$today', created_at)) as days_left 
        FROM payment 
        WHERE user_id = $user_id 
        AND pv >= 15
        AND DATEDIFF('$today', created_at) <= $days 
        AND status = 'paid' 
        AND (invoice IS NOT NULL AND invoice <> '')
        ORDER BY created_at DESC
        LIMIT 1;";
        $data = $db->show($sql);
        if (count($data)) {
            return true;
        } else {
            return false;
        }
    }
    function check_ever_active($db, $user_id)
    {
        $today = date('Y-m-d H:i:s');
        $sql = "SELECT pv, (DATEDIFF('$today', created_at)) as days_left 
        FROM payment 
        WHERE user_id = $user_id 
        AND pv >= 15
        AND status = 'paid' 
        AND (invoice IS NOT NULL AND invoice <> '')
        ORDER BY created_at DESC
        LIMIT 1;";
        $data = $db->show($sql);
        if (count($data)) {
            return true;
        } else {
            return false;
        }
    }
    function save_pv_commissions($db)
    {
        $db->conn->beginTransaction();
        $sql = "select id,member_level,ref from pk_user where is_active=1;";
        $users = $db->show($sql);
        try {
            foreach ($users as $u) {
                $u = obj($u);
                $commission = 0;
                $pv_percentage = 0;
                if ($this->check_active($db, $u->id)) {
                    $trees = $this->my_tree($db, $ref = $u->id, $depth = 1);
                    $totalpv = $this->calculateTotalPV($trees);
                    switch (strval($u->member_level)) {
                        case '2':
                            $pv_percentage = 5;
                            $commission = round($totalpv * 0.05, 2);
                            break;
                        case '3':
                            $pv_percentage = 7.5;
                            $commission = round($totalpv * 0.075, 2);
                            break;
                        case '4':
                            $pv_percentage = 10;
                            $commission = round($totalpv * 0.10, 2);
                            break;
                        default:
                            $pv_percentage = 0;
                            $commission = 0;
                            break;
                    }
                    $db->tableName = "transactions";
                    $arr = null;
                    $arr['transacted_to'] = $u->id;
                    $arr['transacted_by'] = $u->id;
                    $arr['from_date'] = $this->firstDay;
                    $arr['to_date'] = $this->lastDay;
                    $arr['trn_group'] = 4; //team commission
                    $already = $db->get($arr);
                    $arr['trn_num'] = uniqid('team');
                    $arr['purchase_amt'] = 0;
                    $arr['trn_type'] = 1; //1: credit, 2: debit
                    $arr['status'] = 1; //1: Active
                    $arr['ref'] = $u->ref; //reference
                    if (!$already && $totalpv > 0 && $commission > 0) {
                        $arr['amount'] = $commission;
                        $arr['team_pv_sum'] = $totalpv;
                        $arr['team_pv_percentage'] = $pv_percentage;
                        $arr['member_level'] = $u->member_level;
                        $db->insertData = $arr;
                        $arr = null;
                        $db->create();
                    }
                }
            }
            $db->conn->commit();
        } catch (PDOException $th) {
            // echo $th;
            $db->conn->rollback();
        }
    }
    function save_diamond_commissions($db)
    {
        $db->conn->beginTransaction();
        $sql = "select id,member_level,ref from pk_user where member_level=4 and is_active=1;";
        $users = $db->show($sql);
        try {
            foreach ($users as $u) {
                $u = obj($u);
                $commission = 0;
                $pv_percentage = 0;
                if ($this->check_active($db, $u->id)) {
                    $commission = $this->my_diamond_bonus($db, $u->id);
                    $db->tableName = "transactions";
                    $arr = null;
                    $arr['transacted_to'] = $u->id;
                    $arr['transacted_by'] = $u->id;
                    $arr['from_date'] = $this->firstDay;
                    $arr['to_date'] = $this->lastDay;
                    $arr['trn_group'] = 1; //diamond commissions
                    $already = $db->get($arr);
                    $arr['trn_num'] = uniqid('team');
                    $arr['purchase_amt'] = 0;
                    $arr['trn_type'] = 1; //1: credit, 2: debit
                    $arr['status'] = 1; //1: Active
                    $arr['ref'] = $u->ref; //reference
                    if (!$already && $commission > 0) {
                        $arr['amount'] = $commission;
                        $arr['team_pv_sum'] = 0;
                        $arr['team_pv_percentage'] = 0;
                        $arr['member_level'] = $u->member_level;
                        $db->insertData = $arr;
                        $arr = null;
                        $db->create();
                    }
                }
            }
            $db->conn->commit();
        } catch (PDOException $th) {
            echo $th;
            $db->conn->rollback();
        }
    }
    function my_tree($db, $ref, $depth = 1)
    {
        $prtdata = array();
        // $db = new Dbobjects;
        $sql = "select pk_user.id, pk_user.username, pk_user.image, pk_user.ref from pk_user where pk_user.ref = '$ref' and pk_user.ref != 0 order by pk_user.id desc";
        $data = $db->show($sql);

        foreach ($data as $p) {
            $od = $this->order_details($db, $user_id = $p['id']);
            $prtdata[] = array(
                'id' => $p['id'],
                'ring' => $depth,
                'username' => $p['username'],
                'is_active' => $this->check_active($db, $user_id = $p['id']),
                'pv' => $od['total_pv'] ? round($od['total_pv'], 2) : 0,
                'tree' => $this->my_tree($db, $p['id'], $depth + 1)
            );
        }

        return $prtdata;
    }
    function my_diamond_bonus($db, $myid)
    {
        $diamond_cmsn = 0;
        $sql = "select SUM(amount) as t_amt from transactions where trn_group='4' and ref='$myid' and member_level = '4'";
        $amt = $db->showOne($sql);
        if ($amt['t_amt']) {
            $diamond_cmsn = round(($amt['t_amt'] * 0.20), 2);
        }
        $sql = "SELECT SUM(amount) as t_amt
        FROM transactions 
        WHERE ref IN (SELECT transacted_to FROM transactions WHERE trn_group='4' AND ref = '$myid') 
        AND member_level = '4';
        ";
        $amtpartners = $db->showOne($sql);
        if ($amtpartners['t_amt']) {
            $diamond_cmsn += round((($amtpartners['t_amt'] * 0.20) * 0.20), 2);
        }
        return $diamond_cmsn;
    }

    function structure_tree(array $data)
    {
        // $sql = "select id, ref from pk_user where pk_user.id='{$myid}'";
        $output = null;

        foreach ($data as $item) {
            $mmbrcnt = count($item['tree']);
            $text_muted = $mmbrcnt == 0 ? 'text-muted' : 'text-bold has-members';
            $partners = $mmbrcnt > 1 ? 'partners' : 'partner';
            $output .= '<li>';
            $output .= "<span class='caret $text_muted'>" . $item['username'] . " - (" . count($item['tree']) . " $partners)</span>";

            if (!empty($item['tree'])) {
                $output .= '<ul class="nested">';
                $output .= $this->structure_tree($item['tree']);
                $output .= '</ul>';
            }

            $output .= '</li>';
        }
        return $output;
    }
    function calculateTotalPV($data)
    {
        $totalPV = 0;

        foreach ($data as $item) {
            $totalPV += $item['pv'];

            if (isset($item['tree']) && !empty($item['tree'])) {
                // If the user has a tree, recursively calculate the total PV for the tree
                $totalPV += $this->calculateTotalPV($item['tree']);
            }
        }

        return $totalPV;
    }
    function calculateTotalPVForDiamond($data)
    {
        $totalPV = 0;

        foreach ($data as $item) {
            $totalPV += ($item['pv'] * 0.02);
            if (isset($item['tree']) && !empty($item['tree'])) {
                // If the user has a tree, recursively calculate the total PV for the tree
                $totalPV += $this->calculateTotalPV($item['tree']);
            }
        }

        return $totalPV;
    }
    function bonus_sum_cr($db, $myid)
    {
        $trn_group = '2'; //direct bonus
        $trn_type = '1'; //credit amt
        $status = '1'; //active
        $sql = "
        select SUM(amount) as total_db from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type'
        AND status='$status'
        ";
        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }

    function bonus_sum_last_month($db)
    {
        $trn_group = '2'; // direct bonus
        $trn_type = '1'; // credit amt
        $status = '1'; // active

        // Calculate last month's start and end dates
        $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
        $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));

        $sql = "
        SELECT SUM(amount) AS total_db 
        FROM transactions 
        WHERE trn_group = '$trn_group'
        AND trn_type = '$trn_type'
        AND status = '$status'
        AND created_at >= '$lastMonthStart' AND created_at <= '$lastMonthEnd'
    ";

        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }

    function bonus_sum_current_month($db)
    {
        $trn_group = '2'; // direct bonus
        $trn_type = '1'; // credit amt
        $status = '1'; // active

        // Calculate current month's start and end dates
        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');

        $sql = "
        SELECT SUM(amount) AS total_db 
        FROM transactions 
        WHERE trn_group = '$trn_group'
        AND trn_type = '$trn_type'
        AND status = '$status'
        AND created_at >= '$currentMonthStart' AND created_at <= '$currentMonthEnd'
    ";

        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }

    function team_pv_sum_cr($db, $myid)
    {
        $trn_group = '4'; //Team commossions
        $trn_type = '1'; //credit amt
        $status = '1'; //active
        $sql = "
        select SUM(amount) as total_db from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type'
        AND status='$status'
        ";
        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }
    // amount debited
    function debited_amount($db, $myid)
    {
        $trn_group = '3'; //withdrawal request
        $trn_type = '2'; //debit
        $status = '1'; //active/apporved
        $sql = "
        select SUM(amount) as total_db from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type'
        AND status='$status'
        ";
        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }
    function product_purchase_amount($db, $myid)
    {
        $trn_group = '5'; //withdrawal request
        $trn_type = '2'; //debit
        $status = '1'; //active/apporved
        $sql = "
        select SUM(amount) as total_db from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type'
        AND status='$status'
        ";
        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }
    function requested_amount($db, $myid)
    {
        $trn_group = '3'; //withdrawal request
        $trn_type = '2'; //debit
        $status = '0'; //active/apporved
        $sql = "
        select SUM(amount) as total_db from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type'
        AND status='$status'
        ";
        $cmsn = $db->showOne($sql)['total_db'];
        return $cmsn ? round($cmsn, 2) : 0;
    }
    // Lifetime earning / gross amount
    function lifetime_commission($db, $myid)
    {
        // life time = bonus sum + team pv sum
        return round(($this->bonus_sum_cr($db, $myid) + $this->team_pv_sum_cr($db, $myid)), 2);
    }
    function net_commission($db, $myid)
    {
        // net income = bonus sum + team pv sum - debited amount
        return round(($this->bonus_sum_cr($db, $myid) + $this->team_pv_sum_cr($db, $myid) - $this->debited_amount($db, $myid) - $this->product_purchase_amount($db, $myid)), 2);
    }
    function net_balance_minus_requested_balance($db, $myid)
    {
        $old_req_point = $this->requested_amount($db, $myid);
        $net_balance_point = $this->net_commission($db, $myid);
        return $net_balance_point - $old_req_point;
    }
    function withdrawal_request_list_extended($db, $myid, $req, $data_limit = 5)
    {
        $trn_group = '3'; //withdrawal request
        $trn_type = '2'; //debited
        $sql = "
        select * from transactions 
        where transacted_to='$myid' 
        AND trn_type='$trn_type'
        ";
        $current_page = 0;
        $data_limit = $data_limit;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $tp = count($db->show($sql));
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        $q = null;
        if (isset($req->q)) {
            $q = $req->q;
        }
        $trn_group = '3'; //withdrawal request
        $trn_type = '2'; //debit amt
        $sql = "
        select * from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type' ORDER BY id LIMIT $page_limit
        ";
        $commissions = $db->show($sql);
        return (object) array(
            'req' => obj($req),
            'total_cmsn' => $tp,
            'current_page' => $cp,
            'commissions' => $commissions
        );
    }
    function my_realtime_bonus_list_extended($db, $myid, $req, $data_limit = 5)
    {
        $trn_group = '2'; //direct bonus
        $trn_type = '1'; //credited
        $sql = "
        select * from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type'
        ";
        $current_page = 0;
        $data_limit = $data_limit;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $tp = count($db->show($sql));
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        $q = null;
        if (isset($req->q)) {
            $q = $req->q;
        }
        $trn_group = '2'; //direct bonus
        $trn_type = '1'; //credited
        $sql = "
        select * from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type' ORDER BY id DESC LIMIT $page_limit
        ";
        $commissions = $db->show($sql);
        return (object) array(
            'req' => obj($req),
            'total_cmsn' => $tp,
            'current_page' => $cp,
            'commissions' => $commissions
        );
    }
    function withdrawal_request_list($db, $myid)
    {
        // $trn_group = '3'; //with drwala request
        $trn_type = '2'; //debited
        $sql = "
        select * from transactions 
        where transacted_to='$myid' 
        AND trn_type='$trn_type' ORDER BY id DESC
        ";
        return $db->show($sql);
    }
    function bonus_list($db, $myid)
    {
        $trn_group = '2'; //with drwala request
        $trn_type = '1'; //credited
        $sql = "
        select * from transactions 
        where transacted_to='$myid' 
        AND trn_group='$trn_group'
        AND trn_type='$trn_type' ORDER BY id DESC
        ";
        return $db->show($sql);
    }
    function all_withdrawal_request_list($db)
    {
        $trn_group = '3'; //with drwala request
        $trn_type = '2'; //debited
        $sql = "
        select * from transactions 
        WHERE trn_type='$trn_type' 
        AND status = '0'
        ORDER BY id DESC
        ";
        return $db->show($sql);
    }
    function all_withdrawal_confirm_list($db)
    {
        $trn_group = '3'; //with drwala request
        $trn_type = '2'; //debited
        $sql = "
        select * from transactions 
        where trn_group='$trn_group'
        AND trn_type='$trn_type' 
        AND status = '1'
        ORDER BY id DESC
        ";
        return $db->show($sql);
    }
    function all_withdrawal_cancelled_list($db)
    {
        $trn_group = '3'; //with drwala request
        $trn_type = '2'; //debited
        $sql = "
        select * from transactions 
        where trn_group='$trn_group'
        AND trn_type='$trn_type' 
        AND status = '2'
        ORDER BY id DESC
        ";
        return $db->show($sql);
    }
    function top_members($db, $myid)
    {
        $sql = "WITH RankedPayments AS (
            SELECT
                pk_user.username,
                payment.pv,
                payment.user_id,
                ROW_NUMBER() OVER (PARTITION BY pk_user.username ORDER BY payment.pv DESC) AS rnk
            FROM
                payment
                JOIN pk_user ON payment.user_id = pk_user.id
            WHERE
                payment.user_id IN (SELECT id FROM pk_user WHERE pk_user.ref = '$myid')
        )
        SELECT
            username,
            pv,
            user_id
        FROM
            RankedPayments
        WHERE
                rnk = 1
            ORDER BY
                pv DESC;
        ";
        return $db->show($sql);
    }
}
