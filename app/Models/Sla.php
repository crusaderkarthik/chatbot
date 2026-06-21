<?php
require_once APP . '/Core/Model.php';

class Sla extends Model {
    protected static string $table = 'sla_policies';

    public static function forPriority(string $priority): ?array {
        return Database::fetch('SELECT * FROM sla_policies WHERE priority=?', [$priority]);
    }

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO sla_policies (priority, response_hours, resolution_hours) VALUES (?,?,?)',
            [$d['priority'], $d['response_hours'], $d['resolution_hours']]
        );
    }

    public static function check(array $ticket): void {
        $sla = self::forPriority($ticket['priority']);
        if (!$sla) return;

        $created = strtotime($ticket['created_at']);
        $now = time();

        $responseBreached = !$ticket['first_response_at']
            && ($now - $created) > $sla['response_hours'] * 3600;

        $resolutionBreached = !in_array($ticket['status'], ['resolved','closed','archived'])
            && ($now - $created) > $sla['resolution_hours'] * 3600;

        if (($responseBreached || $resolutionBreached) && !$ticket['sla_breach']) {
            Ticket::markSlaBreached($ticket['id']);
            Database::insert(
                'INSERT INTO sla_breaches (ticket_id, breach_type, breached_at) VALUES (?,?,NOW())',
                [$ticket['id'], $responseBreached ? 'response' : 'resolution']
            );
        }
    }
}
