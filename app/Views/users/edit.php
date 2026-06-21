<?php
$allPerms = ['view_tickets','view_all_tickets','create_tickets','assign_tickets','close_tickets','delete_tickets','archive_tickets','merge_tickets','create_alias','log_time','manage_kb','manage_users','manage_settings'];
$userPerms = json_decode($user['permissions'] ?? '[]', true) ?? [];
?>
<div class="page-header">
    <h1>Edit User: <?= e($user['name']) ?></h1>
    <a href="<?= url('users') ?>" class="btn btn-ghost">← Back</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url('users/' . $user['id'] . '/edit') ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Name *</label>
                    <input type="text" name="name" value="<?= e($user['name']) ?>" required class="form-control">
                </div>
                <div class="form-group flex-1">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= e($user['email']) ?>" required class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Role</label>
                    <select name="role" class="form-control" id="role-select" onchange="togglePerms(this.value)">
                        <?php foreach (['agent','sub_admin','admin'] as $r): ?>
                        <option value="<?= $r ?>" <?= $user['role']===$r?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$r)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <?php foreach (['active','inactive','suspended'] as $s): ?>
                        <option value="<?= $s ?>" <?= $user['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Department</label>
                    <input type="text" name="department" value="<?= e($user['department']) ?>" class="form-control">
                </div>
                <div class="form-group flex-1">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= e($user['phone']) ?>" class="form-control">
                </div>
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

            <div id="perms-section">
                <h3>Permissions</h3>
                <div class="perms-grid">
                    <?php foreach ($allPerms as $perm): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="permissions[]" value="<?= $perm ?>"
                            <?= in_array($perm, $userPerms)?'checked':'' ?>>
                        <?= ucwords(str_replace('_',' ',$perm)) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <h3>Notifications</h3>
            <?php $notifPrefs = json_decode($user['notification_prefs'] ?? '{}', true) ?? []; ?>
            <label class="checkbox-label">
                <input type="checkbox" name="notif_email" value="1" <?= ($notifPrefs['email'] ?? true)?'checked':'' ?>>
                Email Notifications
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="notif_discord" value="1" <?= ($notifPrefs['discord'] ?? false)?'checked':'' ?>>
                Discord Notifications
            </label>

            <button type="submit" class="btn btn-primary mt-3">Update User</button>
        </form>
    </div>
</div>
<script>
function togglePerms(role) {
    document.getElementById('perms-section').style.display = role === 'admin' ? 'none' : 'block';
}
togglePerms(document.getElementById('role-select').value);
</script>
