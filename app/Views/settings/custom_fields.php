<div class="page-header"><h1>Custom Fields</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="flex-1">
        <div class="card mb-4">
            <div class="card-header">Add Field</div>
            <div class="card-body">
                <form method="POST" action="<?= url('settings/custom-fields') ?>">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label>Label</label>
                            <input type="text" name="label" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <?php foreach (['text','textarea','number','date','select','checkbox','radio'] as $t): ?>
                                <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Options (comma-separated, for select/radio)</label>
                        <input type="text" name="options" class="form-control" placeholder="Option 1, Option 2">
                    </div>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label>Category Scope (optional)</label>
                            <select name="category_id" class="form-control">
                                <option value="">— All categories —</option>
                                <?php foreach ($cats as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_required" value="1"> Required
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Field</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="data-table">
                    <thead><tr><th>Label</th><th>Type</th><th>Category</th><th>Required</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($fields as $f): ?>
                    <tr>
                        <td><?= e($f['label']) ?></td>
                        <td><?= ucfirst($f['type']) ?></td>
                        <td><?= $f['category_id'] ? '—' : 'All' ?></td>
                        <td><?= $f['is_required'] ? '✓' : '—' ?></td>
                        <td>
                            <form method="POST" action="<?= url('settings/custom-fields/' . $f['id'] . '/delete') ?>" onsubmit="return confirm('Delete?')">
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
