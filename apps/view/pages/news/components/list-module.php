<?php
$db = new Dbobjects;
$ctx = new stdClass;
$data_limit = 5;

$req = obj($_GET);
$current_page = 0;
$data_limit = $data_limit;
$page_limit = "0,$data_limit";
$cp = 0;
if (isset($req->page) && intval($req->page)) {
    $cp = $req->page;
    $current_page = (abs($req->page) - 1) * $data_limit;
    $page_limit = "$current_page,$data_limit";
}
$rc = $db->showOne("select COUNT(id) as id_count from content where content_group = 'news'");
$total_rows = $rc ? $rc['id_count'] : 0;
$tp = $total_rows;
if ($tp %  $data_limit == 0) {
    $tp = $tp / $data_limit;
} else {
    $tp = floor($tp / $data_limit) + 1;
}
$rows = $db->show("select  * from content where content_group='news' order by id desc limit $page_limit");

$ctx = (object) array(
    'req' => obj($req),
    'rows_count' => $tp,
    'current_page' => $cp,
    'is_active' => true,
    'rows' => $rows
);
$pl = $ctx->rows;
$tp = $ctx->rows_count;
$cp = $ctx->current_page;
$active = $ctx->is_active;
// myprint($pl)
?>



<style>
    .featured-post,
    .trending-post {
        font-size: 30px;
    }
</style>
<section class="section">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col my-3">
                            <h5 class="card-title">All Page</h5>

                        </div>
                        <div class="col my-3 hide">
                            <form action="">
                                <div class="row">
                                    <div class="col-8">
                                        <input value="<?php echo isset($_GET['search']) ? $_GET['search'] : null; ?>" type="search" class="form-control" name="search" placeholder="Search...">
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary ">Search</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col text-end my-3">
                            <a class="btn btn-dark" href="/<?php echo home . "/news/create";
                                                            ?>">Add New</a>
                        </div>
                    </div>
                    <form action="" id="delete-bulk-form">
                        <div id="deletebulkres"></div>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="action" class="form-select" id="">
                                    <option value="">Action</option>
                                    <option value="delete_selected_items">Delete selected (Parmanently)</option>
                                </select>
                            </div>
                            <div class="col-md-4">

                                <button type="submit" id="delete-bulk-btn" class="btn btn-danger">Done</button>

                            </div>
                        </div>
                    </form>

                    <?php
                    ajaxActive("#upload-info");
                    pkAjax_form("#delete-bulk-btn", "#delete-bulk-form", "#deletebulkres");
                    ?>
                    <!-- Table with stripped rows -->
                    <table class="table datatable">
                        <thead>
                            <tr>

                                <th scope="col">
                                    <input type="checkbox" id="selct_all_ids"> Select
                                </th>
                                <th scope="col">Id</th>

                                <th scope="col">Banner</th>
                                <th scope="col">Title</th>
                                
                                <th scope="col">Status</th>
                                <th scope="col">Publish Date</th>
                                <th scope="col">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pl as $key => $pv) :
                                $pv = obj($pv);
                                $cat = getData(table: "content", id: $pv->parent_id);
                                $cat_title =  $cat ? $cat['title'] : "Uncategorised";
                            ?>

                                <tr>
                                    <th>
                                        <input type="checkbox" name="selected_obj_id" value="<?php echo $pv->id; ?>">
                                    </th>
                                    <th scope="row"><?php echo $pv->id; ?></th>
                                    <th>
                                        <img style="width:100%; max-height:30px; object-fit:cover;" src="/<?php echo MEDIA_URL; ?>/images/pages/<?php echo $pv->banner; ?>" alt="<?php echo $pv->title; ?>">
                                    </th>
                                    <td><?php echo $pv->title; ?></td>
                                   
                                    <td>
                                        <?php echo $pv->status==1?"Published":"Draft"; ?>
                                    </td>
                                    <td><?php echo $pv->pub_date; ?></td>

                                    <td>
                                        <a class="btn-primary btn btn-sm" href="/<?php echo home . "/news/edit/?id=" . $pv->id; ?>">Edit</a>
                                    </td>


                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- End Table with stripped rows -->
                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">

                            <?php
                            $tp = $tp;
                            $current_page = $cp; // Assuming first page is the current page
                            if ($active == true) {
                                $link =  '/news/list/'; // route('pageList');
                            } else {
                                $link =  '/news/list/'; // route('pageTrashList');
                            }
                            // Show first two pages
                            for ($i = 1; $i <= $tp; $i++) {
                            ?>
                                <li class="page-item"><a class="page-link" href="/<?php echo home . $link . "?page=$i"; ?>"><?php echo $i; ?></a></li>
                            <?php
                            } ?>




                        </ul>
                    </nav>

                    <!-- Pagination -->
                </div>

            </div>

        </div>
    </div>
</section>
<script>
    function sendData(data, url, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = () => {
            if (xhr.status === 200) {
                // console.log('Data successfully sent.');
                callback(null, xhr.responseText);
            } else {
                console.log('Request failed. Status:', xhr.status);
                callback(xhr.status);
            }
        };
        xhr.send(JSON.stringify(data));
    }
</script>





<script>
    const selectAllCheckbox = document.getElementById('selct_all_ids');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_obj_id"]');
    const deleteBulkForm = document.getElementById('delete-bulk-form');

    selectAllCheckbox.addEventListener('change', function() {
        individualCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            updateFormInputs(checkbox);
        });
    });

    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateFormInputs(checkbox);
            selectAllCheckbox.checked = Array.from(individualCheckboxes).every(checkbox => checkbox.checked);
        });
    });

    function updateFormInputs(checkbox) {
        if (checkbox.checked) {
            appendInput(deleteBulkForm, 'selected_ids[]', checkbox.value);
        } else {
            removeInput(deleteBulkForm, 'selected_ids[]', checkbox.value);
        }
    }

    function appendInput(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    function removeInput(form, name, value) {
        const inputToRemove = form.querySelector(`input[name="${name}"][value="${value}"]`);
        if (inputToRemove) {
            form.removeChild(inputToRemove);
        }
    }

    deleteBulkForm.addEventListener('submit', function(event) {
        individualCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                removeInput(deleteBulkForm, 'selected_ids[]', checkbox.value);
            }
        });
    });
</script>