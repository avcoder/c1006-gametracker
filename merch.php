<?php
// session_set_cookie_params(['samesite' => 'none', 'secure' => true]);
session_start();
require_once 'validations.php';


// connect to db
require_once 'database.php';
$conn = db_connect();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">

    <link rel="preconnect" href="https://app.snipcart.com">
    <link rel="preconnect" href="https://cdn.snipcart.com">
    <link rel="stylesheet" href="https://cdn.snipcart.com/themes/v3.2.0/default/snipcart.css" />

</head>

<body>
    <div class="container">

        <header class="mb-5">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand gamefont fs-3" href="#">Merch</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="main.php">Games</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav d-flex">
                            <li class="nav-item">
                                <button class="btn btn-success snipcart-checkout">
                                    <span class="snipcart-total-price">$0.00</span>
                                    <i class="bi bi-cart4"></i>
                                    <span class="badge rounded-pill bg-dark snipcart-items-count">0</span>                                    
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

<?php

$sql = "SELECT * FROM items";
$items = db_queryAll($sql, $conn);
?>

<div class="row">
    <div class="col">
        <?php foreach($items as $item) { ?>
            <div class="card mb-4" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                    <a href="<?= $item['item_image'] ?>">
                        <img src="<?= $item['item_image'] ?>" class="img-fluid rounded-start" alt="">
                    </a>
                    </div>
                    <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?= $item['item_name'] ?></h5>
                        <p class="card-text"><?= $item['item_desc'] ?></p>
                        <button class="btn btn-primary buy-button snipcart-add-item"
                            data-item-id="<?= $item['item_id'] ?>"
                            data-item-price="<?= $item['item_price'] ?>"
                            data-item-url="/"
                            data-item-name="<?= $item['item_name'] ?>"
                            data-item-description="<?= $item['item_desc'] ?>"
                            data-item-image="<?= $item['item_image'] ?>"
                        >
                            Add to cart ($<?= $item['item_price'] ?>)
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        <?php } ?>    
    </div>
    <div class="col">
        <img src="items/bg.jpg" alt="">
    </div>
</div>

<script async src="https://cdn.snipcart.com/themes/v3.2.0/default/snipcart.js"></script>
<div hidden id="snipcart" data-api-key="YzRjM2RiZmItYzA3NC00OGNmLWJhMjMtMTZjNGU3MmJlZGJjNjM3Mzg3MjAxNjQwNTQxOTAx"></div>

<?php
include_once 'shared/footer.php';
?>