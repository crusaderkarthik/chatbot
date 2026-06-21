<div class="page-header"><h1>Saved Replies</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="flex-1">
        <div class="card mb-4">
            <div class="card-header">New Saved Reply</div>
            <div class="card-body">
                <form method="POST" action="<?= url('settings/saved-replies') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group"><label>Title</label><input type="text" name="title" required class="form-control"></div>
                    <div class="form-group"><label>Content</label><textarea name="content" required class="form-control" rows="4"></textarea></div>
                    <?php if (Auth::role() === 'admin'): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_global" value="1"> Make global (visible to all agents)
                    </label>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary mt-2">Save Reply</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="data-table">
                    <thead><tr><th>Title</th><th>Global</th><th>Preview</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($replies as $r): ?>
                    <tr>
                        <td><?= e($r['title']) ?></td>
                        <td><?= $r['is_global'] ? '✓' : '—' ?></td>
                        <td class="text-muted"><?= e(substr($r['content'], 0, 60)) ?>…</td>
                        <td>
                            <form method="POST" action="<?= url('settings/saved-replies/' . $r['id'] . '/delete') ?>" onsubmit="return confirm('Delete?')">
                                <?= csrf_field() ?><button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
