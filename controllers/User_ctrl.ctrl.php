<?php
class User_ctrl
{
    protected $db;
    public function __construct()
    {
        $this->db = new Dbobjects;
    }
    function store_pv()
    {
        // $sql = "SELECT id from pk_user where is_active = 1";
        // $data = $this->db->show($sql);
        // $count = 0;
        // foreach ($data as $key => $u) {
        //     $am_i_active = $this->am_i_active($u['id'])['active'];
        //     $pvctrl = new Pv_ctrl;
        //     $tree = $pvctrl->my_tree($u['id'], 1, $am_i_active);
        //     $cmsn = $pvctrl->calculate_sum($tree, 1, 10);
        //     $remark = $am_i_active==true?'active':'inactive';
        //     $fund_sql = "select id from my_fund where user_id = {$u['id']} AND  date_from = '{$pvctrl->firstDay}' AND  date_to = '{$pvctrl->lastDay}' AND content_group = 'pv'";
        //     $old_fund = $this->db->show($fund_sql);
        //     if (count($old_fund)>=1) {
        //         $of = $old_fund[0];
        //         $fund_sql = "update my_fund set amt = {$cmsn['pv_sum']} , remark = '$remark' where id = {$of['id']}";
        //     }else{
        //         $fund_sql = "insert into my_fund (user_id, amt, date_from, date_to,content_group,remark) values({$u['id']}, {$cmsn['pv_sum']}, '{$pvctrl->firstDay}','{$pvctrl->lastDay}','pv','$remark')";
        //     }
        //     $this->db->show($fund_sql);
        //     $count ++;
        // }
        // echo $count . "rows updated";
    }
    function upload_kyc()
    {
        $req = obj($_POST);
        $request = obj($_FILES);
        $old = USER?obj(USER):null;
        if (isset($request->nid_doc) && $request->nid_doc['name'] != "" && $request->nid_doc['error'] == 0) {
            $ext = pathinfo($request->nid_doc['name'], PATHINFO_EXTENSION);
            $docname = str_replace(" ", "_", $this->getUrlSafeString("nid_".$old->username)) . uniqid("_") . "." . $ext;
            $dir = MEDIA_ROOT . "docs/profiles/" . $docname;
            $upload = move_uploaded_file($request->nid_doc['tmp_name'], $dir);
            if ($upload) {
                $arr['nid_doc'] = $docname;
                // $old = obj($user);
                if ($old) {
                    if ($old->nid_doc != "") {
                        $olddir = MEDIA_ROOT . "docs/profiles/" . $old->nid_doc;
                        if (file_exists($olddir)) {
                            unlink($olddir);
                        }
                    }
                }
                $nid_doc = $docname;
                (new Dbobjects)->execSql("update pk_user set nid_doc='$nid_doc' where pk_user.id = '$old->id'");
            }
        }
        if (isset($request->address_doc) && $request->address_doc['name'] != "" && $request->address_doc['error'] == 0) {
            $ext = pathinfo($request->address_doc['name'], PATHINFO_EXTENSION);
            $docname = str_replace(" ", "_", $this->getUrlSafeString("adrs_".$old->username)) . uniqid("_") . "." . $ext;
            $dir = MEDIA_ROOT . "docs/profiles/" . $docname;
            $upload = move_uploaded_file($request->address_doc['tmp_name'], $dir);
            if ($upload) {
                $arr['nid_doc'] = $docname;
                // $old = obj($user);
                if ($old) {
                    if ($old->address_doc != "") {
                        $olddir = MEDIA_ROOT . "docs/profiles/" . $old->address_doc;
                        if (file_exists($olddir)) {
                            unlink($olddir);
                        }
                    }
                }
                $nid_doc = $docname;
                (new Dbobjects)->execSql("update pk_user set address_doc='$nid_doc' where pk_user.id = '$old->id'");
            }
        }
    }
    function getUrlSafeString($inputString)
    {
        // Replace spaces with hyphens and remove other special characters
        $urlSafeString = preg_replace('/[^a-zA-Z0-9\-]/', '-', $inputString);

        // Remove consecutive hyphens and trim leading/trailing hyphens
        $urlSafeString = preg_replace('/-+/', '-', trim($urlSafeString, '-'));

        // Convert to lowercase
        $urlSafeString = strtolower($urlSafeString);

        return $urlSafeString;
    }
    function am_i_active($user_id)
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
            return array('active' => true, 'data' => $data);
        } else {
            return array('active' => false, 'data' => null);
        }
        // myprint($data);
        // echo $this->sql;
    }

    function my_all_commission($userid)
    {
        // New pv claculation
        $pvctrl = new Pv_ctrl;
        $pv_sum = $pvctrl->my_lifetime_commission_sum($userid);
        $pv_sum += $pvctrl->my_admin_commission_sum($userid);
        $rv_sum = $pvctrl->my_lifetime_rank_advance_sum($userid);
        $rv_sum += my_rv_and_admin_rv($user_id = $userid, $dbobj = null);
        $rv_sum += old_data($key_name = "rank_advance", $userid);
        $direct_bonus =  old_data($key_name = "direct_bonus", $userid);
        $direct_bonus +=  $pvctrl->my_lifetime_direct_bonus_sum($userid);
        $position = getPosition($level = $rv_sum);

        $db = new Dbobjects;
        $share = my_all_share($userid);
        $old_lifetime_pv =  old_data($key_name = "commission", $userid);
        $lifetime_pv_new_old = $pv_sum + $old_lifetime_pv;
        ###############################################
        $direct_m = $direct_bonus ? $direct_bonus : 0;
        ###############################################
        $sql = "select SUM(amt) as total_amt from credits where status = 'paid' and remark='confirmed' and user_id = {$userid}";
        $cmsn = $db->show($sql);

        $tm_paid = $cmsn[0]['total_amt'] ? round(($cmsn[0]['total_amt']), 2) : 0;
        $lifetime_m = round(($lifetime_pv_new_old + $direct_m + $share), 2);

        return [
            'position' => $position,
            'cmsn_gt' => $lifetime_m,
            'rv_gt' => $rv_sum,
            'total_paid' => $tm_paid,
            'total_unpaid' => round(($lifetime_m - $tm_paid), 2),
        ];
    }
}
