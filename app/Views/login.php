<!DOCTYPE html>
<html>

<head>
    <title>Login - Poshan Aahar</title>
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
            <h4 class="text-primary fw-bold">शालेय पोषण आहार</h4>
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