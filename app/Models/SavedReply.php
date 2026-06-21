<?php
require_once APP . '/Core/Model.php';

class SavedReply extends Model {
    protected static string $table = 'saved_replies';

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO saved_replies (title, content, is_global, user_id, created_at) VALUES (?,?,?,?,NOW())',
            [$d['title'], $d['content'], $d['is_global'] ? 1 : 0, $d['user_id']]
        );
    }

    public static function forUser(int $userId): array {
        return Database::fetchAll(
            'SELECT * FROM saved_replies WHERE is_global=1 OR user_id=? ORDER BY title',
            [$userId]
        );
    }
}
