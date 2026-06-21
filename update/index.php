<?php
/**
 * Crusader Works — Web Updater
 * Access at: yoursite.com/update
 *
 * Password is set below. Change it after first use.
 */
define('UPDATE_PASSWORD', 'CrusaderUpdate2024!');

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() === PHP_SESSION_NONE) session_start();

$rootDir     = dirname(__DIR__);
$configFile  = $rootDir . '/config/config.php';
$migrDir     = __DIR__ . '/migrations';
$trackingKey = 'ran_migrations';

// --- Auth ---
if (isset($_POST['logout'])) {
    unset($_SESSION['updater_auth']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
if (isset($_POST['update_password'])) {
    if ($_POST['update_password'] === UPDATE_PASSWORD) {
        $_SESSION['updater_auth'] = true;
    } else {
        $authError = 'Wrong password.';
    }
}
$authed = !empty($_SESSION['updater_auth']);

// --- DB connection (only if app is configured) ---
$db  = null;
$dbError = '';
if ($authed && file_exists($configFile)) {
    require_once $configFile;
    try {
        $db = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        // Ensure tracking table exists
        $db->exec("CREATE TABLE IF NOT EXISTS update_migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            ran_at DATETIME NOT NULL DEFAULT NOW()
        )");
    } catch (PDOException $e) {
        $dbError = 'DB connection failed: ' . $e->getMessage();
        $db = null;
    }
}

// --- Run migrations ---
$migResults = [];
if ($authed && isset($_POST['run_migrations']) && $db) {
    $files = glob($migrDir . '/*.sql');
    sort($files);

    $ran = $db->query("SELECT filename FROM update_migrations")->fetchAll(PDO::FETCH_COLUMN);
    $ranSet = array_flip($ran);

    foreach ($files as $file) {
        $name = basename($file);
        if (isset($ranSet[$name])) {
            $migResults[] = ['file' => $name, 'status' => 'skipped', 'msg' => 'Already applied'];
            continue;
        }
        try {
            $sql = file_get_contents($file);
            $stmts = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($stmts as $stmt) {
                if ($stmt !== '') $db->exec($stmt);
            }
            $db->prepare("INSERT INTO update_migrations (filename) VALUES (?)")->execute([$name]);
            $migResults[] = ['file' => $name, 'status' => 'ok', 'msg' => 'Applied successfully'];
        } catch (Throwable $e) {
            $migResults[] = ['file' => $name, 'status' => 'error', 'msg' => $e->getMessage()];
        }
    }
    if (empty($files)) {
        $migResults[] = ['file' => '—', 'status' => 'skipped', 'msg' => 'No migration files found in /update/migrations/'];
    }
}

// --- List pending migrations ---
$pendingCount = 0;
$allMigrations = [];
if ($authed && $db) {
    $files = glob($migrDir . '/*.sql');
    sort($files);
    $ran = $db->query("SELECT filename, ran_at FROM update_migrations ORDER BY ran_at")->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($files as $file) {
        $name = basename($file);
        $allMigrations[] = [
            'name'   => $name,
            'status' => isset($ran[$name]) ? 'applied' : 'pending',
            'ran_at' => $ran[$name] ?? null,
        ];
        if (!isset($ran[$name])) $pendingCount++;
    }
}

// --- System info ---
$sysInfo = [];
if ($authed) {
    $sysInfo['PHP Version']   = phpversion();
    $sysInfo['App Configured'] = file_exists($configFile) ? 'Yes' : 'No (config/config.php missing)';
    $sysInfo['DB Connected']   = $db ? 'Yes' : 'No' . ($dbError ? " — $dbError" : '');
    $sysInfo['Migrations Pending'] = $pendingCount;
    $sysInfo['Web Server']     = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    $sysInfo['PHP Memory Limit'] = ini_get('memory_limit');
    $sysInfo['Max Upload Size']  = ini_get('upload_max_filesize');
    $sysInfo['Disk Free']        = function_exists('disk_free_space') ? round(disk_free_space($rootDir)/1024/1024/1024, 2) . ' GB' : 'N/A';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Crusader Works — Updater</title>
<style>
*,*::before,*::after{box-sizing:border-box}
body{font-family:system-ui,sans-serif;background:#0f172a;color:#e2e8f0;margin:0;padding:2rem 1rem;min-height:100vh}
.wrap{max-width:860px;margin:0 auto}
h1{font-size:1.5rem;color:#f8fafc;margin:0 0 .25rem}
.subtitle{color:#94a3b8;font-size:.875rem;margin-bottom:2rem}
.card{background:#1e293b;border:1px solid #334155;border-radius:10px;margin-bottom:1.5rem;overflow:hidden}
.card-header{padding:.75rem 1.25rem;background:#273549;font-weight:600;font-size:.875rem;color:#cbd5e1;border-bottom:1px solid #334155;display:flex;align-items:center;justify-content:space-between}
.card-body{padding:1.25rem}
label{display:block;font-size:.82rem;font-weight:600;color:#94a3b8;margin-bottom:.35rem}
input[type=password]{width:100%;padding:.6rem .85rem;background:#0f172a;border:1px solid #475569;border-radius:6px;color:#f1f5f9;font-size:.9rem;outline:none}
input[type=password]:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.2)}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border:none;border-radius:6px;font-size:.875rem;font-weight:600;cursor:pointer;text-decoration:none;transition:background .15s}
.btn-primary{background:#3b82f6;color:#fff}.btn-primary:hover{background:#2563eb}
.btn-success{background:#16a34a;color:#fff}.btn-success:hover{background:#15803d}
.btn-ghost{background:transparent;color:#94a3b8;border:1px solid #475569}.btn-ghost:hover{color:#f1f5f9;border-color:#64748b}
.btn-danger{background:#dc2626;color:#fff}.btn-danger:hover{background:#b91c1c}
.alert-error{background:#450a0a;border:1px solid #7f1d1d;color:#fca5a5;padding:.75rem 1rem;border-radius:6px;margin-bottom:1rem;font-size:.875rem}
table{width:100%;border-collapse:collapse;font-size:.85rem}
th{text-align:left;padding:.5rem .75rem;color:#94a3b8;font-weight:600;border-bottom:1px solid #334155}
td{padding:.6rem .75rem;border-bottom:1px solid #1e293b;vertical-align:top}
.badge{display:inline-block;padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:600}
.badge-ok{background:#14532d;color:#4ade80}
.badge-error{background:#450a0a;color:#f87171}
.badge-skipped{background:#1e3a5f;color:#93c5fd}
.badge-pending{background:#422006;color:#fb923c}
.badge-applied{background:#14532d;color:#4ade80}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:.5rem}
.info-row{display:flex;justify-content:space-between;padding:.45rem .75rem;background:#0f172a;border-radius:6px;font-size:.82rem}
.info-label{color:#94a3b8}
.info-value{color:#f1f5f9;font-weight:500}
.login-wrap{max-width:400px;margin:4rem auto}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem}
</style>
</head>
<body>
<div class="wrap">

<?php if (!$authed): ?>

<div class="login-wrap">
    <h1>⚙ Crusader Works</h1>
    <p class="subtitle">Web Updater — enter the updater password to continue.</p>

    <?php if (!empty($authError)): ?>
    <div class="alert-error"><?= htmlspecialchars($authError) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Updater Login</div>
        <div class="card-body">
            <form method="POST">
                <div style="margin-bottom:1rem">
                    <label>Updater Password</label>
                    <input type="password" name="update_password" autofocus required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Unlock →</button>
            </form>
        </div>
    </div>
</div>

<?php else: ?>

<div class="topbar">
    <div>
        <h1>⚙ Crusader Works — Updater</h1>
        <p class="subtitle" style="margin:0">Database migration runner &amp; system info</p>
    </div>
    <form method="POST" style="margin:0">
        <button name="logout" value="1" class="btn btn-ghost">Logout</button>
    </form>
</div>

<!-- System Info -->
<div class="card">
    <div class="card-header">System Info</div>
    <div class="card-body">
        <div class="info-grid">
        <?php foreach ($sysInfo as $k => $v): ?>
        <div class="info-row">
            <span class="info-label"><?= htmlspecialchars($k) ?></span>
            <span class="info-value"><?= htmlspecialchars((string)$v) ?></span>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if ($dbError): ?>
<div class="alert-error">⚠ <?= htmlspecialchars($dbError) ?></div>
<?php endif; ?>

<!-- Migration Results -->
<?php if ($migResults): ?>
<div class="card">
    <div class="card-header">Migration Results</div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>File</th><th>Status</th><th>Message</th></tr></thead>
            <tbody>
            <?php foreach ($migResults as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['file']) ?></td>
                <td><span class="badge badge-<?= $r['status'] ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                <td><?= htmlspecialchars($r['msg']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Migrations -->
<div class="card">
    <div class="card-header">
        Database Migrations
        <?php if ($pendingCount > 0): ?>
        <span class="badge badge-pending"><?= $pendingCount ?> pending</span>
        <?php else: ?>
        <span class="badge badge-applied">Up to date</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if ($db): ?>

        <?php if ($allMigrations): ?>
        <table style="margin-bottom:1rem">
            <thead><tr><th>Migration File</th><th>Status</th><th>Applied At</th></tr></thead>
            <tbody>
            <?php foreach ($allMigrations as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><span class="badge badge-<?= $m['status'] === 'applied' ? 'applied' : 'pending' ?>"><?= $m['status'] ?></span></td>
                <td><?= $m['ran_at'] ? htmlspecialchars($m['ran_at']) : '—' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color:#94a3b8;margin-bottom:1rem">No migration files in <code>/update/migrations/</code> yet.</p>
        <?php endif; ?>

        <?php if ($pendingCount > 0 || empty($allMigrations)): ?>
        <form method="POST" onsubmit="return confirm('Run all pending migrations now?')">
            <button name="run_migrations" value="1" class="btn btn-success">▶ Run All Pending Migrations</button>
        </form>
        <?php else: ?>
        <form method="POST">
            <button name="run_migrations" value="1" class="btn btn-ghost">Re-check migrations</button>
        </form>
        <?php endif; ?>

        <?php else: ?>
        <p style="color:#f87171">Cannot connect to the database. Check your config/config.php.</p>
        <?php endif; ?>
    </div>
</div>

<!-- How to add migrations -->
<div class="card">
    <div class="card-header">How to Add a Migration</div>
    <div class="card-body" style="font-size:.85rem;color:#94a3b8;line-height:1.7">
        <ol style="padding-left:1.25rem;margin:0">
            <li>Create a <code>.sql</code> file in <code>/update/migrations/</code> — e.g. <code>002_add_column.sql</code></li>
            <li>Name it with a numeric prefix so migrations run in order.</li>
            <li>Write your SQL statements (one per line, each ending in <code>;</code>).</li>
            <li>Come back to this page and click <strong>Run All Pending Migrations</strong>.</li>
        </ol>
        <p style="margin-top:1rem">Each file is only run once. The system tracks which migrations have been applied in the <code>update_migrations</code> database table.</p>
    </div>
</div>

<?php endif; ?>
</div>
</body>
</html>
