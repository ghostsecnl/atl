<?php
declare(strict_types=1);
$slug = preg_replace('/[^a-z0-9\-]/i', '', (string)($slug_value ?? ''));
$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM airports WHERE id = ?');
$stmt->execute([$slug]);
$airport = $stmt->fetch();
if (!$airport) { http_response_code(404); echo '<div class="container section"><h1>Luchthaven niet gevonden</h1></div>'; return; }
$prices = $pdo->prepare('SELECT p.car, p.van, r.id as region_id, r.name as region_name FROM prices p JOIN regions r ON r.id = p.region_id WHERE p.airport_id = ? ORDER BY r.sort_order');
$prices->execute([$airport['id']]);
$rows = $prices->fetchAll();
$page_title = 'Taxi naar ' . $airport['name'] . ' | ' . settings_get('company_name');
$page_desc  = 'Vaste tarieven voor taxi naar ' . $airport['name'] . ' vanuit heel Zuid- en Midden-Limburg.';
?>
<section class="section">
  <div class="container">
    <a class="back-link" href="<?= h(url('')) ?>">← Terug</a>
    <div class="section-head">
      <span class="eyebrow">Luchthaven</span>
      <h1>Taxi naar <?= h($airport['name']) ?></h1>
      <p>Bekijk de vaste tarieven per regio voor een enkele reis. Retour = 2× de enkele prijs.</p>
    </div>
    <table class="price-table">
      <thead><tr><th>Regio</th><th>Personenauto</th><th>Taxibus</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><a href="<?= h(url('?regio=' . $r['region_id'])) ?>"><?= h($r['region_name']) ?></a></td>
          <td><?= h(format_price($r['car'])) ?></td>
          <td><?= h(format_price($r['van'])) ?></td>
          <td><a class="btn btn-primary btn-sm" href="<?= h(url('#boeken')) ?>">Boek</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
