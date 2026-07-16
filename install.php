<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/seed_data.php';

db_init();
$pdo = db();

// Seed settings defaults + email templates
$defaults = SETTING_DEFAULTS;
foreach ($defaults as $k => $v) {
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO settings(key,value) VALUES(?,?)');
    $stmt->execute([$k, $v]);
}
$tpls = default_email_templates();
foreach ($tpls as $k => $v) {
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO settings(key,value) VALUES(?,?)');
    $stmt->execute(['tpl_' . $k, $v]);
}

// Regions
$existing = (int)$pdo->query('SELECT COUNT(*) c FROM regions')->fetch()['c'];
if ($existing === 0) {
    $ins = $pdo->prepare('INSERT INTO regions(id,name,representative_place,municipalities,plaatsen,sort_order) VALUES(?,?,?,?,?,?)');
    foreach (seed_regions_data() as $i => $r) {
        [$id,$name,$rep,$gem,$plaatsen] = $r;
        $ins->execute([$id,$name,$rep,json_encode($gem, JSON_UNESCAPED_UNICODE),json_encode($plaatsen, JSON_UNESCAPED_UNICODE),$i]);
    }
}

// Airports
$existing = (int)$pdo->query('SELECT COUNT(*) c FROM airports')->fetch()['c'];
if ($existing === 0) {
    $ins = $pdo->prepare('INSERT INTO airports(id,name,place_query,sort_order) VALUES(?,?,?,?)');
    foreach (seed_airports_data() as $i => $a) {
        [$id,$name,$q] = $a;
        $ins->execute([$id,$name,$q,$i]);
    }
}

// Prices (only if empty)
$existing = (int)$pdo->query('SELECT COUNT(*) c FROM prices')->fetch()['c'];
if ($existing === 0) {
    $ins = $pdo->prepare('INSERT INTO prices(region_id,airport_id,car,van,distance_km) VALUES(?,?,?,?,?)');
    foreach (seed_default_prices() as $rid => $airports) {
        foreach ($airports as $aid => $vals) {
            $ins->execute([$rid, $aid, $vals['car'], $vals['van'], $vals['distance_km']]);
        }
    }
}

// Pages
$existing = (int)$pdo->query('SELECT COUNT(*) c FROM pages')->fetch()['c'];
if ($existing === 0) {
    $ins = $pdo->prepare('INSERT INTO pages(slug,title,meta_title,meta_description,h1,body,sort_order,in_nav,updated_at) VALUES(?,?,?,?,?,?,?,?,?)');
    foreach (seed_pages_data() as $p) {
        $ins->execute([
            $p['slug'],$p['title'],$p['meta_title'],$p['meta_description'],
            $p['h1'],$p['body'],(int)$p['sort_order'],(int)$p['in_nav'],
            date('c'),
        ]);
    }
}

// Admin user (default admin / admin)
$existing = (int)$pdo->query('SELECT COUNT(*) c FROM admins')->fetch()['c'];
if ($existing === 0) {
    $ins = $pdo->prepare('INSERT INTO admins(username,password_hash,created_at) VALUES(?,?,?)');
    $ins->execute(['admin', password_hash('admin', PASSWORD_BCRYPT), date('c')]);
}

?><!doctype html>
<html lang="nl"><meta charset="utf-8"><title>Installatie voltooid</title>
<style>body{font-family:Arial,sans-serif;background:#f1f5f9;color:#0f172a;padding:40px;max-width:720px;margin:auto}h1{color:#0b1730}code{background:#0b1730;color:#f5c518;padding:2px 8px;border-radius:4px}.warn{background:#fee2e2;border:1px solid #ef4444;padding:16px;border-radius:8px;color:#991b1b;margin:16px 0}a{color:#2563eb}</style>
<h1>✅ Installatie voltooid</h1>
<p>De database <code>data/zlat.sqlite</code> is aangemaakt en gevuld met standaardgegevens.</p>
<p><a href="<?= h(url('')) ?>">→ Ga naar de site</a> · <a href="<?= h(url('admin.php')) ?>">→ Open admin</a></p>
<div class="warn"><strong>Belangrijk:</strong> verwijder nu <code>install.php</code> uit de webroot om misbruik te voorkomen.</div>
<h2>Standaard admin</h2>
<p>Gebruiker: <code>admin</code> — Wachtwoord: <code>admin</code><br>Wijzig direct na inloggen het wachtwoord via <strong>Instellingen</strong>.</p>
