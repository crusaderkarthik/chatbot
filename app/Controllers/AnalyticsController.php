<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/TimeLog.php';
require_once APP . '/Models/Setting.php';

class AnalyticsController extends Controller {
    public function index(): void {
        $this->requireAuth();

        $statusCounts = Database::fetchAll('SELECT status, COUNT(*) as cnt FROM tickets WHERE deleted=0 GROUP BY status');
        $priorityCounts = Database::fetchAll('SELECT priority, COUNT(*) as cnt FROM tickets WHERE deleted=0 GROUP BY priority');
        $categoryCounts = Database::fetchAll(
            'SELECT c.name, COUNT(t.id) as cnt FROM tickets t LEFT JOIN categories c ON c.id=t.category_id WHERE t.deleted=0 GROUP BY c.name ORDER BY cnt DESC LIMIT 10'
        );

        $avgResponse = Database::fetch(
            'SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_h FROM tickets WHERE first_response_at IS NOT NULL AND deleted=0'
        );
        $avgResolution = Database::fetch(
            "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_h FROM tickets WHERE status IN ('closed','resolved') AND deleted=0"
        );

        $dailyVolume = Database::fetchAll(
            'SELECT DATE(created_at) as d, COUNT(*) as cnt FROM tickets WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY d ORDER BY d'
        );

        $leaderboard = Database::fetchAll(
            "SELECT u.name, COUNT(t.id) as resolved FROM users u
             JOIN tickets t ON t.assigned_to=u.id
             WHERE t.status IN ('resolved','closed') AND t.deleted=0
             GROUP BY u.id ORDER BY resolved DESC LIMIT 10"
        );

        $timeLogs = TimeLog::recentAll(20);
        $totalPages = ceil(Database::fetch('SELECT COUNT(*) as c FROM time_logs')['c'] / 20);

        $this->view('layouts.app', [
            'title'          => 'Analytics',
            'content_view'   => 'dashboard.analytics',
            'statusCounts'   => $statusCounts,
            'priorityCounts' => $priorityCounts,
            'categoryCounts' => $categoryCounts,
            'avgResponse'    => round($avgResponse['avg_h'] ?? 0, 1),
            'avgResolution'  => round($avgResolution['avg_h'] ?? 0, 1),
            'dailyVolume'    => $dailyVolume,
            'leaderboard'    => $leaderboard,
            'timeLogs'       => $timeLogs,
            'totalPages'     => $totalPages,
        ]);
    }

    public function export(): void {
        $this->requireAuth();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="tickets-export-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Subject','Status','Priority','Category','Requester','Assigned To','Created At','SLA Breach']);
        $tickets = Database::fetchAll(
            'SELECT t.id, t.subject, t.status, t.priority, c.name as cat, t.requester_name, u.name as agent, t.created_at, t.sla_breach
             FROM tickets t
             LEFT JOIN categories c ON c.id=t.category_id
             LEFT JOIN users u ON u.id=t.assigned_to
             WHERE t.deleted=0
             ORDER BY t.id DESC'
        );
        foreach ($tickets as $r) {
            fputcsv($out, [format_ticket_id($r['id']), $r['subject'], $r['status'], $r['priority'], $r['cat'], $r['requester_name'], $r['agent'], $r['created_at'], $r['sla_breach'] ? 'Yes' : 'No']);
        }
        fclose($out);
        exit;
    }
}
