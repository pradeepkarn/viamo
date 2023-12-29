<?php
$url = $url = explode("/", $_SERVER["QUERY_STRING"]);
$namespace = $url[0];
$home = home;
if (!isset($url[1])) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete_selected_items') {
            $pg_ctrl = new Page_ctrl;
            if(is_superuser()){
                $pg_ctrl->delete_bulk();
            }
            return;
        }
        return;
    }
    import("apps/view/pages/news/list.php");
    return;
} else if (isset($url[1]) && $url[1] == '') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete_selected_items') {
            $pg_ctrl = new Page_ctrl;
            if(is_superuser()){
                $pg_ctrl->delete_bulk();
            }
            return;
        }
        return;
    }
    import("apps/view/pages/news/list.php");
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/list") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete_selected_items') {
            $pg_ctrl = new Page_ctrl;
            if(is_superuser()){
                $pg_ctrl->delete_bulk();
            }
            return;
        }
        return;
    }
    import("apps/view/pages/news/list.php");
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/edit") {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_content'])) {
            $pg_ctrl = new Page_ctrl;
            $pg_ctrl->update($req = $_GET);
            // myprint($_POST);
            return;
        }
    }
    import("apps/view/pages/news/edit.php");
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/create") {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['create_content'])) {
            $pg_ctrl = new Page_ctrl;
            $pg_ctrl->save($req = $_GET);
            // myprint($_POST);
            return;
        }
    }
    import("apps/view/pages/news/create.php");
    return;
}
if ("{$url[0]}/{$url[1]}" == "{$namespace}/articles") {
    if (isset($_GET['story'])) {
        import("apps/view/pages/news/view-single.php");
        return;
    }
    import("apps/view/pages/news/view-list.php");
    return;
}
