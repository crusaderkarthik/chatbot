<div class="page-header">
    <h1>Dashboard</h1>
    <a href="<?= url('tickets/create') ?>" class="btn btn-primary">+ New Ticket</a>
</div>

<!-- Status counters -->
<div class="stats-grid">
    <?php
    $statuses = ['new'=>'New','open'=>'Open','assigned'=>'Assigned','in_progress'=>'In Progress','waiting_for_client'=>'Waiting','on_hold'=>'On Hold','resolved'=>'Resolved','closed'=>'Closed'];
    foreach ($statuses as $key => $label):
    ?>
    <div class="stat-card status-<?= $key ?>">
        <div class="stat-value"><?= $counts[$key] ?? 0 ?></div>
        <div class="stat-label"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Weekly volume mini-chart -->
<div class="card mt-4">
    <div class="card-header">Ticket Volume — Last 7 Days</div>
    <div class="card-body">
        <canvas id="volumeChart" height="80"></canvas>
    </div>
</div>

<!-- Recent tickets -->
<div class="card mt-4">
    <div class="card-header">Recent Tickets</div>
    <div class="card-body p-0">
        <table class="data-table">
            <thead><tr>
                <th>ID</th><th>Subject</th><th>Category</th><th>Priority</th><th>Status</th><th>Created</th>
            </tr></thead>
            <tbody>
            <?php foreach ($recentTickets as $t): ?>
            <tr>
                <td><a href="<?= url('tickets/' . $t['id']) ?>"><?= format_ticket_id($t['id']) ?></a></td>
                <td><?= e($t['subject']) ?></td>
                <td><?= e($t['category_name'] ?? '—') ?></td>
                <td><span class="badge priority-<?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span></td>
                <td><span class="badge status-<?= $t['status'] ?>"><?= ucwords(str_replace('_',' ',$t['status'])) ?></span></td>
                <td><?= ago($t['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentTickets)): ?>
            <tr><td colspan="6" class="text-center text-muted">No tickets yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
    var labels = <?= json_encode(array_column($volumeWeek, 'd')) ?>;
    var data   = <?= json_encode(array_column($volumeWeek, 'cnt')) ?>;
    var canvas = document.getElementById('volumeChart');
    if (!canvas || !labels.length) return;
    var ctx = canvas.getContext('2d');
    var w = canvas.width = canvas.parentElement.offsetWidth;
    var h = canvas.height = 100;
    var max = Math.max(...data, 1);
    var step = w / (labels.length || 1);
    ctx.clearRect(0,0,w,h);
    ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--primary') || '#3b82f6';
    ctx.lineWidth = 2;
    ctx.beginPath();
    data.forEach(function(v,i) {
        var x = i * step + step/2;
        var y = h - (v/max)*(h-10) - 5;
        i===0 ? ctx.moveTo(x,y) : ctx.lineTo(x,y);
    });
    ctx.stroke();
})();
</script>
