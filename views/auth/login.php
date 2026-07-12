<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AssetFlow - Employee Login">
    <title>AssetFlow - Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Inter for modern typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Login Styles -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

    <main class="login-page">
        <div class="login-card">

            <!-- Header -->
            <header class="login-header">
                <h1 class="login-title">AssetFlow - Login</h1>
            </header>

            <!-- Logo -->
            <div class="login-logo-wrapper">
                <div class="login-logo" aria-label="AssetFlow logo">
                    <span>AF</span>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger login-alert" role="alert">
                <?= e($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'success' ? 'success' : 'info') ?> login-alert" role="alert">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" class="login-form" method="POST" action="index.php?page=login" novalidate>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="name@company.com"
                        value="<?= e($email) ?>"
                        required
                        autocomplete="email"
                    >
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        minlength="6"
                        autocomplete="current-password"
                    >
                    <div class="invalid-feedback">Password must be at least 6 characters.</div>
                </div>

                <div class="forgot-password-link">
                    <a href="index.php?page=forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100">Sign In</button>
            </form>

            <!-- Divider -->
            <hr class="login-divider">

            <!-- Signup Section -->
            <section class="signup-section">
                <p class="signup-heading">New here?</p>

                <div class="signup-info-box" role="note">
                    <p>Sign up creates an employee account. Admin roles are assigned later.</p>
                </div>

                <a href="index.php?page=signup" class="btn btn-primary btn-create-account w-100">
                    Create Account
                </a>
            </section>

        </div>
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Login Validation -->
    <script src="assets/js/login.js"></script>
</body>
</html>
