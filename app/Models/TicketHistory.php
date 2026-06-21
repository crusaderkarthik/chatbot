<?php
require_once APP . '/Core/Model.php';

class TicketHistory extends Model {
    protected static string $table = 'ticket_history';

    public static function log(int $ticketId, int $userId, string $action, string $oldVal = '', string $newVal = ''): void {
        Database::insert(
            'INSERT INTO ticket_history (ticket_id, user_id, action, old_value, new_value, created_at) VALUES (?,?,?,?,?,NOW())',
            [$ticketId, $userId, $action, $oldVal, $newVal]
        );
    }

    public static function forTicket(int $ticketId): array {
        return Database::fetchAll(
            'SELECT h.*, u.name as actor_name FROM ticket_history h JOIN users u ON u.id=h.user_id WHERE h.ticket_id=? ORDER BY h.created_at ASC',
            [$ticketId]
        );
    }
}
