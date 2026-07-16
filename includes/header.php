<?php
declare(strict_types=1);
if (!function_exists('h')) require_once __DIR__ . '/functions.php';
$s = settings_all();
$nav = db()->query('SELECT slug,title FROM pages WHERE in_nav = 1 ORDER BY sort_order')->fetchAll();
$page_title = $page_title ?? ($s['company_name'] . ' | Voordelig luchthavenvervoer 24/7');
$page_desc  = $page_desc  ?? 'Boek voordelig airport taxivervoer van en naar Schiphol, Eindhoven, Dusseldorf en Brussel. Vaste tarieven, 24/7 beschikbaar.';
?><!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h($page_title) ?></title>
<meta name="description" content="<?= h($page_desc) ?>">
<link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>">
<link rel="icon" type="image/x-icon" href="/AirportTaxiLimburg.ico">
</head>
<body>
<header class="site-header">
 <div class="container header__inner">
    <a href="https://taxisittard-geleen.com/" class="brand">
      <span>✈</span>
      <span>Airport Taxi Limburg</span>
    </a>

    <!-- Hamburger menu knop -->
 <button class="menu-toggle" onclick="this.closest('.site-header').classList.toggle('active')">
  ☰
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.menu-toggle');
    const header = document.querySelector('header');
    
    toggle.addEventListener('click', function() {
        header.classList.toggle('active');
    });
});
</script>

    <nav class="site-nav">
      <a href="https://taxisittard-geleen.com/">Home</a>
      <a href="?p=airport-taxis">Airport taxi's</a>
      <a href="?p=onze-service">Onze service</a>
      <a href="?p=zakelijk-vervoer">Zakelijk vervoer</a>
      <a href="?p=veelgestelde-vragen">Veelgestelde vragen</a>
      <a href="?p=contact">Contact</a>
    </nav>
    
    <a href="tel:0622334566" class="btn btn-gold btn-sm">06 22 33 45 66</a>
  </div>
<script>
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('.site-header').classList.toggle('active');
    });
</script>
</header>
<main>
