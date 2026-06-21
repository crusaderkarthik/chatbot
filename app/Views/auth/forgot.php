<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Forgot Password</title><link rel="stylesheet" href="<?= asset('css/app.css') ?>"></head>
<body class="auth-body">
<div class="auth-card">
    <h1 class="auth-title">Reset Password</h1>
    <?php $s = flash('success'); $e = flash('error'); ?>
    <?php if ($s): ?><div class="alert alert-success"><?= e($s) ?></div><?php endif; ?>
    <?php if ($e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endif; ?>
    <form method="POST" action="<?= url('auth/forgot') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required class="form-control" autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
        <p class="text-center mt-2"><a href="<?= url('auth/login') ?>">Back to login</a></p>
    </form>
</div>
</body>
</html>
