<div class="page-header">
    <h1>New Article</h1>
    <a href="<?= url('kb') ?>" class="btn btn-ghost">← Back</a>
</div>
<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url('kb/create') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" required class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">— None —</option>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Content *</label>
                <textarea name="content" required class="form-control" rows="16"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Article</button>
        </form>
    </div>
</div>
