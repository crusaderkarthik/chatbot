<div class="page-header">
    <h1>My Profile</h1>
</div>
<div class="two-col-layout">
    <div class="card mb-3">
        <div class="card-header">Profile Information</div>
        <div class="card-body">
            <form method="POST" action="<?= url('profile') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= e($user['name']) ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" value="<?= e($user['department']) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= e($user['phone']) ?>" class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group flex-1">
                        <label>Timezone</label>
                        <select name="timezone" class="form-control">
                            <?php foreach (DateTimeZone::listIdentifiers() as $tz): ?>
                            <option value="<?= $tz ?>" <?= $user['timezone']===$tz?'selected':'' ?>><?= $tz ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group flex-1">
                        <label>Theme</label>
                        <select name="theme" class="form-control">
                            <option value="light" <?= $user['theme']==='light'?'selected':'' ?>>Light</option>
                            <option value="dark" <?= $user['theme']==='dark'?'selected':'' ?>>Dark</option>
                        </select>
                    </div>
                </div>
                <h4>Notifications</h4>
                <?php $np = json_decode($user['notification_prefs'] ?? '{}', true) ?? []; ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="notif_email" value="1" <?= ($np['email']??true)?'checked':'' ?>> Email
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="notif_discord" value="1" <?= ($np['discord']??false)?'checked':'' ?>> Discord
                </label>
                <button type="submit" class="btn btn-primary mt-3">Save Profile</button>
            </form>
        </div>
    </div>

    <div>
        <div class="card mb-3">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <form method="POST" action="<?= url('profile/password') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>New Password (8+ chars)</label>
                        <input type="password" name="new_password" required minlength="8" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-secondary">Change Password</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Avatar</div>
            <div class="card-body">
                <?php if ($user['avatar']): ?>
                <img src="<?= asset('uploads/' . $user['avatar']) ?>" class="avatar-lg mb-2" alt="Avatar">
                <?php endif; ?>
                <form method="POST" action="<?= url('profile/avatar') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="file" name="avatar" accept="image/*" class="form-control mb-2">
                    <button type="submit" class="btn btn-secondary">Upload Avatar</button>
                </form>
            </div>
        </div>
    </div>
</div>
