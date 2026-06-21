<?php $isAdmin = Auth::role() === 'admin'; ?>
<div class="page-header">
    <div>
        <h1><?= format_ticket_id($ticket['id']) ?>: <?= e($ticket['subject']) ?>
            <?php if ($ticket['sla_breach']): ?>
                <span class="badge badge-danger">SLA Breached</span>
            <?php endif; ?>
        </h1>
        <div class="ticket-meta">
            <span class="badge status-<?= $ticket['status'] ?>"><?= ucwords(str_replace('_',' ',$ticket['status'])) ?></span>
            <span class="badge priority-<?= $ticket['priority'] ?>"><?= ucfirst($ticket['priority']) ?></span>
            <span class="text-muted">Created <?= ago($ticket['created_at']) ?> by <?= e($ticket['creator_name'] ?? '?') ?></span>
        </div>
    </div>
    <a href="<?= url('tickets') ?>" class="btn btn-ghost">← Back</a>
</div>

<div class="ticket-layout">
    <!-- Left: timeline -->
    <div class="ticket-main">
        <!-- Original message -->
        <div class="timeline-event public">
            <div class="event-header">
                <strong><?= e($ticket['requester_name']) ?></strong>
                <span class="text-muted"><?= e($ticket['requester_email']) ?></span>
                <span class="text-muted ml-auto"><?= ago($ticket['created_at']) ?></span>
            </div>
            <div class="event-body"><?= nl2br(e($ticket['message'])) ?></div>
        </div>

        <!-- Comments / Notes -->
        <div id="comments">
        <?php foreach ($comments as $c): ?>
        <div class="timeline-event <?= $c['is_internal'] ? 'internal' : 'public' ?>">
            <div class="event-header">
                <?php if ($c['author_avatar']): ?>
                    <img src="<?= asset('uploads/' . $c['author_avatar']) ?>" class="avatar-xs" alt="">
                <?php else: ?>
                    <div class="avatar-placeholder-xs"><?= strtoupper($c['author_name'][0]) ?></div>
                <?php endif; ?>
                <strong><?= e($c['author_name']) ?></strong>
                <?php if ($c['is_internal']): ?><span class="badge badge-internal">Internal Note</span><?php endif; ?>
                <span class="text-muted ml-auto"><?= ago($c['created_at']) ?></span>
            </div>
            <div class="event-body"><?= nl2br(e($c['message'])) ?></div>
        </div>
        <?php endforeach; ?>
        </div>

        <!-- Reply form -->
        <div class="card mt-4">
            <div class="card-header">
                <span>Reply</span>
                <label class="toggle-label ml-auto">
                    <input type="checkbox" id="toggle-internal" name="is_internal" value="1">
                    Internal Note
                </label>
            </div>
            <div class="card-body" id="reply-box">
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/reply') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="is_internal" id="is_internal_field" value="0">

                    <!-- Saved replies -->
                    <?php if (!empty($savedReplies)): ?>
                    <div class="form-group">
                        <select onchange="insertSavedReply(this)" class="form-control">
                            <option value="">— Insert saved reply —</option>
                            <?php foreach ($savedReplies as $sr): ?>
                            <option value="<?= e($sr['content']) ?>"><?= e($sr['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <textarea name="message" id="reply-message" required class="form-control" rows="6"
                            placeholder="Type your reply… use @Username to mention colleagues"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="file" name="attachments[]" multiple class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>

        <!-- History tab -->
        <div class="card mt-4">
            <div class="card-header collapsible" onclick="toggleSection('history-body')">
                Ticket History (<?= count($history) ?>)
            </div>
            <div id="history-body" class="card-body p-0 hidden">
                <table class="data-table">
                    <thead><tr><th>Action</th><th>By</th><th>Old</th><th>New</th><th>When</th></tr></thead>
                    <tbody>
                    <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?= e($h['action']) ?></td>
                        <td><?= e($h['actor_name']) ?></td>
                        <td class="text-muted"><?= e($h['old_value']) ?></td>
                        <td><?= e($h['new_value']) ?></td>
                        <td><?= ago($h['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Time logs -->
        <div class="card mt-4">
            <div class="card-header collapsible" onclick="toggleSection('time-body')">
                Time Logged: <?= format_duration($totalTime) ?>
            </div>
            <div id="time-body" class="card-body hidden">
                <?php if (Auth::can('log_time')): ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/log-time') ?>" class="inline-form mb-3">
                    <?= csrf_field() ?>
                    <input type="number" name="minutes" min="1" placeholder="Minutes" class="form-control form-inline" required>
                    <input type="text" name="note" placeholder="Note (optional)" class="form-control form-inline">
                    <button type="submit" class="btn btn-secondary">Log Time</button>
                </form>
                <!-- Timer -->
                <?php
                $timerOnThis = $activeTimer && $activeTimer['ticket_id'] == $ticket['id'];
                ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/timer') ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <?php if ($timerOnThis): ?>
                        <input type="hidden" name="action" value="stop">
                        <button type="submit" class="btn btn-danger">⏹ Stop Timer</button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="start">
                        <button type="submit" class="btn btn-success">▶ Start Timer</button>
                    <?php endif; ?>
                </form>
                <?php endif; ?>

                <table class="data-table mt-2">
                    <thead><tr><th>Agent</th><th>Duration</th><th>Note</th><th>Logged</th></tr></thead>
                    <tbody>
                    <?php foreach ($timeLogs as $tl): ?>
                    <tr>
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

        <!-- Attachments -->
        <?php if ($attachments): ?>
        <div class="card mt-4">
            <div class="card-header">Attachments (<?= count($attachments) ?>)</div>
            <div class="card-body">
                <?php foreach ($attachments as $att): ?>
                <div class="attachment-item">
                    <a href="<?= asset('uploads/' . $att['filename']) ?>" target="_blank"><?= e($att['original_name']) ?></a>
                    <span class="text-muted">(<?= round($att['size']/1024) ?> KB)</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right: sidebar panel -->
    <div class="ticket-sidebar">
        <!-- Status -->
        <div class="card mb-3">
            <div class="card-header">Status</div>
            <div class="card-body">
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/status') ?>">
                    <?= csrf_field() ?>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <?php foreach (['new','open','assigned','in_progress','waiting_for_client','on_hold','resolved','closed'] as $s): ?>
                        <option value="<?= $s ?>" <?= $ticket['status']===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php if (Auth::can('archive_tickets') && $ticket['status'] === 'closed'): ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/archive') ?>" class="mt-2">
                    <?= csrf_field() ?>
                    <button class="btn btn-ghost btn-sm w-full">Archive Ticket</button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Assign -->
        <?php if (Auth::can('assign_tickets')): ?>
        <div class="card mb-3">
            <div class="card-header">Assignment</div>
            <div class="card-body">
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/assign') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Agent</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">— Unassigned —</option>
                            <?php foreach ($agents as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= $ticket['assigned_to']==$a['id']?'selected':'' ?>><?= e($a['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">— None —</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $ticket['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-secondary btn-sm w-full">Save Assignment</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tags -->
        <div class="card mb-3">
            <div class="card-header">Tags</div>
            <div class="card-body">
                <div class="tags-display mb-2">
                    <?php foreach ($tags as $tag): ?>
                    <span class="tag" style="background:<?= e($tag['color']) ?>"><?= e($tag['name']) ?></span>
                    <?php endforeach; ?>
                </div>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/tag') ?>">
                    <?= csrf_field() ?>
                    <div class="tags-checkboxes">
                        <?php foreach ($allTags as $tag): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"
                                <?= in_array($tag['id'], array_column($tags,'id'))?'checked':'' ?>>
                            <span class="tag" style="background:<?= e($tag['color']) ?>"><?= e($tag['name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-secondary btn-sm w-full mt-2">Update Tags</button>
                </form>
            </div>
        </div>

        <!-- Watchers -->
        <div class="card mb-3">
            <div class="card-header">Watchers</div>
            <div class="card-body">
                <?php foreach ($watchers as $w): ?>
                <div class="watcher-item"><?= e($w['name']) ?></div>
                <?php endforeach; ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/watch') ?>" class="mt-2">
                    <?= csrf_field() ?>
                    <button class="btn btn-ghost btn-sm w-full"><?= $isWatching ? 'Unwatch' : 'Watch' ?></button>
                </form>
            </div>
        </div>

        <!-- Custom Field Values -->
        <?php if (!empty($cfValues)): ?>
        <div class="card mb-3">
            <div class="card-header">Custom Fields</div>
            <div class="card-body">
                <?php foreach ($cfValues as $cfv): ?>
                <div class="cf-row">
                    <span class="cf-label"><?= e($cfv['label']) ?></span>
                    <span class="cf-value"><?= e($cfv['value']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Dependencies -->
        <div class="card mb-3">
            <div class="card-header collapsible" onclick="toggleSection('deps-body')">Dependencies</div>
            <div id="deps-body" class="card-body hidden">
                <?php foreach ($dependencies as $dep): ?>
                <div class="dep-item">
                    <span class="badge"><?= $dep['type'] ?></span>
                    <a href="<?= url('tickets/' . $dep['depends_on_id']) ?>"><?= format_ticket_id($dep['depends_on_id']) ?></a>
                    <span class="badge status-<?= $dep['status'] ?>"><?= $dep['status'] ?></span>
                </div>
                <?php endforeach; ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/dependency') ?>" class="mt-2">
                    <?= csrf_field() ?>
                    <input type="number" name="depends_on_id" placeholder="Ticket ID" class="form-control form-inline">
                    <select name="type" class="form-control form-inline">
                        <option>blocks</option>
                        <option>is_blocked_by</option>
                        <option>relates_to</option>
                    </select>
                    <button class="btn btn-secondary btn-sm">Add</button>
                </form>
            </div>
        </div>

        <!-- Aliases -->
        <?php if (Auth::can('create_alias')): ?>
        <div class="card mb-3">
            <div class="card-header collapsible" onclick="toggleSection('alias-body')">Aliases</div>
            <div id="alias-body" class="card-body hidden">
                <?php foreach ($aliases as $al): ?><div class="text-muted"><?= e($al['alias']) ?></div><?php endforeach; ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/alias') ?>" class="mt-2">
                    <?= csrf_field() ?>
                    <input type="text" name="alias" placeholder="Add alias…" class="form-control">
                    <button class="btn btn-secondary btn-sm mt-1">Add Alias</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Merge -->
        <?php if (Auth::can('merge_tickets')): ?>
        <div class="card mb-3">
            <div class="card-header collapsible" onclick="toggleSection('merge-body')">Merge Ticket</div>
            <div id="merge-body" class="card-body hidden">
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/merge') ?>">
                    <?= csrf_field() ?>
                    <input type="number" name="source_id" placeholder="Source Ticket ID" class="form-control">
                    <button class="btn btn-warning btn-sm mt-1">Merge Into This</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="card mb-3">
            <div class="card-header">Actions</div>
            <div class="card-body flex-col gap-1">
                <?php if (Auth::can('create_tickets')): ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/clone') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-ghost btn-sm w-full">Clone Ticket</button>
                </form>
                <?php endif; ?>
                <?php if (Auth::can('delete_tickets')): ?>
                <form method="POST" action="<?= url('tickets/' . $ticket['id'] . '/delete') ?>" onsubmit="return confirm('Delete this ticket?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger btn-sm w-full">Delete Ticket</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function insertSavedReply(sel) {
    if (sel.value) {
        document.getElementById('reply-message').value = sel.value;
        sel.selectedIndex = 0;
    }
}

document.getElementById('toggle-internal').addEventListener('change', function() {
    var field = document.getElementById('is_internal_field');
    var box   = document.getElementById('reply-box');
    field.value = this.checked ? '1' : '0';
    box.classList.toggle('internal-mode', this.checked);
});

function toggleSection(id) {
    var el = document.getElementById(id);
    if (el) el.classList.toggle('hidden');
}
</script>
