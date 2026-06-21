<?php
require_once APP . '/Core/Model.php';

class Ticket extends Model {
    protected static string $table = 'tickets';

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO tickets (subject, message, category_id, priority, requester_name, requester_email, status, created_by, created_at)
             VALUES (?,?,?,?,?,?,?,?,NOW())',
            [$d['subject'], $d['message'], $d['category_id'] ?? null, $d['priority'], $d['requester_name'], $d['requester_email'], 'new', $d['created_by']]
        );
    }

    public static function listForUser(int $userId, string $role, array $perms, array $filters = []): array {
        $where = ['t.deleted = 0'];
        $params = [];

        if ($role !== 'admin' && !in_array('view_all_tickets', $perms)) {
            $where[] = '(t.assigned_to = ? OR t.created_by = ? OR EXISTS(SELECT 1 FROM ticket_watchers tw WHERE tw.ticket_id=t.id AND tw.user_id=?))';
            $params = array_merge($params, [$userId, $userId, $userId]);
        }

        if (!empty($filters['status'])) {
            $where[] = 't.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[] = 't.priority = ?';
            $params[] = $filters['priority'];
        }
        if (!empty($filters['category_id'])) {
            $where[] = 't.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(t.subject LIKE ? OR t.requester_email LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql = 'SELECT t.*, c.name as category_name, u.name as assignee_name
                FROM tickets t
                LEFT JOIN categories c ON c.id = t.category_id
                LEFT JOIN users u ON u.id = t.assigned_to
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY t.created_at DESC';
        return Database::fetchAll($sql, $params);
    }

    public static function full(int $id): ?array {
        return Database::fetch(
            'SELECT t.*, c.name as category_name, u.name as assignee_name, cr.name as creator_name
             FROM tickets t
             LEFT JOIN categories c ON c.id = t.category_id
             LEFT JOIN users u ON u.id = t.assigned_to
             LEFT JOIN users cr ON cr.id = t.created_by
             WHERE t.id = ?',
            [$id]
        );
    }

    public static function updateStatus(int $id, string $status): void {
        Database::execute('UPDATE tickets SET status=?, updated_at=NOW() WHERE id=?', [$status, $id]);
    }

    public static function assign(int $id, ?int $userId, ?int $catId): void {
        $status = $userId ? 'assigned' : 'open';
        Database::execute('UPDATE tickets SET assigned_to=?, category_id=?, status=?, updated_at=NOW() WHERE id=?', [$userId, $catId, $status, $id]);
    }

    public static function markSlaBreached(int $id): void {
        Database::execute('UPDATE tickets SET sla_breach=1 WHERE id=?', [$id]);
    }

    public static function setFirstResponseAt(int $id): void {
        Database::execute('UPDATE tickets SET first_response_at=NOW() WHERE id=? AND first_response_at IS NULL', [$id]);
    }

    public static function countByStatus(): array {
        $rows = Database::fetchAll('SELECT status, COUNT(*) as cnt FROM tickets WHERE deleted=0 GROUP BY status');
        $result = [];
        foreach ($rows as $r) $result[$r['status']] = $r['cnt'];
        return $result;
    }
}
