<div class="page-header"><h1>Discord Integration</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="card flex-1">
        <div class="card-body">
            <form method="POST" action="<?= url('settings/discord') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Discord Webhook URL</label>
                    <input type="url" name="discord_webhook" value="<?= e($settings['discord_webhook'] ?? '') ?>" class="form-control" placeholder="https://discord.com/api/webhooks/…">
                </div>
                <div class="form-group">
                    <label>Notification Triggers</label>
                    <div class="perms-grid">
                        <?php foreach (['created','assigned','replied','status','closed'] as $t): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="triggers[]" value="<?= $t ?>" <?= in_array($t, $triggers)?'checked':'' ?>>
                            <?= ucfirst($t) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Discord Settings</button>
            </form>
        </div>
    </div>
</div>
