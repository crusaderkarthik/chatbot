<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/Ticket.php';
require_once APP . '/Models/Comment.php';
require_once APP . '/Models/TicketHistory.php';
require_once APP . '/Models/Attachment.php';
require_once APP . '/Models/Tag.php';
require_once APP . '/Models/Watcher.php';
require_once APP . '/Models/ActiveTimer.php';
require_once APP . '/Models/TimeLog.php';
require_once APP . '/Models/Category.php';
require_once APP . '/Models/User.php';
require_once APP . '/Models/CustomField.php';
require_once APP . '/Models/TicketTemplate.php';
require_once APP . '/Models/SavedReply.php';
require_once APP . '/Models/Sla.php';
require_once APP . '/Models/Setting.php';
require_once APP . '/Helpers/Notifier.php';
require_once APP . '/Helpers/Discord.php';
require_once APP . '/Helpers/Mailer.php';

class TicketController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $user  = Auth::user();
        $perms = json_decode($user['permissions'] ?? '[]', true) ?? [];

        $filters = [
            'status'      => $_GET['status'] ?? '',
            'priority'    => $_GET['priority'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'search'      => $_GET['search'] ?? '',
        ];

        $tickets    = Ticket::listForUser($user['id'], $user['role'], $perms, $filters);
        $categories = Category::all('name');

        $this->view('layouts.app', [
            'title'        => 'Tickets',
            'content_view' => 'tickets.index',
            'tickets'      => $tickets,
            'filters'      => $filters,
            'categories'   => $categories,
        ]);
    }

    public function create(): void {
        $this->requirePermission('create_tickets');
        $categories = Category::all('name');
        $templates  = TicketTemplate::all('name');
        $fields     = CustomField::forCategory(null);

        $this->view('layouts.app', [
            'title'        => 'New Ticket',
            'content_view' => 'tickets.create',
            'categories'   => $categories,
            'templates'    => $templates,
            'fields'       => $fields,
        ]);
    }

    public function store(): void {
        $this->requirePermission('create_tickets');
        verify_csrf();

        $categoryId = (int)$this->input('category_id') ?: null;
        $id = Ticket::create([
            'subject'         => $this->sanitize($this->input('subject')),
            'message'         => $this->input('message'),
            'category_id'     => $categoryId,
            'priority'        => $this->input('priority', 'medium'),
            'requester_name'  => $this->sanitize($this->input('requester_name')),
            'requester_email' => $this->sanitize($this->input('requester_email')),
            'created_by'      => Auth::id(),
        ]);

        // Custom fields
        $cfValues = $_POST['cf'] ?? [];
        if ($cfValues) CustomField::saveValues($id, $cfValues);

        // Attachments
        if (!empty($_FILES['attachments']['name'][0])) {
            foreach ($_FILES['attachments']['name'] as $i => $name) {
                $file = [
                    'name'     => $name,
                    'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                    'size'     => $_FILES['attachments']['size'][$i],
                    'error'    => $_FILES['attachments']['error'][$i],
                ];
                Attachment::upload($file, $id, Auth::id());
            }
        }

        Watcher::add($id, Auth::id());
        TicketHistory::log($id, Auth::id(), 'created', '', 'new');

        $ticket = Ticket::full($id);
        Discord::notify('created', $ticket);

        flash('success', 'Ticket ' . format_ticket_id($id) . ' created.');
        redirect('tickets/' . $id);
    }

    public function show(string $id): void {
        $this->requireAuth();
        $ticket = Ticket::full((int)$id);
        if (!$ticket || $ticket['deleted']) { http_response_code(404); $this->view('errors.404'); return; }

        Sla::check($ticket);

        $comments    = Comment::forTicket($ticket['id']);
        $history     = TicketHistory::forTicket($ticket['id']);
        $attachments = Attachment::forTicket($ticket['id']);
        $tags        = Tag::forTicket($ticket['id']);
        $watchers    = Watcher::forTicket($ticket['id']);
        $timeLogs    = TimeLog::forTicket($ticket['id']);
        $totalTime   = TimeLog::totalForTicket($ticket['id']);
        $allTags     = Tag::all('name');
        $agents      = User::agents();
        $categories  = Category::all('name');
        $cfValues    = CustomField::valuesForTicket($ticket['id']);
        $cfFields    = CustomField::forCategory($ticket['category_id']);
        $savedReplies = SavedReply::forUser(Auth::id());
        $activeTimer = ActiveTimer::forUser(Auth::id());
        $dependencies = Database::fetchAll(
            'SELECT td.*, t.subject, t.status FROM ticket_dependencies td JOIN tickets t ON t.id=td.depends_on_id WHERE td.ticket_id=?',
            [$ticket['id']]
        );
        $merges = Database::fetchAll('SELECT * FROM ticket_merges WHERE parent_id=?', [$ticket['id']]);
        $aliases = Database::fetchAll('SELECT * FROM ticket_aliases WHERE ticket_id=?', [$ticket['id']]);
        $isWatching = (bool) Database::fetch('SELECT id FROM ticket_watchers WHERE ticket_id=? AND user_id=?', [$ticket['id'], Auth::id()]);

        $this->view('layouts.app', [
            'title'        => format_ticket_id($ticket['id']) . ': ' . $ticket['subject'],
            'content_view' => 'tickets.show',
            'ticket'       => $ticket,
            'comments'     => $comments,
            'history'      => $history,
            'attachments'  => $attachments,
            'tags'         => $tags,
            'allTags'      => $allTags,
            'watchers'     => $watchers,
            'timeLogs'     => $timeLogs,
            'totalTime'    => $totalTime,
            'agents'       => $agents,
            'categories'   => $categories,
            'cfValues'     => $cfValues,
            'cfFields'     => $cfFields,
            'savedReplies' => $savedReplies,
            'activeTimer'  => $activeTimer,
            'dependencies' => $dependencies,
            'merges'       => $merges,
            'aliases'      => $aliases,
            'isWatching'   => $isWatching,
        ]);
    }

    public function reply(string $id): void {
        $this->requireAuth();
        verify_csrf();
        $ticket = Ticket::full((int)$id);
        if (!$ticket) { redirect('tickets'); }

        $message    = $this->input('message');
        $isInternal = (bool)$this->input('is_internal', 0);

        $commentId = Comment::create([
            'ticket_id'   => $ticket['id'],
            'user_id'     => Auth::id(),
            'message'     => $message,
            'is_internal' => $isInternal,
        ]);

        TicketHistory::log($ticket['id'], Auth::id(), $isInternal ? 'internal_note' : 'reply', '', '');
        Ticket::setFirstResponseAt($ticket['id']);

        // Mentions
        Notifier::notifyMentions($message, $ticket);

        // Attachments
        if (!empty($_FILES['attachments']['name'][0])) {
            foreach ($_FILES['attachments']['name'] as $i => $name) {
                $file = ['name' => $name, 'tmp_name' => $_FILES['attachments']['tmp_name'][$i], 'size' => $_FILES['attachments']['size'][$i], 'error' => $_FILES['attachments']['error'][$i]];
                Attachment::upload($file, $ticket['id'], Auth::id());
            }
        }

        if (!$isInternal) {
            Notifier::notifyWatchers($ticket, 'replied', "New reply on ticket " . format_ticket_id($ticket['id']) . ": " . $ticket['subject']);
            Discord::notify('replied', $ticket);
        }

        redirect('tickets/' . $id . '#comments');
    }

    public function updateStatus(string $id): void {
        $this->requireAuth();
        verify_csrf();
        $ticket = Ticket::full((int)$id);
        if (!$ticket) redirect('tickets');

        $newStatus = $this->input('status');
        $allowed   = ['new','open','assigned','in_progress','waiting_for_client','on_hold','resolved','closed'];
        if (!in_array($newStatus, $allowed)) redirect('tickets/' . $id);

        $old = $ticket['status'];
        Ticket::updateStatus($ticket['id'], $newStatus);
        TicketHistory::log($ticket['id'], Auth::id(), 'status_changed', $old, $newStatus);
        Notifier::notifyWatchers($ticket, 'status', "Ticket " . format_ticket_id($ticket['id']) . " status changed to $newStatus.");
        Discord::notify('status', array_merge($ticket, ['status' => $newStatus]));

        flash('success', 'Status updated.');
        redirect('tickets/' . $id);
    }

    public function assign(string $id): void {
        $this->requirePermission('assign_tickets');
        verify_csrf();
        $ticket = Ticket::full((int)$id);
        if (!$ticket) redirect('tickets');

        $assignTo   = (int)$this->input('assigned_to') ?: null;
        $categoryId = (int)$this->input('category_id') ?: null;
        Ticket::assign($ticket['id'], $assignTo, $categoryId);
        TicketHistory::log($ticket['id'], Auth::id(), 'assigned', (string)$ticket['assigned_to'], (string)$assignTo);
        Discord::notify('assigned', $ticket);

        flash('success', 'Ticket assigned.');
        redirect('tickets/' . $id);
    }

    public function tagUpdate(string $id): void {
        $this->requireAuth();
        verify_csrf();
        $tagIds = array_map('intval', $_POST['tags'] ?? []);
        Tag::syncTicket((int)$id, $tagIds);
        TicketHistory::log((int)$id, Auth::id(), 'tags_updated', '', implode(',', $tagIds));
        redirect('tickets/' . $id);
    }

    public function watch(string $id): void {
        $this->requireAuth();
        verify_csrf();
        $watching = Watcher::toggle((int)$id, Auth::id());
        flash('success', $watching ? 'Watching ticket.' : 'Unwatched ticket.');
        redirect('tickets/' . $id);
    }

    public function timer(string $id): void {
        $this->requireAuth();
        verify_csrf();
        $action = $this->input('action');
        if ($action === 'start') {
            ActiveTimer::start(Auth::id(), (int)$id);
            flash('success', 'Timer started.');
        } else {
            ActiveTimer::stop(Auth::id());
            flash('success', 'Timer stopped and time logged.');
        }
        redirect('tickets/' . $id);
    }

    public function logTime(string $id): void {
        $this->requirePermission('log_time');
        verify_csrf();
        $minutes = (int)$this->input('minutes');
        $note    = $this->sanitize($this->input('note'));
        if ($minutes > 0) {
            TimeLog::create((int)$id, Auth::id(), $minutes, $note);
            TicketHistory::log((int)$id, Auth::id(), 'time_logged', '', "$minutes min");
        }
        flash('success', 'Time logged.');
        redirect('tickets/' . $id);
    }

    public function cloneTicket(string $id): void {
        $this->requirePermission('create_tickets');
        verify_csrf();
        $ticket = Ticket::full((int)$id);
        if (!$ticket) redirect('tickets');

        $newId = Ticket::create([
            'subject'         => '[Clone] ' . $ticket['subject'],
            'message'         => $ticket['message'],
            'category_id'     => $ticket['category_id'],
            'priority'        => $ticket['priority'],
            'requester_name'  => $ticket['requester_name'],
            'requester_email' => $ticket['requester_email'],
            'created_by'      => Auth::id(),
        ]);

        TicketHistory::log($newId, Auth::id(), 'cloned_from', '', (string)$ticket['id']);
        flash('success', 'Ticket cloned as ' . format_ticket_id($newId));
        redirect('tickets/' . $newId);
    }

    public function merge(string $id): void {
        $this->requirePermission('merge_tickets');
        verify_csrf();
        $sourceId = (int)$this->input('source_id');
        if ($sourceId === (int)$id || !$sourceId) redirect('tickets/' . $id);

        Database::execute('UPDATE comments SET ticket_id=? WHERE ticket_id=?', [$id, $sourceId]);
        Database::execute('UPDATE attachments SET ticket_id=? WHERE ticket_id=?', [$id, $sourceId]);
        Database::insert('INSERT INTO ticket_merges (parent_id, merged_id, merged_at) VALUES (?,?,NOW())', [$id, $sourceId]);
        Database::execute('UPDATE tickets SET deleted=1 WHERE id=?', [$sourceId]);
        TicketHistory::log((int)$id, Auth::id(), 'merged', '', (string)$sourceId);

        flash('success', 'Ticket merged.');
        redirect('tickets/' . $id);
    }

    public function alias(string $id): void {
        $this->requirePermission('create_alias');
        verify_csrf();
        $alias = $this->sanitize($this->input('alias'));
        if ($alias) {
            Database::insert('INSERT IGNORE INTO ticket_aliases (ticket_id, alias, created_at) VALUES (?,?,NOW())', [$id, $alias]);
        }
        flash('success', 'Alias added.');
        redirect('tickets/' . $id);
    }

    public function delete(string $id): void {
        $this->requirePermission('delete_tickets');
        verify_csrf();
        Database::execute('UPDATE tickets SET deleted=1 WHERE id=?', [$id]);
        TicketHistory::log((int)$id, Auth::id(), 'deleted', '', '');
        flash('success', 'Ticket deleted.');
        redirect('tickets');
    }

    public function archive(string $id): void {
        $this->requirePermission('archive_tickets');
        verify_csrf();
        Ticket::updateStatus((int)$id, 'archived');
        TicketHistory::log((int)$id, Auth::id(), 'archived', '', '');
        flash('success', 'Ticket archived.');
        redirect('tickets');
    }

    public function history(string $id): void {
        $this->requireAuth();
        $ticket  = Ticket::full((int)$id);
        $history = TicketHistory::forTicket((int)$id);
        $this->view('layouts.app', [
            'title'        => 'History: ' . format_ticket_id((int)$id),
            'content_view' => 'tickets.history',
            'ticket'       => $ticket,
            'history'      => $history,
        ]);
    }
}
