<div class="page-header">
    <h1>Edit Article</h1>
    <div>
        <form method="POST" action="<?= url('kb/' . $article['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this article?')">
            <?= csrf_field() ?>
            <button class="btn btn-danger">Delete</button>
        </form>
        <a href="<?= url('kb/' . $article['id']) ?>" class="btn btn-ghost">← Cancel</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url('kb/' . $article['id'] . '/edit') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" value="<?= e($article['title']) ?>" required class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">— None —</option>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $article['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <?php foreach (['draft','published','archived'] as $s): ?>
                        <option value="<?= $s ?>" <?= $article['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Content *</label>
                <textarea name="content" required class="form-control" rows="16"><?= e($article['content']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Article</button>
        </form>
    </div>
</div>
