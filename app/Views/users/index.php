<div class="page-header">
    <h1>Users</h1>
    <a href="<?= url('users/create') ?>" class="btn btn-primary">+ New User</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>
                    <?php if ($u['avatar']): ?>
                        <img src="<?= asset('uploads/' . $u['avatar']) ?>" class="avatar-xs" alt="">
                    <?php else: ?>
                        <div class="avatar-placeholder-xs inline"><?= strtoupper($u['name'][0]) ?></div>
                    <?php endif; ?>
                    <?= e($u['name']) ?>
                </td>
                <td><?= e($u['email']) ?></td>
                <td><span class="badge role-<?= $u['role'] ?>"><?= ucfirst(str_replace('_',' ',$u['role'])) ?></span></td>
                <td><?= e($u['department']) ?></td>
                <td><span class="badge status-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
                <td>
                    <a href="<?= url('users/' . $u['id'] . '/edit') ?>" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="POST" action="<?= url('users/' . $u['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this user?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
