<div class="page-header"><h1>SLA Policies</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="flex-1">
        <div class="card mb-4">
            <div class="card-header">Add / Update Policy</div>
            <div class="card-body">
                <form method="POST" action="<?= url('settings/sla') ?>">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label>Priority</label>
                            <select name="priority" class="form-control">
                                <?php foreach (['low','medium','high','critical'] as $p): ?>
                                <option value="<?= $p ?>"><?= ucfirst($p) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label>Response Time (hours)</label>
                            <input type="number" name="response_hours" min="1" required class="form-control">
                        </div>
                        <div class="form-group flex-1">
                            <label>Resolution Time (hours)</label>
                            <input type="number" name="resolution_hours" min="1" required class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Policy</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="data-table">
                    <thead><tr><th>Priority</th><th>Response (h)</th><th>Resolution (h)</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($policies as $p): ?>
                    <tr>
                        <td><span class="badge priority-<?= $p['priority'] ?>"><?= ucfirst($p['priority']) ?></span></td>
                        <td><?= $p['response_hours'] ?>h</td>
                        <td><?= $p['resolution_hours'] ?>h</td>
                        <td>
                            <form method="POST" action="<?= url('settings/sla/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('Delete?')">
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
    </div>
</div>
