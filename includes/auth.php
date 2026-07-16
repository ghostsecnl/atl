<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';

function admin_is_logged_in(): bool {
    start_session_once();
    return !empty($_SESSION['admin_id']);
}

function admin_login(string $username, string $password): bool {
    $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row) return false;
    if (!password_verify($password, $row['password_hash'])) return false;
    start_session_once();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int)$row['id'];
    $_SESSION['admin_username'] = $username;
    return true;
}

function admin_logout(): void {
    start_session_once();
    $_SESSION = [];
    session_destroy();
}

function admin_require(): void {
    if (!admin_is_logged_in()) {
        header('Location: ' . url('admin.php?p=login'));
        exit;
    }
}

function admin_change_password(int $id, string $new_password): void {
    $hash = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
    $stmt->execute([$hash, $id]);
}
