<?php
declare(strict_types=1);

function auth_restore_session_from_remember_cookie(): void
{
    if (isset($_SESSION['user']) || empty($_COOKIE['remember_user'])) {
        return;
    }

    if (!function_exists('user_get_by_username_or_email')) {
        return;
    }

    $username_saved = $_COOKIE['remember_user'];
    $user = user_get_by_username_or_email($username_saved);

    if ($user && (int) ($user['status'] ?? 1) !== 0) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'display_name' => $user['display_name'],
            'role' => (string) ((int) $user['role'])
        ];
    }
}

function auth_require_admin(): void
{
    if (!isset($_SESSION["user"]) || (int) ($_SESSION["user"]["role"] ?? 0) !== 1) {
        header("Location: index.php?error=unauthorized");
        exit;
    }
}
function auth_is_logged_in(): bool
{
    return isset($_SESSION["user"]);
}
function auth_get_current_user(): ?array
{
    return $_SESSION["user"] ?? null;
}