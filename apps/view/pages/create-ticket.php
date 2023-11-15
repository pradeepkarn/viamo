<?php 
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
?>
<div id="layoutSidenav">
<?php import("apps/view/inc/sidebar.php"); ?>
<div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <!-- <h1 class="mt-4">Dashboard</h1> -->
                        <ol class="breadcrumb mt-3 mb-4">
                            <li class="breadcrumb-item active mycl">Create Ticket</li>
                        </ol>

                        <div class="container">
                    <div class="row">
                        <div class="col-6">
                        <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Ticket name" aria-label="Ticket name">
                        </div>
                        <div class="col">
                            <input type="date" class="form-control" placeholder="Date" aria-label="Date">
                        </div>
                    </div>
                        <div class="row mb-3">
                        <div class="col">
                        <input type="text" class="form-control" placeholder="Status" aria-label="Status">
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Subject" aria-label="Subject">
                        </div>
                    </div>
                        <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Ticket Type" aria-label="Ticket Type">
                        </div>
                        <div class="col">
                            <input type="text" hidden class="form-control" placeholder="Opened By" aria-label="Opened By">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary">Create Ticket</button>
                    <div class="row mt-4">
                        <div class="col-6">
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">Message</button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalOne">Reply</button>
                            </div>
                    </div>
                        </div>
                    </div>
                    
                </div>
                        </div>
                       

<!-- Message Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Message title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <textarea class="form-control" name="" id="" cols="30" rows="4" placeholder="Send Message"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send</button>
      </div>
    </div>
  </div>
</div>


<!-- Reply Modal -->
<div class="modal fade" id="exampleModalOne" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Reply title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" name="" id="" cols="30" rows="4" placeholder="Send Reply"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send</button>
      </div>
    </div>
  </div>
</div>

                </main>
                <?php import("apps/view/inc/footer-credit.php");?>
            </div>
</div>
<?php 
import("apps/view/inc/footer.php");
?>