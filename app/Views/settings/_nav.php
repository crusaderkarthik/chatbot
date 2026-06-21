<nav class="settings-nav">
    <a href="<?= url('settings') ?>" class="<?= str_ends_with($_SERVER['REQUEST_URI'],'/settings')?'active':'' ?>">General</a>
    <a href="<?= url('settings/branding') ?>">Branding</a>
    <a href="<?= url('settings/categories') ?>">Categories</a>
    <a href="<?= url('settings/sla') ?>">SLA Policies</a>
    <a href="<?= url('settings/custom-fields') ?>">Custom Fields</a>
    <a href="<?= url('settings/templates') ?>">Templates</a>
    <a href="<?= url('settings/tags') ?>">Tags</a>
    <a href="<?= url('settings/saved-replies') ?>">Saved Replies</a>
    <a href="<?= url('settings/smtp') ?>">SMTP</a>
    <a href="<?= url('settings/discord') ?>">Discord</a>
</nav>
