<form action="" id="save-new-page-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add Page</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . "/news/list"; ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <h4>Title</h4>
                    <input type="text" name="title" class="form-control my-3" placeholder="Title">
                    <h4>Body</h4>
                    <textarea class="tiny_textarea" name="content" id="mce_0" aria-hidden="true"></textarea>
                    
                </div>
                <div class="col-md-4">
                    <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="" alt="">
                    <select name="status" class="form-select">
                        <option value="0">Draft</option>
                        <option value="1">Published</option>
                    </select>
                    <div class="d-grid">
                        <input type="hidden" name="create_content">
                        <button id="save-page-btn" type="button" class="btn btn-primary my-3">Save</button>
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

        const titleInput = document.querySelector('input[name="title"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (titleInput && slugInput) {
            titleInput.addEventListener('keyup', () => {
                const title = titleInput.value.trim();
                generateSlug(title, slugInput);
            });
        }
    }
</script>
<?php pkAjax_form("#save-page-btn", "#save-new-page-form", "#res"); ?>