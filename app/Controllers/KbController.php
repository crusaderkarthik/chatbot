<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/KbArticle.php';
require_once APP . '/Models/KbCategory.php';

class KbController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $q        = $_GET['q'] ?? '';
        $articles = $q ? KbArticle::search($q) : KbArticle::published();
        $cats     = KbCategory::tree();
        $this->view('layouts.app', ['title' => 'Knowledge Base', 'content_view' => 'kb.index', 'articles' => $articles, 'cats' => $cats, 'q' => $q]);
    }

    public function create(): void {
        $this->requirePermission('manage_kb');
        $cats = KbCategory::all('name');
        $this->view('layouts.app', ['title' => 'New Article', 'content_view' => 'kb.create', 'cats' => $cats]);
    }

    public function store(): void {
        $this->requirePermission('manage_kb');
        verify_csrf();
        $id = KbArticle::create([
            'title'       => $this->sanitize($this->input('title')),
            'content'     => $this->input('content'),
            'status'      => $this->input('status', 'draft'),
            'category_id' => (int)$this->input('category_id') ?: null,
            'author_id'   => Auth::id(),
        ]);
        flash('success', 'Article created.');
        redirect('kb/' . $id);
    }

    public function show(string $id): void {
        $this->requireAuth();
        $article = KbArticle::full((int)$id);
        if (!$article) redirect('kb');
        KbArticle::incrementViews((int)$id);
        $this->view('layouts.app', ['title' => $article['title'], 'content_view' => 'kb.show', 'article' => $article]);
    }

    public function edit(string $id): void {
        $this->requirePermission('manage_kb');
        $article = KbArticle::full((int)$id);
        $cats    = KbCategory::all('name');
        $this->view('layouts.app', ['title' => 'Edit Article', 'content_view' => 'kb.edit', 'article' => $article, 'cats' => $cats]);
    }

    public function update(string $id): void {
        $this->requirePermission('manage_kb');
        verify_csrf();
        KbArticle::update((int)$id, [
            'title'       => $this->sanitize($this->input('title')),
            'content'     => $this->input('content'),
            'status'      => $this->input('status', 'draft'),
            'category_id' => (int)$this->input('category_id') ?: null,
        ]);
        flash('success', 'Article updated.');
        redirect('kb/' . $id);
    }

    public function delete(string $id): void {
        $this->requirePermission('manage_kb');
        verify_csrf();
        KbArticle::delete((int)$id);
        flash('success', 'Article deleted.');
        redirect('kb');
    }

    public function categories(): void {
        $this->requirePermission('manage_kb');
        $cats = KbCategory::tree();
        $this->view('layouts.app', ['title' => 'KB Categories', 'content_view' => 'kb.categories', 'cats' => $cats]);
    }

    public function storeCategory(): void {
        $this->requirePermission('manage_kb');
        verify_csrf();
        KbCategory::create($this->sanitize($this->input('name')), (int)$this->input('parent_id') ?: null);
        flash('success', 'Category created.');
        redirect('kb/categories');
    }
}
