<div class="page-header">
    <h1>New Ticket</h1>
    <a href="<?= url('tickets') ?>" class="btn btn-ghost">← Back</a>
</div>

<?php if (!empty($templates)): ?>
<div class="card mb-3">
    <div class="card-header">Load Template</div>
    <div class="card-body">
        <select id="template-select" class="form-control" onchange="loadTemplate(this)">
            <option value="">— Select a template —</option>
            <?php foreach ($templates as $tpl): ?>
            <option value="<?= $tpl['id'] ?>"
                data-subject="<?= e($tpl['subject']) ?>"
                data-message="<?= e($tpl['message']) ?>"
                data-category="<?= $tpl['category_id'] ?>"
                data-priority="<?= $tpl['priority'] ?>"
            ><?= e($tpl['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url('tickets/create') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group">
                <label>Requester Name *</label>
                <input type="text" name="requester_name" required class="form-control">
            </div>
            <div class="form-group">
                <label>Requester Email *</label>
                <input type="email" name="requester_email" required class="form-control">
            </div>
            <div class="form-group">
                <label>Subject *</label>
                <input type="text" name="subject" id="field-subject" required class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Category</label>
                    <select name="category_id" id="field-category" class="form-control" onchange="loadCustomFields(this.value)">
                        <option value="">— None —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label>Priority *</label>
                    <select name="priority" id="field-priority" class="form-control">
                        <?php foreach (['low','medium','high','critical'] as $p): ?>
                        <option value="<?= $p ?>" <?= $p==='medium'?'selected':'' ?>><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Message *</label>
                <textarea name="message" id="field-message" required class="form-control" rows="8"></textarea>
            </div>

            <!-- Custom Fields -->
            <div id="custom-fields-area">
                <?php foreach ($fields as $f): ?>
                <?php require APP . '/Views/tickets/_custom_field.php'; ?>
                <?php endforeach; ?>
            </div>

            <!-- Attachments -->
            <div class="form-group">
                <label>Attachments</label>
                <div class="drop-zone" id="drop-zone">
                    <p>Drag & drop files here or <label for="file-input" class="link-btn">browse</label></p>
                    <input type="file" id="file-input" name="attachments[]" multiple class="hidden-input">
                </div>
                <div id="file-list"></div>
            </div>

            <button type="submit" class="btn btn-primary">Create Ticket</button>
        </form>
    </div>
</div>

<script>
function loadTemplate(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('field-subject').value   = opt.dataset.subject;
    document.getElementById('field-message').value   = opt.dataset.message;
    document.getElementById('field-priority').value  = opt.dataset.priority;
    var cat = document.getElementById('field-category');
    if (cat) cat.value = opt.dataset.category;
}

function loadCustomFields(categoryId) {
    fetch('<?= url('api/custom-fields') ?>?category_id=' + categoryId)
        .then(r => r.text())
        .then(html => { document.getElementById('custom-fields-area').innerHTML = html; })
        .catch(() => {});
}

// Drag & drop
(function() {
    var zone  = document.getElementById('drop-zone');
    var input = document.getElementById('file-input');
    var list  = document.getElementById('file-list');
    if (!zone) return;

    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragging'); });
    zone.addEventListener('dragleave', function() { zone.classList.remove('dragging'); });
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('dragging');
        updateFileList(e.dataTransfer.files);
    });
    input.addEventListener('change', function() { updateFileList(this.files); });

    function updateFileList(files) {
        list.innerHTML = '';
        Array.from(files).forEach(function(f) {
            var d = document.createElement('div');
            d.className = 'file-item';
            d.textContent = f.name + ' (' + Math.round(f.size/1024) + ' KB)';
            list.appendChild(d);
        });
    }
})();
</script>
