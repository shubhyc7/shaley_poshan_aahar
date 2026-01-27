<!DOCTYPE html>
<html>

<head>
    <!-- <title>Login - Poshan Aahar</title> -->
    <title>लॉगिन - शालेय पोषण आहार प्रणाली</title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon_io/apple-touch-icon.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon_io/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon_io/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon_io/favicon-16x16.png') ?>">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body {
            background: #f4f7f6;
            display: flex;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="card login-card shadow border-0">
        <div class="text-center mb-4">
            <h4 class="text-primary fw-bold">शालेय पोषण आहार प्रणाली</h4>
            <p class="text-muted small">कृपया आपले खाते लॉग-इन करा</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('Auth/login') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">यूजरनेम</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">पासवर्ड</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">लॉग-इन करा</button>
        </form>
    </div>
</body>

</html>