<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Shaley Poshan Aahar System</title> -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon_io/apple-touch-icon.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon_io/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon_io/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon_io/favicon-16x16.png') ?>">

    <link href="<?php echo base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('css/all.min.css'); ?>">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
        }

        .main-container {
            margin-top: 30px;
            margin-bottom: 50px;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .navbar-nav .nav-link.active {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        /* Project-wide alert messages - margin only */
        .alert-wrapper { margin: 0.5rem; }

        /* Project-wide button/link consistency */
        .card-header .btn-success.btn-sm,
        .card-header .btn-primary.btn-sm { min-height: 44px; display: inline-flex; align-items: center; }
        .card-header .btn-header-group { display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; align-items: center; }
        .btn-action-group .btn { margin-right: 0.25rem; }
        .btn-action-group .btn:last-child { margin-right: 0; }

        /* Responsive Header adjustment without changing HTML */
        @media (max-width: 768px) {

            /* Target the card-header with d-flex */
            .card-header.d-flex {
                flex-direction: column !important;
                /* Forces buttons below heading */
                align-items: center !important;
                text-align: center;
            }

            /* Add space between heading and button container */
            .card-header.d-flex h5 {
                margin-bottom: 15px !important;
            }

            /* Make the button container full width */
            .card-header.d-flex>div {
                display: flex;
                flex-direction: column;
                /* Stack buttons on top of each other */
                width: 100%;
                gap: 10px;
                /* Space between the two buttons */
            }

            /* Remove the 'me-2' margin and make buttons full width */
            .card-header.d-flex .btn {
                margin-right: 0 !important;
                width: 100%;
                display: flex !important;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-utensils me-2"></i> शालेय पोषण आहार प्रणाली
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link text-warning" href="<?= base_url('Auth/logout') ?>">
                            <i class="fas fa-sign-out-alt"></i> बाहेर पडा (Logout)
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('items*') ? 'active fw-bold border-bottom' : '' ?>" href="<?= base_url('items') ?>">
                            <i class="fas fa-list me-1"></i> वस्तू यादी
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('ItemRates*') ? 'active fw-bold border-bottom' : '' ?>" href="<?= base_url('ItemRates') ?>">
                            <i class="fas fa-coins me-1"></i> वस्तू दर
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('StudentStrength*') ? 'active fw-bold border-bottom' : '' ?>" href="<?= base_url('StudentStrength') ?>">
                            <i class="fas fa-users me-1"></i> विद्यार्थी संख्या
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('Stock*') ? 'active fw-bold border-bottom' : '' ?>" href="<?= base_url('Stock') ?>">
                            <i class="fas fa-box me-1"></i> स्टॉक नोंद
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('entries*') ? 'active fw-bold border-bottom' : '' ?>" href="<?= base_url('entries') ?>">
                            <i class="fas fa-edit me-1"></i> दैनंदिन नोंद
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="row">
            <div class="col-md-12">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 bg-white border-top mt-auto">
        <p class="text-muted mb-0">&copy; <?= date('Y') ?> शालेय पोषण आहार प्रणाली</p>
    </footer>

    <script src="<?php echo base_url('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('js/jquery-3.6.0.min.js'); ?>"></script>



</body>

</html>