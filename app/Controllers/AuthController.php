<?php
require_once APP . '/Core/Controller.php';
require_once APP . '/Core/Auth.php';
require_once APP . '/Models/User.php';
require_once APP . '/Helpers/Mailer.php';
require_once APP . '/Models/Setting.php';

class AuthController extends Controller {
    public function showLogin(): void {
        if (Auth::check()) redirect('dashboard');
        $this->view('auth.login', ['title' => 'Login']);
    }

    public function login(): void {
        verify_csrf();
        $email    = $this->sanitize($this->input('email'));
        $password = $this->input('password');

        if (Auth::attempt($email, $password)) {
            $user = Auth::user();
            Database::insert(
                'INSERT INTO audit_logs (user_id, action, ip, user_agent, created_at) VALUES (?,?,?,?,NOW())',
                [$user['id'], 'login', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
            );
            redirect('dashboard');
        }
        flash('error', 'Invalid credentials or account inactive.');
        redirect('auth/login');
    }

    public function logout(): void {
        Auth::logout();
        redirect('auth/login');
    }

    public function showForgot(): void {
        $this->view('auth.forgot', ['title' => 'Forgot Password']);
    }

    public function forgot(): void {
        verify_csrf();
        $email = $this->sanitize($this->input('email'));
        $user  = User::findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            Database::execute('DELETE FROM password_resets WHERE email=?', [$email]);
            Database::insert('INSERT INTO password_resets (email, token, expires_at) VALUES (?,?,?)', [$email, $token, $expires]);
            $link = url('auth/reset/' . $token);
            Mailer::send($email, 'Password Reset', "Click to reset: <a href='$link'>$link</a>");
        }
        flash('success', 'If that email exists, a reset link has been sent.');
        redirect('auth/forgot');
    }

    public function showReset(string $token): void {
        $reset = Database::fetch('SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()', [$token]);
        if (!$reset) { flash('error', 'Invalid or expired token.'); redirect('auth/forgot'); }
        $this->view('auth.reset', ['title' => 'Reset Password', 'token' => $token]);
    }

    public function reset(string $token): void {
        verify_csrf();
        $reset = Database::fetch('SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()', [$token]);
        if (!$reset) { flash('error', 'Invalid or expired token.'); redirect('auth/forgot'); }
        $pass = $this->input('password');
        if (strlen($pass) < 8) { flash('error', 'Password must be 8+ chars.'); redirect('auth/reset/' . $token); }
        $user = User::findByEmail($reset['email']);
        if ($user) User::updatePassword($user['id'], $pass);
        Database::execute('DELETE FROM password_resets WHERE token=?', [$token]);
        flash('success', 'Password updated. Please log in.');
        redirect('auth/login');
    }
}
