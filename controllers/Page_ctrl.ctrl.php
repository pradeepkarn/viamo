<?php
class Page_ctrl
{
    // Save by ajax call
    public function save($req = null)
    {
        $request = null;
        $data = null;
        $data = $_POST;
        $data['banner'] = $_FILES['banner'];
        $rules = [
            'title' => 'required|string',
            'content' => 'required|string',
            'banner' => 'required|file'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            return;
        }

        $request = obj($data);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->title)) {
            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "news";
            $arr['title'] = $request->title;
            $arr['slug'] = getUrlSafeString(generate_slug(trim($request->title)));
            $arr['content'] = $request->content;
            $arr['status'] = intval(isset($request->status)?$request->status:0);
            $arr['created_at'] = date('Y-m-d H:i:s');
            $postid = (new Model('content'))->store($arr);
            if (intval($postid)) {
                $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                $imgname = str_replace(" ", "_", getUrlSafeString($request->title)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
                if ($upload) {
                    (new Model('content'))->update($postid, array('banner' => $imgname));
                }
                echo js_alert('Content created');
                echo go_to("news/list");
            } else {
                echo js_alert('Page not created');
                return false;
            }
        }
    }

    // Save by ajax call
    public function update($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        $request = null;
        $data = null;
        $data = $_POST;
        $data['id'] = $req->id;
        $data['banner'] = $_FILES['banner'];
        $rules = [
            'id' => 'required|integer',
            'title' => 'required|string',
            'content' => 'required|string'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        $request = obj($data);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->title)) {
            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "news";
            $arr['title'] = $request->title;
            // if ($content->slug != $request->slug) {
            //     $arr['slug'] = generate_slug(trim($request->slug));
            // }
            $arr['content'] = $request->content;
            // $arr['parent_id'] = $request->parent_id;
            $arr['status'] = intval(isset($request->status)?$request->status:0);
            $arr['updated_at'] = date('Y-m-d H:i:s');
            if ($request->banner['name'] != "" && $request->banner['error'] == 0) {
                $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                $imgname = str_replace(" ", "_", getUrlSafeString($request->title)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
                if ($upload) {
                    $arr['banner'] = $imgname;
                    $old = obj($content);
                    if ($old) {
                        if ($old->banner != "") {
                            $olddir = MEDIA_ROOT . "images/pages/" . $old->banner;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                }
            }
            try {
                (new Model('content'))->update($request->id, $arr);
                echo js_alert('Content updated');
                // echo go_to(route('pageEdit', ['id' => $request->id]));
                echo RELOAD;
                return;
            } catch (PDOException $e) {
                echo js_alert('Content not updated, check slug or content data');
                return;
            }
        }
    }
    public function move_to_trash($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        try {
            (new Model('content'))->update($req->id, array('is_active' => 0));
            echo js_alert('Content moved to trash');
            // echo go_to(route('pageList'));
            return;
        } catch (PDOException $e) {
            echo js_alert('Content not moved to trash');
            return;
        }
    }
    public function restore($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        try {
            (new Model('content'))->update($req->id, array('is_active' => 1));
            echo js_alert('Content moved to active list');
            // echo go_to(route('pageTrashList'));
            return;
        } catch (PDOException $e) {
            echo js_alert('Content not moved to active list');
            return;
        }
    }
    public function delete_trash($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            return;
        }
        try {
            $content_exists = (new Model('content'))->exists(['id' => $req->id, 'is_active' => 0]);
            if ($content_exists) {
                if ((new Model('content'))->destroy($req->id)) {
                    echo js_alert('Content deleted permanatly');
                    // echo go_to(route('pageTrashList'));
                    return;
                }
            }
            echo js_alert('Content does not exixt');
            // echo go_to(route('pageTrashList'));
            return;
        } catch (PDOException $e) {
            echo js_alert('Content not deleted');
            return;
        }
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
    // Post list
    public function page_list($ord = "DESC", $limit = 5, $active = 1, $sort_by = 'views')
    {
        $cntobj = new Model('content');
        return $cntobj->filter_index(array('content_group' => 'page', 'is_active' => $active), $ord, $limit, $change_order_by_col = $sort_by);
    }
    public function page_search_list($keyword, $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('content');
        $search_arr['id'] = $keyword;
        $search_arr['title'] = $keyword;
        // $search_arr['content'] = $keyword;
        $search_arr['author'] = $keyword;
        // $search_arr['created_at'] = $keyword;
        // $search_arr['updated_at'] = $keyword;
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit,
            whr_arr: array('content_group' => 'page', 'is_active' => $active)
        );
    }
    public function page_detail($id)
    {
        $cntobj = new Model('content');
        return $cntobj->show($id);
    }
    // category list
    public function cat_list($ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('content');
        return $cntobj->filter_index(array('content_group' => 'post_category', 'is_active' => $active), $ord, $limit);
    }
    function delete_bulk()
    {
        $action = $_POST['action'] ?? null;
        $ids = $_POST['selected_ids'] ?? null;
        if ($action != null && $action == "delete_selected_items" && $ids != null) {
            $num = count($ids);
            if ($num == 0) {
                echo js_alert('Object not seleted');
                exit;
            };
            $idsString = implode(',', $ids);
            $db = new Dbobjects;
            $pdo = $db->conn;
            $pdo->beginTransaction();
            $sql = "DELETE FROM content WHERE id IN ($idsString) AND content_group='news'";
            try {
                $db->show($sql);
                $pdo->commit();
                echo js_alert("$num Selected item deleted");
                echo RELOAD;
                return true;
            } catch (PDOException $pd) {
                $pdo->rollBack();
                echo js_alert('Database quer error');
                return false;
            }
        } else {
            echo js_alert('Action not or items not selected');
            exit;
        }
    }
}
