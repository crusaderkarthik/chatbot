<div class="page-header"><h1>Branding</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="card flex-1">
        <div class="card-body">
            <form method="POST" action="<?= url('settings/branding') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Application Name</label>
                    <input type="text" name="app_name" value="<?= e($settings['app_name'] ?? 'Crusader Works') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Primary Color</label>
                    <input type="color" name="primary_color" value="<?= e($settings['primary_color'] ?? '#3b82f6') ?>" class="form-control-color">
                </div>
                <div class="form-group">
                    <label>Logo (PNG/JPG/SVG)</label>
                    <?php if ($settings['logo'] ?? ''): ?>
                    <div class="mb-2"><img src="<?= asset($settings['logo']) ?>" style="max-height:60px" alt="Logo"></div>
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/*" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update Branding</button>
            </form>
        </div>
    </div>
</div>
