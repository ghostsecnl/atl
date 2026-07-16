<?php
// SQLite verbinding
declare(strict_types=1);

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    $path = $dir . '/zlat.sqlite';
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    return $pdo;
}

function db_init(): void {
    $pdo = db();
    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL DEFAULT ""
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS regions (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        municipalities TEXT NOT NULL DEFAULT "[]",
        plaatsen TEXT NOT NULL DEFAULT "[]",
        representative_place TEXT NOT NULL DEFAULT "",
        sort_order INTEGER NOT NULL DEFAULT 0
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS airports (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        place_query TEXT NOT NULL DEFAULT "",
        sort_order INTEGER NOT NULL DEFAULT 0
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS prices (
        region_id TEXT NOT NULL,
        airport_id TEXT NOT NULL,
        car REAL NOT NULL DEFAULT 0,
        van REAL NOT NULL DEFAULT 0,
        distance_km REAL,
        PRIMARY KEY (region_id, airport_id),
        FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE CASCADE,
        FOREIGN KEY (airport_id) REFERENCES airports(id) ON DELETE CASCADE
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference TEXT UNIQUE NOT NULL,
        created_at TEXT NOT NULL,
        address TEXT NOT NULL DEFAULT "",
        region_id TEXT NOT NULL DEFAULT "",
        region_name TEXT NOT NULL DEFAULT "",
        airport_id TEXT NOT NULL DEFAULT "",
        airport_name TEXT NOT NULL DEFAULT "",
        direction TEXT NOT NULL DEFAULT "to_airport",
        trip_type TEXT NOT NULL DEFAULT "oneway",
        postcode TEXT NOT NULL DEFAULT "",
        house_number TEXT NOT NULL DEFAULT "",
        passengers INTEGER NOT NULL DEFAULT 1,
        luggage INTEGER NOT NULL DEFAULT 0,
        vehicle TEXT NOT NULL DEFAULT "car",
        price REAL NOT NULL DEFAULT 0,
        currency TEXT NOT NULL DEFAULT "EUR",
        customer_name TEXT NOT NULL DEFAULT "",
        customer_email TEXT NOT NULL DEFAULT "",
        customer_phone TEXT NOT NULL DEFAULT "",
        pickup_date TEXT NOT NULL DEFAULT "",
        pickup_time TEXT NOT NULL DEFAULT "",
        return_date TEXT NOT NULL DEFAULT "",
        return_time TEXT NOT NULL DEFAULT "",
        flight_number TEXT NOT NULL DEFAULT "",
        payment_method TEXT NOT NULL DEFAULT "cash",
        notes TEXT NOT NULL DEFAULT "",
        status TEXT NOT NULL DEFAULT "new"
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS pages (
        slug TEXT PRIMARY KEY,
        title TEXT NOT NULL DEFAULT "",
        meta_title TEXT NOT NULL DEFAULT "",
        meta_description TEXT NOT NULL DEFAULT "",
        h1 TEXT NOT NULL DEFAULT "",
        body TEXT NOT NULL DEFAULT "",
        sort_order INTEGER NOT NULL DEFAULT 0,
        in_nav INTEGER NOT NULL DEFAULT 0,
        updated_at TEXT NOT NULL
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        created_at TEXT NOT NULL
    )');
}
