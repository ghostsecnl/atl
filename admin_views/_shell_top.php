<!doctype html>
<html lang="nl"><head><meta charset="utf-8"><title>Admin — <?= h(settings_get('company_name')) ?></title>
<link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>"></head>
<body class="admin-body">
<div class="admin-shell">
<aside class="admin-side">
  <div class="brand"><span class="brand__icon">&#9992;</span><span class="brand__name" style="color:#fff">Admin</span></div>
  <?php $current = $_GET['p'] ?? 'dashboard'; $nav = [
    'dashboard'=>'Boekingen','regions'=>'Regio\'s','airports'=>'Luchthavens','prices'=>'Prijzen',
    'pages'=>'Pagina\'s','email_templates'=>'E-mailsjablonen','settings'=>'Instellingen'
  ]; foreach ($nav as $k => $lbl): ?>
    <a href="<?= h(url('admin.php?p=' . $k)) ?>" class="<?= $current === $k ? 'active' : '' ?>"><?= h($lbl) ?></a>
  <?php endforeach; ?>
  <a href="<?= h(url('')) ?>" target="_blank" style="margin-top:20px">→ Bekijk site</a>
  <a href="<?= h(url('admin.php?p=logout')) ?>" style="color:#f5c518">Uitloggen</a>
</aside>
<main class="admin-main">
<?php
$flash = $_SESSION['flash'] ?? null; if ($flash) { unset($_SESSION['flash']); }
if ($flash): ?><div class="msg msg-<?= h($flash['t']) ?>"><?= h($flash['m']) ?></div><?php endif; ?>
