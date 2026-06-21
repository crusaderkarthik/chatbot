<div class="page-header"><h1>General Settings</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="card flex-1">
        <div class="card-body">
            <form method="POST" action="<?= url('settings') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Application Name</label>
                    <input type="text" name="app_name" value="<?= e($settings['app_name'] ?? 'Crusader Works') ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Application URL</label>
                    <input type="url" name="app_url" value="<?= e($settings['app_url'] ?? '') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Ticket Prefix</label>
                    <input type="text" name="ticket_prefix" value="<?= e($settings['ticket_prefix'] ?? 'CW') ?>" class="form-control" maxlength="5">
                </div>
                <div class="form-group">
                    <label>Default Timezone</label>
                    <select name="timezone" class="form-control">
                        <?php foreach (DateTimeZone::listIdentifiers() as $tz): ?>
                        <option value="<?= $tz ?>" <?= ($settings['timezone']??'UTC')===$tz?'selected':'' ?>><?= $tz ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>
