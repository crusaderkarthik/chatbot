<?php
require_once APP . '/Core/Model.php';

class Watcher extends Model {
    protected static string $table = 'ticket_watchers';

    public static function toggle(int $ticketId, int $userId): bool {
        $exists = Database::fetch('SELECT id FROM ticket_watchers WHERE ticket_id=? AND user_id=?', [$ticketId, $userId]);
        if ($exists) {
            Database::execute('DELETE FROM ticket_watchers WHERE ticket_id=? AND user_id=?', [$ticketId, $userId]);
            return false;
        }
        Database::execute('INSERT INTO ticket_watchers (ticket_id, user_id) VALUES (?,?)', [$ticketId, $userId]);
        return true;
    }

    public static function add(int $ticketId, int $userId): void {
        $exists = Database::fetch('SELECT id FROM ticket_watchers WHERE ticket_id=? AND user_id=?', [$ticketId, $userId]);
        if (!$exists) {
            Database::execute('INSERT INTO ticket_watchers (ticket_id, user_id) VALUES (?,?)', [$ticketId, $userId]);
        }
    }

    public static function forTicket(int $ticketId): array {
        return Database::fetchAll(
            'SELECT u.id, u.name, u.email FROM ticket_watchers tw JOIN users u ON u.id=tw.user_id WHERE tw.ticket_id=?',
            [$ticketId]
        );
    }
}
