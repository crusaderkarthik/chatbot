<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Installation Complete</title>
<style>
*, *::before, *::after { box-sizing: border-box; }
body { font-family: system-ui,sans-serif; background:#f0f4f8; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
.card { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.1); max-width:480px; text-align:center; }
h1 { color: #16a34a; }
.btn { display:inline-block; margin-top:1rem; padding:.65rem 2rem; background:#3b82f6; color:#fff; border-radius:6px; text-decoration:none; font-size:1rem; }
</style>
</head>
<body>
<div class="card">
    <h1>✅ Installation Complete!</h1>
    <p>Crusader Works has been successfully installed.</p>
    <p><strong>Please delete the <code>/install/</code> directory</strong> from your server for security.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/auth/login" class="btn">Go to Login →</a>
</div>
</body>
</html>
