<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - SB Admin</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/<?php echo STATIC_URL ?>/assets/img/img7.jpg">
    <link href="/<?php echo home; ?>/static/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        /* Media query to hide the element on screens smaller than 768px (typical mobile devices) */
        @media only screen and (max-width: 768px) {
            .mobile-hide {
                display: none;
            }
        }

        /* Media query to hide the element on screens larger than 768px (typical desktop devices) */
        @media only screen and (min-width: 769px) {
            .desktop-hide {
                display: none;
            }
        }
        /* table.cost-details td{
            width: 100%;
            font-size: 12px;
        } */
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.0/css/select2.min.css" />


<!-- Include Select2 JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.0/js/select2.min.js"></script>
</head>

<body class="sb-nav-fixed">