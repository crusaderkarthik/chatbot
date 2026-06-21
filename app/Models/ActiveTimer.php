<?php
require_once APP . '/Core/Model.php';

class ActiveTimer extends Model {
    protected static string $table = 'active_timers';

    public static function forUser(int $userId): ?array {
        return Database::fetch('SELECT * FROM active_timers WHERE user_id=?', [$userId]);
    }

    public static function start(int $userId, int $ticketId): void {
        // Stop any existing timer first
        self::stop($userId);
        Database::insert('INSERT INTO active_timers (user_id, ticket_id, started_at) VALUES (?,?,NOW())', [$userId, $ticketId]);
    }

    public static function stop(int $userId): ?int {
        $timer = self::forUser($userId);
        if (!$timer) return null;
        $minutes = (int) ceil((time() - strtotime($timer['started_at'])) / 60);
        Database::execute('DELETE FROM active_timers WHERE user_id=?', [$userId]);
        if ($minutes > 0) {
            TimeLog::create($timer['ticket_id'], $userId, $minutes, 'Timer');
        }
        return $timer['ticket_id'];
    }
}
