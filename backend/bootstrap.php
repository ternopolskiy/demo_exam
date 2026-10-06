<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

date_default_timezone_set(APP_TIMEZONE);

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function old(string $key, string $default = ''): string
{
    return (string)($_SESSION['old'][$key] ?? $default);
}

function keep_old(array $fields): void
{
    foreach ($fields as $field) {
        $_SESSION['old'][$field] = $_POST[$field] ?? '';
    }
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function db(): Database
{
    return Database::instance();
}

function auth(): Auth
{
    return Auth::instance();
}
