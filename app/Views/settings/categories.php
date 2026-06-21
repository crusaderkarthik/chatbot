<div class="page-header"><h1>Ticket Categories</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="flex-1">
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="<?= url('settings/categories') ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="text" name="name" required class="form-control form-inline" placeholder="Category name">
                    <input type="text" name="description" class="form-control form-inline" placeholder="Description (optional)">
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="data-table">
                    <thead><tr><th>Name</th><th>Description</th><th>Tickets</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($cats as $c): ?>
                    <tr>
                        <td><?= e($c['name']) ?></td>
                        <td class="text-muted"><?= e($c['description']) ?></td>
                        <td><?= $c['ticket_count'] ?></td>
                        <td>
                            <form method="POST" action="<?= url('settings/categories/' . $c['id'] . '/delete') ?>" onsubmit="return confirm('Delete?')">
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
