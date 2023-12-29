<?php

$db = new Dbobjects;
$slug = isset($_GET['story']) ? $_GET['story'] : null;
if (!$slug) {
    die("Invalid ID");
}
$page_detail = $db->showOne("select content.*, pk_user.first_name, pk_user.last_name from content
JOIN pk_user on content.created_by=pk_user.id
where content_group = 'news' and slug = '$slug'");
$pd = obj($page_detail);

// Fetch previous article
$prev = $db->showOne("SELECT slug, title 
                                  FROM content
                                  WHERE content_group = 'news' 
                                        AND pub_date < '$pd->pub_date'
                                  ORDER BY pub_date DESC
                                  LIMIT 1");

// Fetch next article
$next = $db->showOne("SELECT slug, title 
                              FROM content
                              WHERE content_group = 'news' 
                                    AND pub_date > '$pd->pub_date'
                              ORDER BY pub_date ASC
                              LIMIT 1");
?>
<style>
    body {
        background-color: #f8f9fa;
    }

    .single-post {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 50px auto;
        padding: 30px;
    }

    .post-title {
        font-size: 2rem;
        margin-bottom: 20px;
    }

    .post-meta {
        color: #777;
        margin-bottom: 20px;
    }

    .post-content {
        line-height: 1.6;
    }

    .comments-section {
        margin-top: 40px;
    }

    .comment {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f0f0f0;
        border-radius: 8px;
    }

    .comment-author {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .comment-date {
        color: #777;
    }
    img#banner{
        max-height: 300px;
        width: 100%;
        object-fit: contain;
    }
</style>
<!-- Single Post -->
<div class="container">
    <a class="btn btn-dark" href="<?php echo "/" . home . "/news/articles"; ?>">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div class="single-post">
          <!-- Banner Image -->
          <img id="banner" class="text-center" src="<?php echo "/" . home . "/media/images/pages/" . $pd->banner; ?>" alt="Banner Image">

        <h1 class="post-title"><?php echo $pd->title; ?></h1>
        <p class="post-meta">Published on <?php echo $pd->pub_date; ?> by <?php echo "{$pd->first_name} {$pd->last_name}"; ?></p>
        <div class="post-content">
            <?php echo $pd->content; ?>
        </div>
    </div>

    <!-- Previous and Next Links -->
    <div class="row mt-4">
        <div class="col-md-6">
            <?php if ($prev) : ?>
                <a href="<?php echo "/" . home . "/news/articles/?story=" . $prev['slug']; ?>" class="btn btn-primary">&laquo; Read Previous</a>
            <?php endif; ?>
        </div>
        <div class="col-md-6 text-md-end">
            <?php if ($next) : ?>
                <a href="<?php echo "/" . home . "/news/articles/?story=" . $next['slug']; ?>" class="btn btn-primary">Read Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</div>