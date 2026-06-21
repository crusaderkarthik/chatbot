<?php
require_once APP . '/Core/Model.php';

class Comment extends Model {
    protected static string $table = 'comments';

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO comments (ticket_id, user_id, message, is_internal, created_at) VALUES (?,?,?,?,NOW())',
            [$d['ticket_id'], $d['user_id'], $d['message'], $d['is_internal'] ? 1 : 0]
        );
    }

    public static function forTicket(int $ticketId): array {
        return Database::fetchAll(
            'SELECT c.*, u.name as author_name, u.avatar as author_avatar, u.role as author_role
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.ticket_id = ?
             ORDER BY c.created_at ASC',
            [$ticketId]
        );
    }
}
