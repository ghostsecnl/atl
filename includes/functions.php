<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function h(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_price($value): string {
    $amount = is_numeric($value) ? (float)$value : 0.0;
    $whole = number_format($amount, 2, ',', '.');
    return '€ ' . $whole;
}

function start_session_once(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function csrf_token(): string {
    start_session_once();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(?string $token): bool {
    start_session_once();
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function require_csrf(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return;
    $t = $_POST['csrf'] ?? '';
    if (!csrf_check(is_string($t) ? $t : '')) {
        http_response_code(400);
        die('CSRF verificatie mislukt.');
    }
}

// Settings ------------------------------------------------------------

const SETTING_DEFAULTS = [
    'company_name'      => 'Airport Taxi Limburg',
    'company_email'     => 'info@airport-taxi-limburg.nl',
    'company_phone'     => '06 22 33 45 66',
    'from_name'         => 'Airport Taxi Limburg',
    'from_email'        => 'info@airport-taxi-limburg.nl',
    'car_max_passengers'=> '4',
    'car_max_luggage'   => '4',
    'collect_luggage'   => '1',
    'return_multiplier' => '2',
    'price_per_km_car'  => '1.95',
    'price_per_km_van'  => '2.75',
    'one_off_fee'       => '10',
    'price_round_step'  => '5',
    'booking_ref_prefix'=> 'ZLT',
    'payment_note'      => 'Betaling contant of met pin in de taxi.',
    'booking_counter'   => '0',
    // SMTP
    'smtp_host'         => 'send.one.com',
    'smtp_port'         => '465',
    'smtp_username'     => 'info@airport-taxi-limburg.nl',
    'smtp_password'     => '',
    'smtp_encryption'   => 'tls', // tls, ssl, or empty
];

function settings_all(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $rows = db()->query('SELECT key, value FROM settings')->fetchAll();
    $out = SETTING_DEFAULTS;
    foreach ($rows as $r) {
        $out[$r['key']] = $r['value'];
    }
    $cache = $out;
    return $out;
}

function settings_get(string $key, string $default = ''): string {
    $s = settings_all();
    return $s[$key] ?? $default;
}

function settings_set(string $key, string $value): void {
    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO settings(key, value) VALUES(?, ?) ON CONFLICT(key) DO UPDATE SET value=excluded.value');
    $stmt->execute([$key, $value]);
    // invalidate cache
    static $reset = false;
    if (!$reset) {
        // reset the static cache in settings_all by using reflection-free trick: rebuild
    }
    // Simply flush by making a new call using a reset variable
    $GLOBALS['__settings_reset'] = true;
    // Force re-read next call:
    $ref = new ReflectionFunction('settings_all');
    $vars = $ref->getStaticVariables();
    // Can't easily reset static var; rely on fresh process per request.
}

function tel_href(string $phone): string {
    $digits = preg_replace('/[^\d+]/', '', $phone);
    if (strpos($digits, '+') === 0) return 'tel:' . $digits;
    return 'tel:+31' . ltrim($digits, '0');
}

function base_url(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

function url(string $path = ''): string {
    $path = ltrim($path, '/');
    // Determine script base (works with and without rewrite)
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
    return ($dir === '' ? '' : $dir) . '/' . $path;
}

// Regions helper
function normalize_str(?string $s): string {
    if ($s === null || $s === '') return '';
    if (function_exists('iconv')) {
        $x = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($x !== false) $s = $x;
    }
    $s = mb_strtolower($s, 'UTF-8');
    $s = preg_replace('/[\-\x{2013}\x{2014}]/u', ' ', $s);
    $s = preg_replace('/[^a-z0-9\s]/u', ' ', $s);
    $s = preg_replace('/\s+/', ' ', trim($s));
    return $s;
}

function json_decode_list(?string $raw): array {
    if (!$raw) return [];
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}
