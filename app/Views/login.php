<!DOCTYPE html>
<html lang="mr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e3c72">
    <title>लॉगिन - शालेय पोषण आहार प्रणाली</title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon_io/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon_io/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon_io/favicon-16x16.png') ?>">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/all.min.css') ?>">
    <style>
        :root {
            --login-bg-start: #1e3c72;
            --login-bg-end: #2a5298;
            --login-card-radius: 16px;
            --login-padding: 2rem;
        }

        * {
            box-sizing: border-box;
        }

        html {
            height: 100%;
            -webkit-text-size-adjust: 100%;
        }

        body {
            min-height: 100vh;
            min-height: -webkit-fill-available;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            margin: 0;
            background: linear-gradient(135deg, var(--login-bg-start) 0%, var(--login-bg-end) 50%, #7e8ba3 100%);
            font-size: 16px;
            /* Prevents zoom on iOS when focusing inputs */
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            width: 100%;
            padding: var(--login-padding);
            border-radius: var(--login-card-radius);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        }

        .login-card .form-control {
            min-height: 48px;
            /* Touch-friendly tap target */
            font-size: 1rem;
        }

        .login-card .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-login {
            min-height: 48px;
            padding: 0.65rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
        }

        .login-icon {
            font-size: 2.5rem;
        }

        /* Mobile-first responsive adjustments */
        @media (max-width: 576px) {
            body {
                padding: 0.75rem;
            }

            .login-card {
                padding: 1.5rem;
                border-radius: 12px;
            }

            .login-icon {
                font-size: 2rem;
            }

            .login-card h4 {
                font-size: 1.15rem;
            }
        }

        /* Safe area for notched devices (iPhone X+, etc.) */
        @supports (padding: env(safe-area-inset-bottom)) {
            body {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
                padding-bottom: max(1rem, env(safe-area-inset-bottom));
                padding-top: max(1rem, env(safe-area-inset-top));
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="card login-card shadow border-0 bg-white">
            <div class="text-center mb-4">
                <!-- <i class="fas fa-utensils text-primary login-icon mb-2 d-block"></i> -->
                <h4 class="text-primary fw-bold mb-1">शालेय पोषण आहार प्रणाली</h4>
                <p class="text-muted small mb-0">कृपया आपले खाते लॉग-इन करा</p>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2 flex-shrink-0"></i>
                    <span class="flex-grow-1"><?= esc(session()->getFlashdata('error')) ?></span>
                    <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('Auth/login') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold" for="username">यूजरनेम</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?= esc(old('username')) ?>" required autocomplete="username" placeholder="यूजरनेम प्रविष्ट करा">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold" for="password">पासवर्ड</label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password" placeholder="पासवर्ड प्रविष्ट करा">
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>लॉग-इन करा
                </button>
            </form>
        </div>
    </div>
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>