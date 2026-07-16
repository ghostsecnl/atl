<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';

function decide_vehicle(int $passengers, int $luggage, array $s): string {
    $cmp = (int)($s['car_max_passengers'] ?? 4);
    $cml = (int)($s['car_max_luggage'] ?? 4);
    $collect = ($s['collect_luggage'] ?? '1') === '1';
    $fits_p = $passengers <= $cmp;
    $fits_l = !$collect || $luggage <= $cml;
    return ($fits_p && $fits_l) ? 'car' : 'van';
}

/**
 * Compute a quote. Returns ['ok'=>bool, 'quote'=>[...], 'error'=>string]
 */
function compute_quote(array $req): array {
    $pdo = db();
    $s = settings_all();

    $airport_id = trim((string)($req['airport_id'] ?? ''));
    $airport = $pdo->prepare('SELECT * FROM airports WHERE id = ?');
    $airport->execute([$airport_id]);
    $airport = $airport->fetch();
    if (!$airport) return ['ok' => false, 'error' => 'Onbekende luchthaven.'];

    // Resolve region
    $region_id = trim((string)($req['region_id'] ?? ''));
    $region = null;
    if ($region_id !== '') {
        $stmt = $pdo->prepare('SELECT * FROM regions WHERE id = ?');
        $stmt->execute([$region_id]);
        $region = $stmt->fetch() ?: null;
    }
    // Try postcode/plaats matching if no direct region
    if ($region === null) {
        $postcode = strtoupper(preg_replace('/\s+/', '', (string)($req['postcode'] ?? '')));
        $plaats_hint = (string)($req['plaats'] ?? '');
        if ($plaats_hint !== '') {
            $regions = $pdo->query('SELECT * FROM regions ORDER BY sort_order')->fetchAll();
            $n = normalize_str($plaats_hint);
            foreach ($regions as $r) {
                foreach (json_decode_list($r['plaatsen']) as $p) {
                    if (normalize_str($p) === $n) { $region = $r; break 2; }
                }
            }
            if ($region === null) {
                foreach ($regions as $r) {
                    foreach (json_decode_list($r['municipalities']) as $m) {
                        if (normalize_str($m) === $n) { $region = $r; break 2; }
                    }
                }
            }
        }
        unset($postcode);
    }
    if ($region === null) {
        return ['ok' => false, 'out_of_area' => true, 'error' => 'Kies uw regio uit de lijst.'];
    }

    $stmt = $pdo->prepare('SELECT * FROM prices WHERE region_id = ? AND airport_id = ?');
    $stmt->execute([$region['id'], $airport['id']]);
    $price = $stmt->fetch();
    if (!$price) return ['ok' => false, 'error' => 'Voor deze combinatie is geen tarief beschikbaar.'];

    $passengers = max(1, min(8, (int)($req['passengers'] ?? 1)));
    $luggage    = max(0, min(16, (int)($req['luggage'] ?? 0)));
    $vehicle = decide_vehicle($passengers, $luggage, $s);
    $base = $vehicle === 'car' ? (float)$price['car'] : (float)$price['van'];
    if ($base <= 0) return ['ok' => false, 'error' => 'Voor deze combinatie is nog geen tarief ingesteld.'];

    $trip_type = ($req['trip_type'] ?? 'oneway') === 'return' ? 'return' : 'oneway';
    $direction = ($req['direction'] ?? 'to_airport') === 'to_home' ? 'to_home' : 'to_airport';
    $final = $base;
    if ($trip_type === 'return') $final = round($base * (float)($s['return_multiplier'] ?? 2), 2);

    // Address string built from postcode+huisnr if present
    $address = trim((string)($req['address'] ?? ''));
    if ($address === '') {
        $postcode = trim((string)($req['postcode'] ?? ''));
        $huis = trim((string)($req['house_number'] ?? ''));
        if ($postcode !== '' || $huis !== '') $address = trim($postcode . ' ' . $huis);
    }

    return [
        'ok' => true,
        'quote' => [
            'region_id'    => $region['id'],
            'region_name'  => $region['name'],
            'airport_id'   => $airport['id'],
            'airport_name' => $airport['name'],
            'direction'    => $direction,
            'trip_type'    => $trip_type,
            'passengers'   => $passengers,
            'luggage'      => $luggage,
            'vehicle'      => $vehicle,
            'price'        => (float)$final,
            'currency'     => 'EUR',
            'address'      => $address,
        ],
    ];
}

function next_reference(string $prefix): string {
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $row = $pdo->query('SELECT value FROM settings WHERE key = "booking_counter"')->fetch();
        $n = (int)($row['value'] ?? 0) + 1;
        $stmt = $pdo->prepare('INSERT INTO settings(key,value) VALUES("booking_counter",?) ON CONFLICT(key) DO UPDATE SET value=excluded.value');
        $stmt->execute([(string)$n]);
        $pdo->commit();
    } catch (\Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    $year = (int)date('Y');
    return sprintf('%s-%d-%04d', $prefix, $year, $n);
}
