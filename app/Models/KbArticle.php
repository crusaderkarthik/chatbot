<?php
require_once APP . '/Core/Model.php';

class KbArticle extends Model {
    protected static string $table = 'kb_articles';

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO kb_articles (title, content, status, category_id, author_id, created_at) VALUES (?,?,?,?,?,NOW())',
            [$d['title'], $d['content'], $d['status'], $d['category_id'] ?? null, $d['author_id']]
        );
    }

    public static function update(int $id, array $d): void {
        Database::execute(
            'UPDATE kb_articles SET title=?, content=?, status=?, category_id=?, updated_at=NOW() WHERE id=?',
            [$d['title'], $d['content'], $d['status'], $d['category_id'] ?? null, $id]
        );
    }

    public static function incrementViews(int $id): void {
        Database::execute('UPDATE kb_articles SET views = views + 1 WHERE id=?', [$id]);
    }

    public static function published(): array {
        return Database::fetchAll(
            "SELECT a.*, c.name as category_name, u.name as author_name
             FROM kb_articles a
             LEFT JOIN kb_categories c ON c.id=a.category_id
             JOIN users u ON u.id=a.author_id
             WHERE a.status='published'
             ORDER BY a.views DESC"
        );
    }

    public static function search(string $q): array {
        $like = '%' . $q . '%';
        return Database::fetchAll(
            "SELECT * FROM kb_articles WHERE status='published' AND (title LIKE ? OR content LIKE ?) ORDER BY views DESC",
            [$like, $like]
        );
    }

    public static function full(int $id): ?array {
        return Database::fetch(
            'SELECT a.*, c.name as category_name, u.name as author_name
             FROM kb_articles a
             LEFT JOIN kb_categories c ON c.id=a.category_id
             JOIN users u ON u.id=a.author_id
             WHERE a.id=?',
            [$id]
        );
    }
}
