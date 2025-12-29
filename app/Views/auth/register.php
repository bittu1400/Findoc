<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FINDOC</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Join FINDOC to book doctor appointments</p>
            </div>

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

            <form action="/register" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="<?= htmlspecialchars($_SESSION['old']['phone'] ?? '') ?>"
                        placeholder="+977 9800000000"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="6"
                    >
                    <small>Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Register As</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input 
                                type="radio" 
                                name="role" 
                                value="patient" 
                                <?= (!isset($_SESSION['old']['role']) || $_SESSION['old']['role'] === 'patient') ? 'checked' : '' ?>
                                required
                            >
                            <span>Patient</span>
                            <small>Book appointments with doctors</small>
                        </label>
                        
                        <label class="radio-label">
                            <input 
                                type="radio" 
                                name="role" 
                                value="doctor"
                                <?= (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] === 'doctor') ? 'checked' : '' ?>
                            >
                            <span>Doctor</span>
                            <small>Manage appointments and patients</small>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Create Account</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="/login">Sign in</a></p>
            </div>
        </div>
    </div>

    <?php unset($_SESSION['old']); ?>
</body>
</html>