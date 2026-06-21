<div class="page-header">
    <h1>Analytics</h1>
    <a href="<?= url('analytics/export') ?>" class="btn btn-secondary">⬇ Export CSV</a>
</div>

<!-- KPI cards -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-value"><?= $avgResponse ?>h</div>
        <div class="stat-label">Avg First Response</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $avgResolution ?>h</div>
        <div class="stat-label">Avg Resolution Time</div>
    </div>
    <?php foreach ($statusCounts as $row): ?>
    <div class="stat-card status-<?= $row['status'] ?>">
        <div class="stat-value"><?= $row['cnt'] ?></div>
        <div class="stat-label"><?= ucwords(str_replace('_',' ',$row['status'])) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="two-col-layout">
    <!-- Priority distribution -->
    <div class="card">
        <div class="card-header">Priority Distribution</div>
        <div class="card-body">
            <?php foreach ($priorityCounts as $row): ?>
            <div class="bar-row">
                <span class="bar-label"><?= ucfirst($row['priority']) ?></span>
                <div class="bar-track">
                    <div class="bar-fill priority-<?= $row['priority'] ?>" style="width:<?= min(100, $row['cnt'] * 5) ?>%"></div>
                </div>
                <span class="bar-value"><?= $row['cnt'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Category distribution -->
    <div class="card">
        <div class="card-header">Top Categories</div>
        <div class="card-body">
            <?php foreach ($categoryCounts as $row): ?>
            <div class="bar-row">
                <span class="bar-label"><?= e($row['name'] ?? 'Uncategorized') ?></span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= min(100, $row['cnt'] * 5) ?>%; background: var(--primary)"></div>
                </div>
                <span class="bar-value"><?= $row['cnt'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 30-day volume chart -->
<div class="card mt-4">
    <div class="card-header">Ticket Volume — Last 30 Days</div>
    <div class="card-body">
        <canvas id="volumeChart30" height="120"></canvas>
    </div>
</div>

<!-- Leaderboard -->
<div class="card mt-4">
    <div class="card-header">Resolution Leaderboard</div>
    <div class="card-body p-0">
        <table class="data-table">
            <thead><tr><th>#</th><th>Agent</th><th>Resolved</th></tr></thead>
            <tbody>
            <?php foreach ($leaderboard as $i => $row): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= e($row['name']) ?></td>
                <td><?= $row['resolved'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Time logs -->
<div class="card mt-4">
    <div class="card-header">Recent Time Logs</div>
    <div class="card-body p-0">
        <table class="data-table">
            <thead><tr><th>Ticket</th><th>Agent</th><th>Duration</th><th>Note</th><th>Logged</th></tr></thead>
            <tbody>
            <?php foreach ($timeLogs as $tl): ?>
            <tr>
                <td><a href="<?= url('tickets/' . $tl['ticket_id']) ?>"><?= format_ticket_id($tl['ticket_id']) ?></a>: <?= e($tl['ticket_subject']) ?></td>
                <td><?= e($tl['user_name']) ?></td>
                <td><?= format_duration($tl['minutes']) ?></td>
                <td><?= e($tl['note']) ?></td>
                <td><?= ago($tl['logged_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
    var labels = <?= json_encode(array_column($dailyVolume, 'd')) ?>;
    var data   = <?= json_encode(array_column($dailyVolume, 'cnt')) ?>;
    var canvas = document.getElementById('volumeChart30');
    if (!canvas || !labels.length) return;
    var ctx = canvas.getContext('2d');
    var w = canvas.width = canvas.parentElement.offsetWidth;
    var h = canvas.height = 140;
    var max = Math.max(...data, 1);
    var step = w / (labels.length || 1);
    ctx.clearRect(0,0,w,h);
    ctx.fillStyle = (getComputedStyle(document.documentElement).getPropertyValue('--primary') || '#3b82f6') + '33';
    ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--primary') || '#3b82f6';
    ctx.lineWidth = 2;
    ctx.beginPath();
    data.forEach(function(v,i) {
        var x = i * step + step/2;
        var y = h - (v/max)*(h-15) - 10;
        i===0 ? ctx.moveTo(x,y) : ctx.lineTo(x,y);
    });
    ctx.stroke();
})();
</script>
