<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/User.php';

class UserController extends Controller {
    public function index(): void {
        $this->requirePermission('manage_users');
        $users = User::all('name');
        $this->view('layouts.app', ['title' => 'Users', 'content_view' => 'users.index', 'users' => $users]);
    }

    public function create(): void {
        $this->requirePermission('manage_users');
        $this->view('layouts.app', ['title' => 'New User', 'content_view' => 'users.create']);
    }

    public function store(): void {
        $this->requirePermission('manage_users');
        verify_csrf();

        $email = $this->sanitize($this->input('email'));
        if (User::findByEmail($email)) {
            flash('error', 'Email already exists.');
            redirect('users/create');
        }

        $perms = $_POST['permissions'] ?? [];
        User::create([
            'name'       => $this->sanitize($this->input('name')),
            'email'      => $email,
            'password'   => $this->input('password'),
            'role'       => $this->input('role'),
            'department' => $this->sanitize($this->input('department')),
            'phone'      => $this->sanitize($this->input('phone')),
            'status'     => $this->input('status', 'active'),
            'timezone'   => $this->input('timezone', 'UTC'),
            'permissions' => $perms,
        ]);

        flash('success', 'User created.');
        redirect('users');
    }

    public function edit(string $id): void {
        $this->requirePermission('manage_users');
        $user = User::find((int)$id);
        if (!$user) redirect('users');
        $this->view('layouts.app', ['title' => 'Edit User', 'content_view' => 'users.edit', 'user' => $user]);
    }

    public function update(string $id): void {
        $this->requirePermission('manage_users');
        verify_csrf();
        $perms = $_POST['permissions'] ?? [];
        User::update((int)$id, [
            'name'        => $this->sanitize($this->input('name')),
            'email'       => $this->sanitize($this->input('email')),
            'role'        => $this->input('role'),
            'department'  => $this->sanitize($this->input('department')),
            'phone'       => $this->sanitize($this->input('phone')),
            'status'      => $this->input('status', 'active'),
            'timezone'    => $this->input('timezone', 'UTC'),
            'theme'       => $this->input('theme', 'light'),
            'permissions' => $perms,
            'notification_prefs' => ['email' => (bool)$this->input('notif_email'), 'discord' => (bool)$this->input('notif_discord')],
        ]);
        flash('success', 'User updated.');
        redirect('users');
    }

    public function delete(string $id): void {
        $this->requirePermission('manage_users');
        verify_csrf();
        if ((int)$id === Auth::id()) { flash('error', 'Cannot delete yourself.'); redirect('users'); }
        User::delete((int)$id);
        flash('success', 'User deleted.');
        redirect('users');
    }
}
