<?php
// Show errors during install so we can diagnose issues
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start session independently — installer is self-contained
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple install-only CSRF (no dependency on app helpers)
if (empty($_SESSION['install_token'])) {
    $_SESSION['install_token'] = bin2hex(random_bytes(32));
}
$install_token = $_SESSION['install_token'];

$error   = '';
$success = false;

// Handle POST here if called directly (fallback path)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tok = $_POST['_install_token'] ?? '';
    if (!hash_equals($install_token, $tok)) {
        $error = 'Security token mismatch. Please refresh and try again.';
    } else {
        $dbHost  = trim($_POST['db_host'] ?? 'localhost');
        $dbName  = trim($_POST['db_name'] ?? '');
        $dbUser  = trim($_POST['db_user'] ?? '');
        $dbPass  = $_POST['db_pass'] ?? '';
        $appName = trim($_POST['app_name'] ?? 'Crusader Works');
        $appUrl  = rtrim(trim($_POST['app_url'] ?? ''), '/');
        $adminName  = trim($_POST['admin_name'] ?? '');
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminPass  = $_POST['admin_password'] ?? '';

        if (!$dbName || !$dbUser || !$adminEmail || strlen($adminPass) < 8) {
            $error = 'All fields are required and password must be at least 8 characters.';
        } else {
            try {
                $pdo = new PDO(
                    "mysql:host=$dbHost;charset=utf8mb4",
                    $dbUser, $dbPass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `$dbName`");

                $schemaFile = __DIR__ . '/schema.sql';
                if (!file_exists($schemaFile)) {
                    $error = 'schema.sql not found at: ' . $schemaFile;
                } else {
                    // Split and execute statements one by one
                    $sql = file_get_contents($schemaFile);
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    foreach ($statements as $stmt) {
                        if ($stmt !== '') $pdo->exec($stmt);
                    }

                    // Admin user
                    $hash = password_hash($adminPass, PASSWORD_BCRYPT);
                    $pdo->prepare(
                        "INSERT INTO users (name, email, password, role, status, timezone, theme, permissions, notification_prefs, created_at)
                         VALUES (?,?,?,'admin','active','UTC','light','[]','{}',NOW())"
                    )->execute([$adminName ?: 'Administrator', $adminEmail, $hash]);

                    // Default settings
                    foreach ([['app_name', $appName], ['app_url', $appUrl]] as [$k, $v]) {
                        $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?")
                            ->execute([$k, $v, $v]);
                    }

                    // Write config
                    $configDir = dirname(__DIR__) . '/config';
                    if (!is_dir($configDir)) mkdir($configDir, 0755, true);

                    $config = "<?php\n"
                        . "define('DB_HOST', " . var_export($dbHost, true) . ");\n"
                        . "define('DB_NAME', " . var_export($dbName, true) . ");\n"
                        . "define('DB_USER', " . var_export($dbUser, true) . ");\n"
                        . "define('DB_PASS', " . var_export($dbPass, true) . ");\n"
                        . "define('BASE_URL', " . var_export($appUrl, true) . ");\n"
                        . "define('APP_NAME', " . var_export($appName, true) . ");\n";

                    $configPath = $configDir . '/config.php';
                    if (file_put_contents($configPath, $config) === false) {
                        $error = 'Could not write config/config.php — check folder permissions (chmod 755 config/).';
                    } else {
                        $success = true;
                        $loginUrl = rtrim($appUrl, '/') . '/auth/login';
                    }
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            } catch (Throwable $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Install Crusader Works</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f0f4f8; display: flex; align-items: flex-start; justify-content: center; min-height: 100vh; margin: 0; padding: 40px 16px; }
        .card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.1); width: 100%; max-width: 560px; }
        h1 { margin: 0 0 1.5rem; font-size: 1.5rem; color: #1e293b; }
        h3 { margin: 1.5rem 0 .5rem; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #64748b; padding-top: 1rem; border-top: 1px solid #e2e8f0; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-size: .82rem; font-weight: 600; color: #475569; margin-bottom: .3rem; }
        input[type=text], input[type=email], input[type=password], input[type=url] {
            width: 100%; padding: .55rem .75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: .9rem;
            background: #f8fafc; color: #1e293b;
        }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); background: #fff; }
        .btn { display: block; width: 100%; padding: .7rem; background: #3b82f6; color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 1.5rem; transition: background .15s; }
        .btn:hover { background: #2563eb; }
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: .75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: .875rem; }
        .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 1.5rem; border-radius: 8px; text-align: center; }
        .alert-success h2 { margin: 0 0 .5rem; }
        .alert-success a { color: #2563eb; font-weight: 600; }
        small { color: #94a3b8; font-size: .78rem; display: block; margin-top: .2rem; }
        .row { display: flex; gap: 12px; }
        .row .form-group { flex: 1; }
    </style>
</head>
<body>
<div class="card">
    <h1>⚙ Install Crusader Works</h1>

    <?php if ($success): ?>
    <div class="alert-success">
        <h2>✅ Installation complete!</h2>
        <p>Your database and admin account have been set up.</p>
        <p><strong>Delete the <code>/install/</code> folder</strong> from your server for security, then:</p>
        <p><a href="<?= htmlspecialchars($loginUrl) ?>">→ Go to Login</a></p>
    </div>

    <?php else: ?>

    <?php if ($error): ?>
    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="_install_token" value="<?= htmlspecialchars($install_token) ?>">

        <h3>Database</h3>
        <div class="row">
            <div class="form-group">
                <label>Database Host</label>
                <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
            </div>
            <div class="form-group">
                <label>Database Name</label>
                <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" required placeholder="crusader_works">
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Database User</label>
                <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Database Password</label>
                <input type="password" name="db_pass">
                <small>Leave blank if no password</small>
            </div>
        </div>

        <h3>Application</h3>
        <div class="form-group">
            <label>Application Name</label>
            <input type="text" name="app_name" value="<?= htmlspecialchars($_POST['app_name'] ?? 'Crusader Works') ?>" required>
        </div>
        <div class="form-group">
            <label>Application URL</label>
            <input type="url" name="app_url" value="<?= htmlspecialchars($_POST['app_url'] ?? (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?>" required placeholder="https://yourdomain.com">
            <small>No trailing slash. This is used to generate links in emails and navigation.</small>
        </div>

        <h3>Administrator Account</h3>
        <div class="row">
            <div class="form-group">
                <label>Admin Name</label>
                <input type="text" name="admin_name" value="<?= htmlspecialchars($_POST['admin_name'] ?? '') ?>" placeholder="John Smith">
            </div>
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Admin Password</label>
            <input type="password" name="admin_password" required minlength="8" placeholder="Minimum 8 characters">
        </div>

        <button type="submit" class="btn">Install Now →</button>
    </form>

    <?php endif; ?>
</div>
</body>
</html>
