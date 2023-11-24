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
                    <div class="container ms-2">
                        <div class="row">
                            <div class="col bst_news">
                                <marquee class="marque_news mt-2" behavior="scroll" scrollamount="8" direction="left">
                                    Herzlich Willkommen in der VIAMO-Community.
                                    Am 26. November starten wir die PRE-Launch-Phase mit Webinaren um 10:00/16:00/20:00 Uhr hier:
                                    <a href="https://us02web.zoom.us/j/86939220044">https://us02web.zoom.us/j/86939220044</a>
                                </marquee>
                            </div>
                        </div>
                    </div>
                    <div class="container mb-5">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title pb-3">Title</h5>
                                        <p class="card-text">
                                            Herzlich Willkommen in der VIAMO-Community.
                                            Am 26. November starten wir die PRE-Launch-Phase mit Webinaren um 10:00/16:00/20:00 Uhr hier:
                                            <a href="https://us02web.zoom.us/j/86939220044">https://us02web.zoom.us/j/86939220044</a>
                                        </p>
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
