<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FINDOC</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your FINDOC account</p>
            </div>

            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash']['errors'])): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($_SESSION['flash']['errors'] as $field => $errors): ?>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['flash']['errors']); ?>
            <?php endif; ?>

            <form action="/login" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                    >
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="/forgot-password" class="link-small">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="/register">Create account</a></p>
            </div>
        </div>
    </div>

    <?php unset($_SESSION['old']); ?>
</body>
</html>