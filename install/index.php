<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Install Crusader Works</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f0f4f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.1); width: 100%; max-width: 520px; }
        h1 { margin: 0 0 1rem; font-size: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .35rem; }
        input { width: 100%; padding: .55rem .75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: .9rem; }
        .btn { display: block; width: 100%; padding: .65rem; background: #3b82f6; color: #fff; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; margin-top: 1.5rem; }
        .btn:hover { background: #2563eb; }
        .alert { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: .75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .section-title { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin: 1.5rem 0 .5rem; border-top: 1px solid #e5e7eb; padding-top: 1rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>⚙ Install Crusader Works</h1>

    <?php if (!empty($error)): ?>
    <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

        <div class="section-title">Database</div>
        <div class="form-group"><label>Database Host</label><input type="text" name="db_host" value="localhost" required></div>
        <div class="form-group"><label>Database Name</label><input type="text" name="db_name" required placeholder="crusader_works"></div>
        <div class="form-group"><label>Database User</label><input type="text" name="db_user" required></div>
        <div class="form-group"><label>Database Password</label><input type="password" name="db_pass"></div>

        <div class="section-title">Application</div>
        <div class="form-group"><label>Application Name</label><input type="text" name="app_name" value="Crusader Works" required></div>
        <div class="form-group"><label>Application URL (no trailing slash)</label><input type="text" name="app_url" required placeholder="https://yourdomain.com"></div>

        <div class="section-title">Administrator Account</div>
        <div class="form-group"><label>Admin Name</label><input type="text" name="admin_name" required></div>
        <div class="form-group"><label>Admin Email</label><input type="email" name="admin_email" required></div>
        <div class="form-group"><label>Admin Password (8+ chars)</label><input type="password" name="admin_password" required minlength="8"></div>

        <button type="submit" class="btn">Install Now</button>
    </form>
</div>
</body>
</html>
