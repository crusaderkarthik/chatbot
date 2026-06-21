<div class="page-header">
    <h1>Tickets</h1>
    <?php if (Auth::can('create_tickets')): ?>
    <a href="<?= url('tickets/create') ?>" class="btn btn-primary">+ New Ticket</a>
    <?php endif; ?>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('tickets') ?>" class="filter-bar">
    <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Search subject or email…" class="form-control">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <?php foreach (['new','open','assigned','in_progress','waiting_for_client','on_hold','resolved','closed','archived'] as $s): ?>
        <option value="<?= $s ?>" <?= $filters['status']===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="priority" class="form-control">
        <option value="">All Priorities</option>
        <?php foreach (['low','medium','high','critical'] as $p): ?>
        <option value="<?= $p ?>" <?= $filters['priority']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="category_id" class="form-control">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $filters['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
    <a href="<?= url('tickets') ?>" class="btn btn-ghost">Clear</a>
</form>

<div class="card mt-3">
    <div class="card-body p-0">
        <table class="data-table">
            <thead><tr>
                <th>ID</th><th>Subject</th><th>Requester</th><th>Category</th>
                <th>Priority</th><th>Status</th><th>Assigned To</th><th>Created</th>
            </tr></thead>
            <tbody>
            <?php foreach ($tickets as $t): ?>
            <tr class="<?= $t['sla_breach'] ? 'sla-breach' : '' ?>">
                <td><a href="<?= url('tickets/' . $t['id']) ?>"><?= format_ticket_id($t['id']) ?></a>
                    <?php if ($t['sla_breach']): ?><span class="badge badge-danger" title="SLA Breached">⚠</span><?php endif; ?>
                </td>
                <td><?= e($t['subject']) ?></td>
                <td><?= e($t['requester_name']) ?><br><small class="text-muted"><?= e($t['requester_email']) ?></small></td>
                <td><?= e($t['category_name'] ?? '—') ?></td>
                <td><span class="badge priority-<?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span></td>
                <td><span class="badge status-<?= $t['status'] ?>"><?= ucwords(str_replace('_',' ',$t['status'])) ?></span></td>
                <td><?= e($t['assignee_name'] ?? '—') ?></td>
                <td><?= ago($t['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($tickets)): ?>
            <tr><td colspan="8" class="text-center text-muted">No tickets found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
