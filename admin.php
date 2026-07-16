<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!is_file(__DIR__ . '/data/zlat.sqlite')) { header('Location: ' . url('install.php')); exit; }

start_session_once();
$page = preg_replace('/[^a-z0-9_]/i', '', (string)($_GET['p'] ?? 'dashboard')) ?: 'dashboard';

if ($page === 'logout') { admin_logout(); header('Location: ' . url('admin.php?p=login')); exit; }

if ($page === 'login') {
    $err = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $u = trim((string)($_POST['username'] ?? ''));
        $p = (string)($_POST['password'] ?? '');
        if (admin_login($u, $p)) { header('Location: ' . url('admin.php')); exit; }
        $err = 'Onjuiste inloggegevens.';
    }
    require __DIR__ . '/admin_views/login.php';
    exit;
}

admin_require();

$views = ['dashboard','booking_view','regions','airports','prices','pages','page_edit','settings','email_templates'];
if (!in_array($page, $views, true)) $page = 'dashboard';
$view_file = __DIR__ . '/admin_views/' . $page . '.php';
if (!is_file($view_file)) { $page = 'dashboard'; $view_file = __DIR__ . '/admin_views/dashboard.php'; }

require __DIR__ . '/admin_views/_shell_top.php';
require $view_file;
require __DIR__ . '/admin_views/_shell_bottom.php';
