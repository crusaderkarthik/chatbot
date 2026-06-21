<div class="page-header"><h1>Tags</h1></div>
<div class="settings-layout">
    <?php require APP . '/Views/settings/_nav.php'; ?>
    <div class="flex-1">
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="<?= url('settings/tags') ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="text" name="name" required class="form-control form-inline" placeholder="Tag name">
                    <input type="color" name="color" value="#3b82f6" class="form-control-color">
                    <button type="submit" class="btn btn-primary">Add Tag</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="tags-display">
                    <?php foreach ($tags as $t): ?>
                    <span class="tag" style="background:<?= e($t['color']) ?>"><?= e($t['name']) ?></span>
                    <form method="POST" action="<?= url('settings/tags/' . $t['id'] . '/delete') ?>" style="display:inline">
                        <?= csrf_field() ?><button class="btn btn-danger btn-xs">×</button>
                    </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
