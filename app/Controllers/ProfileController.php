<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Models/User.php';

class ProfileController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $this->view('layouts.app', ['title' => 'My Profile', 'content_view' => 'users.profile', 'user' => Auth::user()]);
    }

    public function update(): void {
        $this->requireAuth();
        verify_csrf();
        $u = Auth::user();
        Database::execute(
            'UPDATE users SET name=?, department=?, phone=?, timezone=?, theme=?, notification_prefs=? WHERE id=?',
            [
                $this->sanitize($this->input('name')),
                $this->sanitize($this->input('department')),
                $this->sanitize($this->input('phone')),
                $this->input('timezone', 'UTC'),
                $this->input('theme', 'light'),
                json_encode(['email' => (bool)$this->input('notif_email'), 'discord' => (bool)$this->input('notif_discord')]),
                $u['id'],
            ]
        );
        flash('success', 'Profile updated.');
        redirect('profile');
    }

    public function password(): void {
        $this->requireAuth();
        verify_csrf();
        $current = $this->input('current_password');
        $new     = $this->input('new_password');
        $u       = Auth::user();
        if (!password_verify($current, $u['password'])) {
            flash('error', 'Current password incorrect.');
            redirect('profile');
        }
        if (strlen($new) < 8) {
            flash('error', 'New password must be 8+ chars.');
            redirect('profile');
        }
        User::updatePassword($u['id'], $new);
        flash('success', 'Password changed.');
        redirect('profile');
    }

    public function avatar(): void {
        $this->requireAuth();
        verify_csrf();
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $file = $_FILES['avatar'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                flash('error', 'Invalid image type.');
                redirect('profile');
            }
            $filename = 'avatar_' . Auth::id() . '_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], ROOT . '/public/uploads/' . $filename);
            User::setAvatar(Auth::id(), $filename);
        }
        flash('success', 'Avatar updated.');
        redirect('profile');
    }
}
