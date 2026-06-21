<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/Setting.php';
require_once APP . '/Models/Sla.php';
require_once APP . '/Models/CustomField.php';
require_once APP . '/Models/TicketTemplate.php';
require_once APP . '/Models/Tag.php';
require_once APP . '/Models/SavedReply.php';
require_once APP . '/Models/Category.php';

class SettingsController extends Controller {
    private function adminOnly(): void { $this->requirePermission('manage_settings'); }

    public function index(): void {
        $this->adminOnly();
        $settings = Setting::allAsArray();
        $this->view('layouts.app', ['title' => 'General Settings', 'content_view' => 'settings.index', 'settings' => $settings]);
    }

    public function update(): void {
        $this->adminOnly();
        verify_csrf();
        Setting::bulk([
            'app_name'     => $this->sanitize($this->input('app_name')),
            'app_url'      => $this->sanitize($this->input('app_url')),
            'ticket_prefix' => $this->sanitize($this->input('ticket_prefix', 'CW')),
            'timezone'     => $this->input('timezone', 'UTC'),
        ]);
        flash('success', 'Settings saved.');
        redirect('settings');
    }

    public function sla(): void {
        $this->adminOnly();
        $policies = Sla::all('priority');
        $this->view('layouts.app', ['title' => 'SLA Policies', 'content_view' => 'settings.sla', 'policies' => $policies]);
    }

    public function storeSla(): void {
        $this->adminOnly();
        verify_csrf();
        Sla::create([
            'priority'         => $this->input('priority'),
            'response_hours'   => (int)$this->input('response_hours'),
            'resolution_hours' => (int)$this->input('resolution_hours'),
        ]);
        flash('success', 'SLA policy saved.');
        redirect('settings/sla');
    }

    public function deleteSla(string $id): void {
        $this->adminOnly();
        verify_csrf();
        Sla::delete((int)$id);
        flash('success', 'SLA deleted.');
        redirect('settings/sla');
    }

    public function customFields(): void {
        $this->adminOnly();
        $fields = CustomField::all('id');
        $cats   = Category::all('name');
        $this->view('layouts.app', ['title' => 'Custom Fields', 'content_view' => 'settings.custom_fields', 'fields' => $fields, 'cats' => $cats]);
    }

    public function storeField(): void {
        $this->adminOnly();
        verify_csrf();
        CustomField::create([
            'label'       => $this->sanitize($this->input('label')),
            'type'        => $this->input('type'),
            'options'     => $this->sanitize($this->input('options')),
            'is_required' => (bool)$this->input('is_required'),
            'category_id' => (int)$this->input('category_id') ?: null,
        ]);
        flash('success', 'Field created.');
        redirect('settings/custom-fields');
    }

    public function deleteField(string $id): void {
        $this->adminOnly();
        verify_csrf();
        CustomField::delete((int)$id);
        flash('success', 'Field deleted.');
        redirect('settings/custom-fields');
    }

    public function templates(): void {
        $this->adminOnly();
        $templates = TicketTemplate::all('name');
        $cats      = Category::all('name');
        $this->view('layouts.app', ['title' => 'Templates', 'content_view' => 'settings.templates', 'templates' => $templates, 'cats' => $cats]);
    }

    public function storeTemplate(): void {
        $this->adminOnly();
        verify_csrf();
        TicketTemplate::create([
            'name'        => $this->sanitize($this->input('name')),
            'subject'     => $this->sanitize($this->input('subject')),
            'message'     => $this->input('message'),
            'category_id' => (int)$this->input('category_id') ?: null,
            'priority'    => $this->input('priority', 'medium'),
        ]);
        flash('success', 'Template created.');
        redirect('settings/templates');
    }

    public function deleteTemplate(string $id): void {
        $this->adminOnly();
        verify_csrf();
        TicketTemplate::delete((int)$id);
        flash('success', 'Template deleted.');
        redirect('settings/templates');
    }

    public function tags(): void {
        $this->adminOnly();
        $tags = Tag::all('name');
        $this->view('layouts.app', ['title' => 'Tags', 'content_view' => 'settings.tags', 'tags' => $tags]);
    }

    public function storeTag(): void {
        $this->adminOnly();
        verify_csrf();
        Tag::create($this->sanitize($this->input('name')), $this->input('color', '#3b82f6'));
        flash('success', 'Tag created.');
        redirect('settings/tags');
    }

    public function deleteTag(string $id): void {
        $this->adminOnly();
        verify_csrf();
        Tag::delete((int)$id);
        flash('success', 'Tag deleted.');
        redirect('settings/tags');
    }

    public function savedReplies(): void {
        $this->requireAuth();
        $replies = SavedReply::forUser(Auth::id());
        $this->view('layouts.app', ['title' => 'Saved Replies', 'content_view' => 'settings.saved_replies', 'replies' => $replies]);
    }

    public function storeSavedReply(): void {
        $this->requireAuth();
        verify_csrf();
        SavedReply::create([
            'title'     => $this->sanitize($this->input('title')),
            'content'   => $this->input('content'),
            'is_global' => Auth::role() === 'admin' && (bool)$this->input('is_global'),
            'user_id'   => Auth::id(),
        ]);
        flash('success', 'Saved reply created.');
        redirect('settings/saved-replies');
    }

    public function deleteSavedReply(string $id): void {
        $this->requireAuth();
        verify_csrf();
        SavedReply::delete((int)$id);
        flash('success', 'Saved reply deleted.');
        redirect('settings/saved-replies');
    }

    public function categories(): void {
        $this->adminOnly();
        $cats = Category::allWithCount();
        $this->view('layouts.app', ['title' => 'Categories', 'content_view' => 'settings.categories', 'cats' => $cats]);
    }

    public function storeCategory(): void {
        $this->adminOnly();
        verify_csrf();
        Category::create($this->sanitize($this->input('name')), $this->sanitize($this->input('description')));
        flash('success', 'Category created.');
        redirect('settings/categories');
    }

    public function deleteCategory(string $id): void {
        $this->adminOnly();
        verify_csrf();
        Category::delete((int)$id);
        flash('success', 'Category deleted.');
        redirect('settings/categories');
    }

    public function smtp(): void {
        $this->adminOnly();
        $settings = Setting::allAsArray();
        $this->view('layouts.app', ['title' => 'SMTP Settings', 'content_view' => 'settings.smtp', 'settings' => $settings]);
    }

    public function updateSmtp(): void {
        $this->adminOnly();
        verify_csrf();
        Setting::bulk([
            'smtp_host' => $this->sanitize($this->input('smtp_host')),
            'smtp_port' => $this->sanitize($this->input('smtp_port')),
            'smtp_user' => $this->sanitize($this->input('smtp_user')),
            'smtp_pass' => $this->input('smtp_pass'),
            'smtp_from' => $this->sanitize($this->input('smtp_from')),
        ]);
        flash('success', 'SMTP settings saved.');
        redirect('settings/smtp');
    }

    public function discord(): void {
        $this->adminOnly();
        $settings = Setting::allAsArray();
        $triggers = json_decode($settings['discord_triggers'] ?? '[]', true) ?? [];
        $this->view('layouts.app', ['title' => 'Discord Settings', 'content_view' => 'settings.discord', 'settings' => $settings, 'triggers' => $triggers]);
    }

    public function updateDiscord(): void {
        $this->adminOnly();
        verify_csrf();
        Setting::bulk([
            'discord_webhook'  => $this->sanitize($this->input('discord_webhook')),
            'discord_triggers' => json_encode($_POST['triggers'] ?? []),
        ]);
        flash('success', 'Discord settings saved.');
        redirect('settings/discord');
    }

    public function branding(): void {
        $this->adminOnly();
        $settings = Setting::allAsArray();
        $this->view('layouts.app', ['title' => 'Branding', 'content_view' => 'settings.branding', 'settings' => $settings]);
    }

    public function updateBranding(): void {
        $this->adminOnly();
        verify_csrf();
        if (!empty($_FILES['logo']['tmp_name'])) {
            $ext  = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png','jpg','jpeg','svg','webp'])) {
                $fn = 'logo.' . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], ROOT . '/public/' . $fn);
                Setting::set('logo', $fn);
            }
        }
        Setting::bulk([
            'primary_color' => $this->input('primary_color', '#3b82f6'),
            'app_name'      => $this->sanitize($this->input('app_name')),
        ]);
        flash('success', 'Branding updated.');
        redirect('settings/branding');
    }
}
