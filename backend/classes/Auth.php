<?php

class Auth
{
    private static ?Auth $instance = null;

    public static function instance(): Auth
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function attempt(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        unset($_SESSION['admin']);
        return true;
    }

    public function attemptAdmin(string $login, string $password): bool
    {
        if ($login !== ADMIN_LOGIN || $password !== ADMIN_PASSWORD) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        unset($_SESSION['user_id']);
        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool
    {
        return !empty($_SESSION['admin']);
    }

    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }
        return User::findById((int)$_SESSION['user_id']);
    }

    public function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }
}
