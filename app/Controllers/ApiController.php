<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/SavedReply.php';
require_once APP . '/Models/ActiveTimer.php';
require_once APP . '/Models/User.php';

class ApiController extends Controller {
    public function savedReplies(): void {
        $this->requireAuth();
        $this->json(SavedReply::forUser(Auth::id()));
    }

    public function timerStatus(): void {
        $this->requireAuth();
        $timer = ActiveTimer::forUser(Auth::id());
        if ($timer) {
            $elapsed = time() - strtotime($timer['started_at']);
            $this->json(['active' => true, 'ticket_id' => $timer['ticket_id'], 'elapsed' => $elapsed]);
        } else {
            $this->json(['active' => false]);
        }
    }

    public function usersSearch(): void {
        $this->requireAuth();
        $q    = $_GET['q'] ?? '';
        $like = '%' . $q . '%';
        $users = Database::fetchAll("SELECT id, name FROM users WHERE name LIKE ? AND status='active' LIMIT 10", [$like]);
        $this->json($users);
    }
}
