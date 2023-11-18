<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid pt-3 px-4">
                <h1 class="mt-4">News</h1>
                <ol class="breadcrumb mb-4 mypop">
                    <li class="breadcrumb-item active">News</li>
                </ol>
                <section>
                    <div class="container px-4 ms-2">
                        <div class="row">
                            <div class="col bst_news">
                                <marquee class="marque_news mt-2" behavior="scroll" scrollamount="8" direction="left">Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel maiores eos alias eius rerum qui expedita, architecto, quasi quae consequatur tenetur delectus perferendis numquam. Dolor ad laboriosam exercitationem expedita. Excepturi.</marquee>
                            </div>
                        </div>
                    </div>
                    <div class="container px-4 mb-5">
                        <div class="row">
                            <div class="col-5 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title pb-3">Title</h5>
                                        <p class="card-text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptate error aut dolor explicabo facilis culpa enim numquam quia, earum blanditiis asperiores fugiat! Animi velit eligendi consequatur illo laudantium cum in.</p>
                                        <a href="#" class="card-link" style="color:black; text-decoration:none">Date</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <?php import("apps/view/inc/footer-credit.php"); ?>
            </div>
    </div>
</div>

<?php
import("apps/view/inc/footer.php");
