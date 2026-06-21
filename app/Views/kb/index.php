<div class="page-header">
    <h1>Knowledge Base</h1>
    <?php if (Auth::can('manage_kb')): ?>
    <a href="<?= url('kb/create') ?>" class="btn btn-primary">+ New Article</a>
    <?php endif; ?>
</div>

<form method="GET" action="<?= url('kb') ?>" class="filter-bar mb-4">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search articles…" class="form-control">
    <button type="submit" class="btn btn-secondary">Search</button>
</form>

<div class="kb-layout">
    <div class="kb-sidebar">
        <h3>Categories</h3>
        <?php foreach ($cats as $cat): ?>
        <div class="kb-cat">
            <strong><?= e($cat['name']) ?></strong>
            <?php foreach ($cat['children'] ?? [] as $child): ?>
            <div class="kb-subcat"><?= e($child['name']) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php if (Auth::can('manage_kb')): ?>
        <a href="<?= url('kb/categories') ?>" class="btn btn-ghost btn-sm mt-2">Manage Categories</a>
        <?php endif; ?>
    </div>
    <div class="kb-articles">
        <?php foreach ($articles as $a): ?>
        <div class="kb-article-card">
            <h3><a href="<?= url('kb/' . $a['id']) ?>"><?= e($a['title']) ?></a></h3>
            <div class="kb-meta">
                <span class="badge status-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span>
                <span class="text-muted"><?= e($a['category_name'] ?? 'Uncategorized') ?></span>
                <span class="text-muted"><?= $a['views'] ?> views</span>
                <span class="text-muted">by <?= e($a['author_name']) ?></span>
            </div>
            <p class="kb-excerpt"><?= e(substr(strip_tags($a['content']), 0, 160)) ?>…</p>
        </div>
        <?php endforeach; ?>
        <?php if (empty($articles)): ?>
        <p class="text-muted">No articles found.</p>
        <?php endif; ?>
    </div>
</div>
