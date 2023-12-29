<?php

$db = new Dbobjects;
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die("Invalid ID");
}
$page_detail = $db->showOne("select * from content where content_group = 'news' and id = '$id'");
$pd = obj($page_detail);

?>

<form action="" id="update-new-page-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Edit Page</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . "/news/list"; ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <h4>Title</h4>
                    <input type="text" name="title" value="<?php echo $pd->title; ?>" class="form-control my-3" placeholder="Title">
                    <!-- <h6>Slug</h6> -->
                    <input type="hidden" name="slug" value="<?php echo $pd->slug; ?>" class="form-control my-3" placeholder="slug">
                    <textarea class="tiny_textarea" name="content" id="mce_0" aria-hidden="true"><?php echo $pd->content; ?></textarea>
                </div>

                <div class="col-md-4">
                    <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="/<?php echo MEDIA_URL; ?>/images/pages/<?php echo $pd->banner; ?>" alt="<?php echo $pd->banner; ?>">
                    <select name="status" class="form-select">
                        <option <?php echo $pd->status == 0 ? "selected" : null; ?> value="0">Draft</option>
                        <option <?php echo $pd->status == 1 ? "selected" : null; ?> value="1">Published</option>
                    </select>
                    <div class="d-grid">
                        <input type="hidden" name="update_content">
                        <input type="hidden" name="id" value="<?php echo $pd->id; ?>">
                        <button id="update-page-btn" type="button" class="btn btn-primary my-3">Update</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<script>
    window.onload = () => {

        const imageInputPage = document.getElementById('image-input');
        const imagePage = document.getElementById('banner');

        imageInputPage.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const fileReader = new FileReader();

            fileReader.onload = () => {
                imagePage.src = fileReader.result;
            };

            fileReader.readAsDataURL(file);
        });

        // for slug
        const titleInput = document.querySelector('input[name="slug"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (titleInput && slugInput) {
            titleInput.addEventListener('keyup', () => {
                const title = titleInput.value.trim();
                generateSlug(title, slugInput);
            });
        }
    }
</script>
<?php pkAjax_form("#update-page-btn", "#update-new-page-form", "#res"); ?>