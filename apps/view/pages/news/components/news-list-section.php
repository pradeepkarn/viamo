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
$rc = $db->showOne("select COUNT(id) as id_count from content where content_group = 'news' and status=1");
$total_rows = $rc ? $rc['id_count'] : 0;
$tp = $total_rows;
if ($tp %  $data_limit == 0) {
    $tp = $tp / $data_limit;
} else {
    $tp = floor($tp / $data_limit) + 1;
}
$rows = $db->show("select  * from content where content_group='news' and status=1 order by id desc limit $page_limit");

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
    /* Custom styles for the blog section */
    .blog-section {
        padding: 80px 0;
    }

    .blog-post {
        margin-bottom: 30px;
    }

    .post-title {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .post-meta {
        color: #777;
    }

    .read-more-btn {
        color: #007bff;
        text-decoration: none;
    }

    .blog-post img {
        width: 100%;
        height: 200px;
        object-fit: contain;
    }
    
</style>
<section class="blog-section">
    <div class="container">
        <div class="row">
            <?php foreach ($pl as $key => $pv) :
                $pv = obj($pv);
                $cat = getData(table: "content", id: $pv->parent_id);
                $cat_title =  $cat ? $cat['title'] : "Uncategorised";
            ?>
                <!-- Blog Post <?php echo $key; ?> -->

                <div class="col-md-4 h-100 my-2 flex-fill">
                <a href="<?php echo "/" . home . "/news/articles/?story=" . $pv->slug; ?>" class="read-more-btn">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="blog-post">
                                <img class="blog-img" src="<?php echo "/" . home . "/media/images/pages/" . $pv->banner; ?>" alt="<?php echo $pv->title; ?>">
                                <h3 class="post-title"><?php echo $pv->title; ?></h3>
                                <p class="post-meta"><?php echo $pv->pub_date; ?></p>
                                <?php echo pk_excerpt($pv->content,50); ?>
                               
                            </div>
                        </div>
                    </div>
                </a>
                </div>
            <?php endforeach; ?>

        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination">

                <?php
                $tp = $tp;
                $current_page = $cp; // Assuming first page is the current page
                if ($active == true) {
                    $link =  '/news/articles/'; // route('pageList');
                } else {
                    $link =  '/news/articles/'; // route('pageTrashList');
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
</section>