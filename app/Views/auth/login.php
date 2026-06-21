<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login — <?= defined('APP_NAME') ? e(APP_NAME) : 'Crusader Works' ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card">
    <h1 class="auth-title">Crusader Works</h1>
    <p class="auth-subtitle">Sign in to your account</p>

    <?php $err = flash('error'); if ($err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('auth/login') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required autofocus class="form-control">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        <p class="text-center mt-2"><a href="<?= url('auth/forgot') ?>">Forgot password?</a></p>
    </form>
</div>
</body>
</html>
