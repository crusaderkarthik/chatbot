<div class="page-header"><h1>SMTP Settings</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="card flex-1">
        <div class="card-body">
            <form method="POST" action="<?= url('settings/smtp') ?>">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group flex-1"><label>SMTP Host</label><input type="text" name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>" class="form-control" placeholder="smtp.gmail.com"></div>
                    <div class="form-group"><label>Port</label><input type="number" name="smtp_port" value="<?= e($settings['smtp_port'] ?? '587') ?>" class="form-control" style="width:100px"></div>
                </div>
                <div class="form-row">
                    <div class="form-group flex-1"><label>Username</label><input type="text" name="smtp_user" value="<?= e($settings['smtp_user'] ?? '') ?>" class="form-control"></div>
                    <div class="form-group flex-1"><label>Password</label><input type="password" name="smtp_pass" value="<?= e($settings['smtp_pass'] ?? '') ?>" class="form-control"></div>
                </div>
                <div class="form-group"><label>From Address</label><input type="email" name="smtp_from" value="<?= e($settings['smtp_from'] ?? '') ?>" class="form-control" placeholder="support@yourcompany.com"></div>
                <button type="submit" class="btn btn-primary">Save SMTP</button>
            </form>
        </div>
    </div>
</div>
