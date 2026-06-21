<div class="page-header"><h1>Ticket Templates</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="flex-1">
        <div class="card mb-4">
            <div class="card-header">Add Template</div>
            <div class="card-body">
                <form method="POST" action="<?= url('settings/templates') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group"><label>Template Name</label><input type="text" name="name" required class="form-control"></div>
                    <div class="form-group"><label>Subject</label><input type="text" name="subject" required class="form-control"></div>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">— None —</option>
                                <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label>Priority</label>
                            <select name="priority" class="form-control">
                                <?php foreach (['low','medium','high','critical'] as $p): ?><option value="<?= $p ?>"><?= ucfirst($p) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label>Message</label><textarea name="message" required class="form-control" rows="5"></textarea></div>
                    <button type="submit" class="btn btn-primary">Add Template</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="data-table">
                    <thead><tr><th>Name</th><th>Subject</th><th>Priority</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($templates as $t): ?>
                    <tr>
                        <td><?= e($t['name']) ?></td>
                        <td><?= e($t['subject']) ?></td>
                        <td><span class="badge priority-<?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span></td>
                        <td>
                            <form method="POST" action="<?= url('settings/templates/' . $t['id'] . '/delete') ?>" onsubmit="return confirm('Delete?')">
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
