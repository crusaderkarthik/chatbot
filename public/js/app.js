/* === CRUSADER WORKS — MAIN JS === */

(function () {
    'use strict';

    /* Auto-dismiss alerts */
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 4000);
    });

    /* Collapsible sections */
    document.querySelectorAll('.collapsible').forEach(function (header) {
        header.addEventListener('click', function () {
            var body = this.nextElementSibling;
            if (body) body.classList.toggle('hidden');
        });
    });

    /* Confirm delete forms */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    /* Drag & drop file upload */
    var dropZone = document.getElementById('drop-zone');
    var fileInput = document.getElementById('file-input');
    var fileList  = document.getElementById('file-list');

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', function () { fileInput.click(); });

        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('dragging');
        });

        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('dragging');
        });

        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('dragging');
            renderFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', function () {
            renderFiles(this.files);
        });

        function renderFiles(files) {
            if (!fileList) return;
            fileList.innerHTML = '';
            Array.from(files).forEach(function (f) {
                var d = document.createElement('div');
                d.className = 'file-item';
                d.textContent = f.name + ' (' + Math.round(f.size / 1024) + ' KB)';
                fileList.appendChild(d);
            });
        }
    }

    /* @mention autocomplete (basic) */
    var replyBox = document.getElementById('reply-message');
    if (replyBox) {
        replyBox.addEventListener('keyup', function (e) {
            var val = this.value;
            var atPos = val.lastIndexOf('@');
            if (atPos === -1) return;
            var query = val.substring(atPos + 1);
            if (query.length < 1 || /\s/.test(query)) return;

            fetch('/api/users-search?q=' + encodeURIComponent(query))
                .then(function (r) { return r.json(); })
                .then(function (users) {
                    removeMentionDropdown();
                    if (!users.length) return;
                    var dd = document.createElement('div');
                    dd.id = 'mention-dropdown';
                    dd.style.cssText = 'position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.1);z-index:1000;min-width:160px;overflow:hidden';
                    users.forEach(function (u) {
                        var item = document.createElement('div');
                        item.textContent = u.name;
                        item.style.cssText = 'padding:8px 12px;cursor:pointer;font-size:.85rem';
                        item.addEventListener('mouseenter', function () { this.style.background = 'var(--bg)'; });
                        item.addEventListener('mouseleave', function () { this.style.background = ''; });
                        item.addEventListener('mousedown', function (ev) {
                            ev.preventDefault();
                            var text = replyBox.value;
                            var at = text.lastIndexOf('@');
                            replyBox.value = text.substring(0, at) + '@' + u.name + ' ';
                            removeMentionDropdown();
                            replyBox.focus();
                        });
                        dd.appendChild(item);
                    });
                    replyBox.parentNode.style.position = 'relative';
                    replyBox.parentNode.appendChild(dd);
                })
                .catch(function () {});
        });

        replyBox.addEventListener('blur', function () {
            setTimeout(removeMentionDropdown, 200);
        });
    }

    function removeMentionDropdown() {
        var dd = document.getElementById('mention-dropdown');
        if (dd) dd.remove();
    }

    /* Timer status poll (if timer badge exists) */
    if (document.getElementById('sidebar-timer')) {
        var elapsed = 0;
        fetch('/api/timer-status')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.active) elapsed = data.elapsed;
            })
            .catch(function () {});
    }

})();
