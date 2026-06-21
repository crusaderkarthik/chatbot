<?php
require_once APP . '/Models/Setting.php';
$user = Auth::user();
$theme = $user['theme'] ?? 'light';
$appName = defined('APP_NAME') ? APP_NAME : 'Crusader Works';
$logo = Setting::get('logo');
$primaryColor = Setting::get('primary_color', '#3b82f6');
$activeTimer = $activeTimer ?? null;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title ?? 'Dashboard') ?> — <?= e($appName) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>:root { --primary: <?= e($primaryColor) ?>; }</style>
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <?php if ($logo): ?>
                <img src="<?= asset($logo) ?>" alt="Logo" class="brand-logo">
            <?php else: ?>
                <span class="brand-text"><?= e($appName) ?></span>
            <?php endif; ?>
        </div>

        <ul class="nav-menu">
            <li><a href="<?= url('dashboard') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') || $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Dashboard
            </a></li>
            <li><a href="<?= url('tickets') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/tickets') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-1 14H5V8h14v10z"/></svg>
                Tickets
            </a></li>
            <li><a href="<?= url('kb') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/kb') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/></svg>
                Knowledge Base
            </a></li>
            <li><a href="<?= url('analytics') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/analytics') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5 1.41-1.41L12 14.17l7.59-7.59L21 8l-9 9z"/></svg>
                Analytics
            </a></li>
            <?php if (Auth::can('manage_users')): ?>
            <li><a href="<?= url('users') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/users') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                Users
            </a></li>
            <?php endif; ?>
            <?php if (Auth::role() === 'admin'): ?>
            <li class="nav-section">Settings</li>
            <li><a href="<?= url('settings') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/settings') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                Settings
            </a></li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <?php if ($activeTimer): ?>
            <div class="timer-badge" id="sidebar-timer">
                <span class="timer-dot"></span>
                <span id="sidebar-timer-display">⏱ Active Timer</span>
            </div>
            <?php endif; ?>
            <a href="<?= url('profile') ?>" class="user-card">
                <?php if ($user['avatar']): ?>
                    <img src="<?= asset('uploads/' . $user['avatar']) ?>" class="avatar-sm" alt="Avatar">
                <?php else: ?>
                    <div class="avatar-placeholder-sm"><?= strtoupper($user['name'][0]) ?></div>
                <?php endif; ?>
                <div class="user-info">
                    <span class="user-name"><?= e($user['name']) ?></span>
                    <span class="user-role"><?= e($user['role']) ?></span>
                </div>
            </a>
            <a href="<?= url('auth/logout') ?>" class="logout-link">Logout</a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="main-content">
        <?php
        $flash_success = flash('success');
        $flash_error   = flash('error');
        ?>
        <?php if ($flash_success): ?>
            <div class="alert alert-success"><?= e($flash_success) ?></div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
            <div class="alert alert-error"><?= e($flash_error) ?></div>
        <?php endif; ?>

        <?php require APP . '/Views/' . str_replace('.', '/', $content_view) . '.php'; ?>
    </main>
</div>

<script src="<?= asset('js/app.js') ?>"></script>
<?php if ($activeTimer): ?>
<script>
(function() {
    var elapsed = <?= time() - strtotime($activeTimer['started_at']) ?>;
    function fmt(s) {
        var h = Math.floor(s/3600), m = Math.floor((s%3600)/60), sec = s%60;
        return (h?h+'h ':'') + m+'m ' + sec+'s';
    }
    var el = document.getElementById('sidebar-timer-display');
    if (el) {
        setInterval(function() { elapsed++; el.textContent = '⏱ ' + fmt(elapsed); }, 1000);
    }
})();
</script>
<?php endif; ?>
</body>
</html>
