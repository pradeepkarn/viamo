<nav class="sb-topnav navbar navbar-expand navbar-light bg-light">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="/<?php echo home; ?>/public/shop<?php echo isset($_SESSION['sponserid'])?"/?sponserid=".$_SESSION['sponserid']:null; ?>">Viamo</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    
    <div id="google_translate_element"></div>
    <div style="float: right !important;" class="cart_items ms-auto">
        <?php
        $currency_flag = null;
        $currency_code = null;
        $currency_name = null;
        $currency_symbol = null;
        $country_name = null;
        if ($_SESSION['guest_id']) {
            $count = isset($_SESSION['guest_id']) ? cart_items($_SESSION['guest_id']) : 0;
            $curr = getCurrency($keyword = MY_COUNTRY);
            // myprint($curr);
            if (count($curr) > 0) {
                $currency_code = $curr['currency']['code'];
                $currency_name = $curr['currency']['name'];
                $currency_flag = $curr['flag'];
                $country_name = $curr['name'];
            } else {
                $currency_flag = null;
                $currency_code = 'CHF';
                $currency_name = 'Franc';
                $currency_symbol = 'CHF';
                $country_name = null;
            }
        } else {
            $count = 0;
        }

        ?>
        <a href="/<?php echo home; ?>/public/payment" class="cart_icon me-5"><i style="font-size: large;" class="bi bi-cart-fill"><sup><?php echo $count; ?>
            </i></a>
    </div>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4 hide">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="#!">
                        <img src="data:image/png;base64,<?php echo $currency_flag; ?>" alt="<?php echo USER != false ? USER['country'] : null; ?>">
                        <?php echo $country_name; ?>
                    </a>
                </li>
                
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <!-- <li><a class="dropdown-item" href="/<?php //echo home; ?>/logout">Logout</a></li> -->
            </ul>
        </li>
    </ul>
</nav>