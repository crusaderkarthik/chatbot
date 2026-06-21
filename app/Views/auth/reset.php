<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Reset Password</title><link rel="stylesheet" href="<?= asset('css/app.css') ?>"></head>
<body class="auth-body">
<div class="auth-card">
    <h1 class="auth-title">New Password</h1>
    <?php $e = flash('error'); if ($e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endif; ?>
    <form method="POST" action="<?= url('auth/reset/' . $token) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>New Password (8+ chars)</label>
            <input type="password" name="password" required minlength="8" class="form-control" autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
    </form>
</div>
</body>
</html>
