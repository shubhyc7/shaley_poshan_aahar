<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaley Poshan Aahar System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-utensils me-2"></i> POSHAN AAHAR
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('entries') ?>">
                            <i class="fas fa-edit me-1"></i> Daily Entry
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('StudentStrength') ?>">
                            <i class="fas fa-users me-1"></i> Student Strength
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('items') ?>">
                            <i class="fas fa-list me-1"></i> Items Master
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('ItemRates') ?>">
                            <i class="fas fa-list me-1"></i> Item Rates
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="<?= base_url('reports') ?>">
                            <i class="fas fa-file-alt me-1"></i> Monthly Report
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
        <p class="text-muted mb-0">&copy; <?= date('Y') ?> Shaley Poshan Aahar Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>

</html>