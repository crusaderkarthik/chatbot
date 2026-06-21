<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/Ticket.php';
require_once APP . '/Models/User.php';
require_once APP . '/Models/TimeLog.php';
require_once APP . '/Models/ActiveTimer.php';
require_once APP . '/Models/Setting.php';

class DashboardController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $user   = Auth::user();
        $counts = Ticket::countByStatus();

        $recentTickets = Database::fetchAll(
            'SELECT t.*, c.name as category_name FROM tickets t
             LEFT JOIN categories c ON c.id=t.category_id
             WHERE t.deleted=0 ORDER BY t.created_at DESC LIMIT 10'
        );

        $volumeWeek = Database::fetchAll(
            'SELECT DATE(created_at) as d, COUNT(*) as cnt FROM tickets
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY d ORDER BY d'
        );

        $activeTimer = ActiveTimer::forUser($user['id']);

        $this->view('layouts.app', [
            'title'        => 'Dashboard',
            'content_view' => 'dashboard.index',
            'counts'       => $counts,
            'recentTickets' => $recentTickets,
            'volumeWeek'   => $volumeWeek,
            'activeTimer'  => $activeTimer,
        ]);
    }
}
