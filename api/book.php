<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/includes/pricing.php';
require_once dirname(__DIR__) . '/includes/mailer.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$name = trim((string)($data['customer_name'] ?? ''));
$phone = trim((string)($data['customer_phone'] ?? ''));
$email = trim((string)($data['customer_email'] ?? ''));

if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'error' => 'Ongeldige invoer']);
    exit;
}

$q = compute_quote($data);
if (empty($q['ok'])) { echo json_encode($q); exit; }
$quote = $q['quote'];

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO bookings (
    reference, created_at, address, region_id, region_name, airport_id, airport_name, 
    direction, trip_type, postcode, house_number, passengers, luggage, vehicle, 
    price, currency, customer_name, customer_email, customer_phone, pickup_date, 
    pickup_time, return_date, return_time, flight_number, payment_method, notes, status, 
    luggage1, city
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

$ok = $stmt->execute([
    next_reference(settings_get('booking_ref_prefix', 'ZLT')), 
    date('c'), 
    $quote['address'], 
    $quote['region_id'], 
    $quote['region_name'], 
    $quote['airport_id'], 
    $quote['airport_name'],
    $quote['direction'], 
    $quote['trip_type'], 
    ($data['postcode'] ?? ''), 
    ($data['house_number'] ?? ''), 
    (int)($quote['passengers'] ?? 0), 
    (int)($data['luggage'] ?? 0), 
    $quote['vehicle'], 
    (float)($quote['price'] ?? 0), 
    $quote['currency'], 
    $name, $email, $phone,
    ($data['pickup_date'] ?? ''), 
    ($data['pickup_time'] ?? ''), 
    ($data['return_date'] ?? ''), 
    ($data['return_time'] ?? ''), 
    ($data['flight_number'] ?? ''), 
    (($data['payment_method'] ?? 'cash') === 'pin' ? 'pin' : 'cash'),
    ($data['notes'] ?? ''), 
    'new', 
    (int)($data['luggage1'] ?? 0), 
    substr((string)($data['city'] ?? ''), 0, 100)
]);

if ($ok) {
    echo json_encode(['ok' => true, 'message' => 'Boeking succesvol opgeslagen.']);
} else {
    echo json_encode(['ok' => false, 'error' => 'Database insert mislukt.']);
}