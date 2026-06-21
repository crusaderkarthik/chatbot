<?php
require_once APP . '/Core/Model.php';

class TimeLog extends Model {
    protected static string $table = 'time_logs';

    public static function create(int $ticketId, int $userId, int $minutes, string $note = ''): int {
        return Database::insert(
            'INSERT INTO time_logs (ticket_id, user_id, minutes, note, logged_at) VALUES (?,?,?,?,NOW())',
            [$ticketId, $userId, $minutes, $note]
        );
    }

    public static function forTicket(int $ticketId): array {
        return Database::fetchAll(
            'SELECT tl.*, u.name as user_name FROM time_logs tl JOIN users u ON u.id=tl.user_id WHERE tl.ticket_id=? ORDER BY tl.logged_at DESC',
            [$ticketId]
        );
    }

    public static function totalForTicket(int $ticketId): int {
        $row = Database::fetch('SELECT SUM(minutes) as total FROM time_logs WHERE ticket_id=?', [$ticketId]);
        return (int)($row['total'] ?? 0);
    }

    public static function recentAll(int $limit = 50, int $offset = 0): array {
        return Database::fetchAll(
            'SELECT tl.*, u.name as user_name, t.subject as ticket_subject, t.id as ticket_id
             FROM time_logs tl
             JOIN users u ON u.id=tl.user_id
             JOIN tickets t ON t.id=tl.ticket_id
             ORDER BY tl.logged_at DESC LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }
}
