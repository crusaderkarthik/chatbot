<div class="page-header">
    <h1><?= e($article['title']) ?></h1>
    <div>
        <?php if (Auth::can('manage_kb')): ?>
        <a href="<?= url('kb/' . $article['id'] . '/edit') ?>" class="btn btn-secondary">Edit</a>
        <?php endif; ?>
        <a href="<?= url('kb') ?>" class="btn btn-ghost">← Back</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="kb-meta mb-3">
            <span class="badge status-<?= $article['status'] ?>"><?= ucfirst($article['status']) ?></span>
            <span class="text-muted"><?= e($article['category_name'] ?? 'Uncategorized') ?></span>
            <span class="text-muted"><?= $article['views'] ?> views</span>
            <span class="text-muted">by <?= e($article['author_name']) ?></span>
            <?php if ($article['updated_at']): ?><span class="text-muted">Updated <?= ago($article['updated_at']) ?></span><?php endif; ?>
        </div>
        <div class="kb-content"><?= nl2br(e($article['content'])) ?></div>
    </div>
</div>
