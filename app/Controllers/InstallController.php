<?php
require_once APP . '/Core/Controller.php';

class InstallController extends Controller {
    public function index(): void {
        if (file_exists(ROOT . '/config/config.php')) {
            redirect('dashboard');
        }
        $this->view('install.index', ['title' => 'Install Crusader Works']);
    }

    public function run(): void {
        if (file_exists(ROOT . '/config/config.php')) {
            redirect('dashboard');
        }
        verify_csrf();

        $dbHost  = trim($_POST['db_host'] ?? 'localhost');
        $dbName  = trim($_POST['db_name'] ?? '');
        $dbUser  = trim($_POST['db_user'] ?? '');
        $dbPass  = $_POST['db_pass'] ?? '';
        $appName = trim($_POST['app_name'] ?? 'Crusader Works');
        $appUrl  = rtrim(trim($_POST['app_url'] ?? ''), '/');
        $adminName  = trim($_POST['admin_name'] ?? '');
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminPass  = $_POST['admin_password'] ?? '';

        if (!$dbName || !$dbUser || !$adminEmail || !$adminPass) {
            $this->view('install.index', ['title' => 'Install', 'error' => 'All fields required.']);
            return;
        }

        try {
            $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$dbName`");

            $sql = file_get_contents(ROOT . '/install/schema.sql');
            $pdo->exec($sql);

            // Create admin user
            $hash = password_hash($adminPass, PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO users (name, email, password, role, status, timezone, theme, permissions, notification_prefs, created_at)
                VALUES (?,?,?,'admin','active','UTC','light','[]','{}',NOW())")
                ->execute([$adminName, $adminEmail, $hash]);

            // Default settings
            $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?")->execute(['app_name', $appName, $appName]);
            $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?")->execute(['app_url', $appUrl, $appUrl]);

            // Write config
            $config = "<?php\ndefine('DB_HOST', " . var_export($dbHost, true) . ");\n"
                    . "define('DB_NAME', " . var_export($dbName, true) . ");\n"
                    . "define('DB_USER', " . var_export($dbUser, true) . ");\n"
                    . "define('DB_PASS', " . var_export($dbPass, true) . ");\n"
                    . "define('BASE_URL', " . var_export($appUrl, true) . ");\n"
                    . "define('APP_NAME', " . var_export($appName, true) . ");\n";

            file_put_contents(ROOT . '/config/config.php', $config);

            $this->view('install.success', ['title' => 'Installation Complete']);
        } catch (\Throwable $e) {
            $this->view('install.index', ['title' => 'Install', 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
