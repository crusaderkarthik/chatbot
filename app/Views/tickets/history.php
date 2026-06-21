<div class="page-header">
    <h1>History: <?= format_ticket_id($ticket['id']) ?></h1>
    <a href="<?= url('tickets/' . $ticket['id']) ?>" class="btn btn-ghost">← Back to Ticket</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="data-table">
            <thead><tr><th>Action</th><th>By</th><th>Old Value</th><th>New Value</th><th>When</th></tr></thead>
            <tbody>
            <?php foreach ($history as $h): ?>
            <tr>
                <td><?= e($h['action']) ?></td>
                <td><?= e($h['actor_name']) ?></td>
                <td class="text-muted"><?= e($h['old_value']) ?></td>
                <td><?= e($h['new_value']) ?></td>
                <td><?= $h['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
