<?php
import("apps/view/inc/header.php");
import("apps/view/inc/navbar.php");
$plobj = new Model('vouchers');
$pl = $plobj->filter_index(['created_by' => USER['id']],'ASC');
?>
<div id="layoutSidenav">
    <?php import("apps/view/inc/sidebar.php"); ?>
    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <ol class="breadcrumb mt-3 mb-4">
                    <li class="breadcrumb-item active">All Vouchers</li>
                </ol>

                <div class="container">
                    <div class="row mb-4">
                    <div class="col-md-4 my-3">
                            <div class="d-grid">
                            <a class="btn btn-dark" href="/<?php echo home; ?>/create-voucher">Add Voucher</a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table id="datatablesSimple1" class="table datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Value</th>
                                        <th scope="col">Always Valid</th>
                                        <th scope="col">Valid from</th>
                                        <th scope="col">Valid upto</th>
                                        <th scope="col">Edit</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pl as $key => $pv) :
                                        $pv = obj($pv);
                                       
                                    ?>
                                        <tr>
                                            <th scope="row"><?php echo $pv->id; ?></th>
                                            <td><?php echo $pv->code; ?></td>
                                            <td><?php echo $pv->voucher_group==2?"&euro;":null; ?> <?php echo $pv->value; ?><?php echo $pv->voucher_group==1?"% less on gross value":" less on gross value"; ?></td>
                                    
                                            <td><?php echo $pv->always_valid?'YES':"NO"; ?></td>
                                            <td><?php echo $pv->always_valid?'NA':$pv->valid_upto; ?></td>
                                            <td><?php echo $pv->always_valid?'NA':$pv->valid_upto; ?></td>

                                            <td>
                                                <a class="btn-primary btn btn-sm" href="/<?php echo home . "/edit-voucher/?id=" . $pv->id; ?>">Edit</a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <?php import("apps/view/inc/footer-credit.php"); ?>
    </div>
</div>
<?php
import("apps/view/inc/footer.php");
?>