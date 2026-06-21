<?php
session_start();

require_once APP . '/Core/helpers.php';
require_once APP . '/Core/Database.php';
require_once APP . '/Core/Model.php';
require_once APP . '/Core/Controller.php';
require_once APP . '/Core/Auth.php';
require_once APP . '/Core/Router.php';

$configFile = ROOT . '/config/config.php';

// Redirect to installer if not configured
if (!file_exists($configFile)) {
    if (!defined('BASE_URL')) define('BASE_URL', '');
    $url = $_GET['url'] ?? '';
    if (!str_starts_with(trim($url, '/'), 'install')) {
        header('Location: /install');
        exit;
    }
    // install/index.php handles both GET and POST itself
    require_once ROOT . '/install/index.php';
    exit;
}

require_once $configFile;

$router = new Router();
require_once APP . '/routes.php';
$router->dispatch($_GET['url'] ?? '');
