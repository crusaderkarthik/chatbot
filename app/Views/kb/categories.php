<div class="page-header">
    <h1>KB Categories</h1>
    <a href="<?= url('kb') ?>" class="btn btn-ghost">← Back to KB</a>
</div>
<div class="two-col-layout">
    <div class="card">
        <div class="card-header">Add Category</div>
        <div class="card-body">
            <form method="POST" action="<?= url('kb/categories') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Parent Category (optional)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">— None (top-level) —</option>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Existing Categories</div>
        <div class="card-body">
            <?php foreach ($cats as $c): ?>
            <div class="cat-item">
                <strong><?= e($c['name']) ?></strong>
                <?php foreach ($c['children'] ?? [] as $child): ?>
                <div class="subcat-item">↳ <?= e($child['name']) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
