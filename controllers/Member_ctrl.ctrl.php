<?php
class Member_ctrl
{
    public $db;
    public $firstDay;
    public $lastDay;
    public $currentDate;
    function __construct()
    {
        $this->currentDate = new DateTime();
        $this->db = new Dbobjects;
        $this->firstDay = date('Y-m-01 00:00:00');
        $this->lastDay = date('Y-m-t 23:59:59');
    }
    // function check_my_member_orders($myid)
    // {
    //     $sql = "SELECT 
    //         DISTINCT payment.user_id AS buyer_id
    //         FROM payment 
    //         WHERE payment.user_id IN (SELECT pk_user.id FROM pk_user WHERE pk_user.ref='$myid') 
    //         AND payment.status = 'paid' 
    //         AND (payment.invoice IS NOT NULL AND payment.invoice <> '') 
    //         AND payment.updated_at >= '$this->firstDay' 
    //         AND payment.updated_at <= '$this->lastDay'
    //         ORDER BY buyer_id;";
    //     return $this->db->show($sql);
    // }
    function save_trn_data(object $db, array $arrdata)
    {
        $obj = obj($arrdata);
        // $obj->transactedTo = 12345;
        // $obj->transactedBy = 67890;
        // $obj->amount = 100.50;
        // $obj->trnNum = "ABC123";
        // $obj->status = 1; // Active
        // $obj->trnGroup = 1; // PV commission
        // $obj->trnType = 1; // Credit
        $sql = "INSERT INTO transactions (transacted_to, transacted_by, amount, trn_num, status, trn_group, trn_type)
        VALUES ('$obj->transactedTo', '$obj->transactedBy', '$obj->amount', '$obj->trnNum', '$obj->status', '$obj->trnGroup', '$obj->trnType')";
        if($this->db->execSql($sql)){
            return true;
        }
        return false;
    }
    function update_my_level($myid)
    {
        $current_level = $this->db->showOne("select member_level as current_level from pk_user where pk_user.id=$myid")['current_level'];
        switch (strval($current_level)) {
            case '0':
                // Upgrade to vip
                // check perosons below me at least three people purchases if yes then update level to 1
                $sql = "SELECT 
                payment.user_id AS buyer_id, pk_user.member_level
                FROM payment 
                JOIN pk_user ON pk_user.id = payment.user_id
                WHERE payment.user_id IN (SELECT pk_user.id FROM pk_user WHERE pk_user.ref='$myid') 
                AND payment.status = 'paid' 
                AND (payment.invoice IS NOT NULL AND payment.invoice <> '');";
                $purchases = $this->db->show($sql);
                $count = count($purchases);
                if ($count >= 3) {
                    // print_r($purchases);
                    if ($this->db->execSql("update pk_user set member_level = '1' where id = '{$myid}'")) {
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
                $sql = "SELECT count(id) as vip_count FROM pk_user WHERE pk_user.ref='$myid' and member_level = '{$current_level}';";
                $vip_count = $this->db->showOne($sql)['vip_count'];
                if ($vip_count >= 3) {
                    if ($this->db->execSql("update pk user set member_level = '2' where id = '{$myid}'")) {
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
                $sql = "SELECT count(id) as super_vip_count FROM pk_user WHERE pk_user.ref='$myid' and member_level = '{$current_level}';";
                $super_vip_count = $this->db->showOne($sql)['super_vip_count'];
                if ($super_vip_count >= 3) {
                    if ($this->db->execSql("update pk user set member_level = '3' where id = '{$myid}'")) {
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
                $sql = "SELECT count(id) as royal_vip_count FROM pk_user WHERE pk_user.ref='$myid' and member_level = '{$current_level}';";
                $royal_count = $this->db->showOne($sql)['royal_vip_count'];
                if ($royal_count >= 3) {
                    if ($this->db->execSql("update pk user set member_level = '4' where id = '{$myid}'")) {
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


    function order_details($user_id = null, $db = new Dbobjects)
    {
        $sql_sum = "SELECT SUM(amount) AS purchase, SUM(pv) AS total_pv, SUM(rv) AS total_rv FROM payment WHERE user_id = $user_id AND status = 'paid' AND (invoice IS NOT NULL AND invoice <> '') AND updated_at >= '$this->firstDay' AND updated_at <= '$this->lastDay';";
        return $db->show($sql_sum)[0];
    }
    function check_active($user_id)
    {
        $today = date('Y-m-d H:i:s');
        $sql = "SELECT pv, (33-DATEDIFF('$today', created_at)) as days_left 
        FROM payment 
        WHERE user_id = $user_id 
        AND pv >= 15 
        AND DATEDIFF('$today', created_at) <= 33 
        AND status = 'paid' 
        AND (invoice IS NOT NULL AND invoice <> '')
        ORDER BY created_at DESC
        LIMIT 1;";
        $data = $this->db->show($sql);
        if (count($data)) {
            return true;
        } else {
            return false;
        }
    }
    function check_ever_active($user_id)
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
        $data = $this->db->show($sql);
        if (count($data)) {
            return true;
        } else {
            return false;
        }
    }
    function my_tree($ref, $depth = 1)
    {
        if ($depth > 10) {
            return []; // Return an empty array if the maximum depth is reached
        }

        $prtdata = array();
        // $this->db = new Dbobjects;
        $sql = "select pk_user.id, pk_user.username, pk_user.image, pk_user.ref from pk_user where pk_user.ref = $ref and pk_user.ref != 0 order by pk_user.id desc";
        $data = $this->db->show($sql);

        foreach ($data as $p) {
            $od = $this->order_details($user_id = $p['id'], $this->db);
            $prtdata[] = array(
                'id' => $p['id'],
                'ring' => $depth,
                'username' => $p['username'],
                'is_active' => $this->check_active($user_id = $p['id']),
                'pv' => $od['total_pv'] ? round($od['total_pv'], 2) : 0,
                'rv' => $od['total_rv'] ? round($od['total_rv'], 2) : 0,
                'tree' => $this->my_tree($p['id'], $depth + 1)
            );
        }

        return $prtdata;
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
}
