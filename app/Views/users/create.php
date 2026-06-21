<?php $allPerms = ['view_tickets','view_all_tickets','create_tickets','assign_tickets','close_tickets','delete_tickets','archive_tickets','merge_tickets','create_alias','log_time','manage_kb','manage_users','manage_settings']; ?>
<div class="page-header">
    <h1>New User</h1>
    <a href="<?= url('users') ?>" class="btn btn-ghost">← Back</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url('users/create') ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Name *</label>
                    <input type="text" name="name" required class="form-control">
                </div>
                <div class="form-group flex-1">
                    <label>Email *</label>
                    <input type="email" name="email" required class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="8" class="form-control">
                </div>
                <div class="form-group flex-1">
                    <label>Role *</label>
                    <select name="role" class="form-control" id="role-select" onchange="togglePerms(this.value)">
                        <option value="agent">Agent</option>
                        <option value="sub_admin">Sub-Admin</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control">
                </div>
                <div class="form-group flex-1">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label>Timezone</label>
                    <select name="timezone" class="form-control">
                        <?php foreach (DateTimeZone::listIdentifiers() as $tz): ?>
                        <option value="<?= $tz ?>" <?= $tz==='UTC'?'selected':'' ?>><?= $tz ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="perms-section">
                <h3>Permissions</h3>
                <div class="perms-grid">
                    <?php foreach ($allPerms as $perm): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="permissions[]" value="<?= $perm ?>">
                        <?= ucwords(str_replace('_',' ',$perm)) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Create User</button>
        </form>
    </div>
</div>
<script>
function togglePerms(role) {
    document.getElementById('perms-section').style.display = role === 'admin' ? 'none' : 'block';
}
togglePerms(document.getElementById('role-select').value);
</script>
