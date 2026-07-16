<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Bootstrap the DB if the file is missing (first visit).
if (!is_file(__DIR__ . '/data/zlat.sqlite')) {
    header('Location: ' . url('install.php'));
    exit;
}

$page = $_GET['p'] ?? 'home';
$page = preg_replace('/[^a-z0-9\-_]/i', '', (string)$page) ?: 'home';

// AJAX API endpoints
if (isset($_GET['api'])) {
    $api = preg_replace('/[^a-z0-9\-_]/i', '', (string)$_GET['api']);
    $file = __DIR__ . '/api/' . $api . '.php';
    if (is_file($file)) { require $file; exit; }
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Onbekende API']);
    exit;
}

// Airport / Region detail
$slug_type = null; $slug_value = null;
if (isset($_GET['luchthaven'])) { $slug_type = 'airport'; $slug_value = $_GET['luchthaven']; }
elseif (isset($_GET['regio']))   { $slug_type = 'region';  $slug_value = $_GET['regio']; }

$view = null;
if ($slug_type === 'airport') { $view = 'airport'; }
elseif ($slug_type === 'region') { $view = 'region'; }
elseif ($page === 'home') { $view = 'home'; }
elseif ($page === 'boeking') { $view = 'booking_success'; }
else { $view = 'content'; }

$file = __DIR__ . '/pages/' . $view . '.php';
if (!is_file($file)) {
    http_response_code(404);
    $view = 'content';
    $page = '__notfound';
    $file = __DIR__ . '/pages/content.php';
}

require __DIR__ . '/includes/header.php';
require $file;
require __DIR__ . '/includes/footer.php';
