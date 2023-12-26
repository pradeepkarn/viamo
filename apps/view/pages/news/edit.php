<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
?>
<script src="https://cdn.tiny.cloud/1/mhpaanhgacwjd383mnua79qirux2ub6tmmtagle79uomfsgl/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active">All Packages</li>
                </ol>
                <?php import("apps/view/pages/news/edit-module.php"); ?>
            </div>
        </main>
        <?php import("apps/view/inc/footer-credit.php"); ?>
    </div>
</div>
<script>
    tinymce.init({
        selector: '.tiny_textarea',
        plugins: 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    });
</script>
<?php
import("apps/view/inc/footer.php");
?>