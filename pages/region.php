<?php
declare(strict_types=1);
$slug = preg_replace('/[^a-z0-9\-]/i', '', (string)($slug_value ?? ''));
$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM regions WHERE id = ?');
$stmt->execute([$slug]);
$region = $stmt->fetch();
if (!$region) { http_response_code(404); echo '<div class="container section"><h1>Regio niet gevonden</h1></div>'; return; }
$prices = $pdo->prepare('SELECT p.car, p.van, a.id as airport_id, a.name as airport_name FROM prices p JOIN airports a ON a.id = p.airport_id WHERE p.region_id = ? ORDER BY a.sort_order');
$prices->execute([$region['id']]);
$rows = $prices->fetchAll();
$plaatsen = json_decode_list($region['plaatsen']);
$page_title = 'Airport taxi vanuit ' . $region['name'] . ' | ' . settings_get('company_name');
$page_desc  = 'Vaste tarieven voor luchthavenvervoer vanuit ' . $region['name'] . '.';
?>
<section class="section">
  <div class="container">
    <a class="back-link" href="<?= h(url('')) ?>">← Terug</a>
    <div class="section-head">
      <span class="eyebrow">Regio</span>
      <h1>Airport taxi vanuit <?= h($region['name']) ?></h1>
      <?php if ($plaatsen): ?><p>Onder andere ophaaladressen in: <?= h(implode(', ', $plaatsen)) ?>.</p><?php endif; ?>
    </div>
    <table class="price-table">
      <thead><tr><th>Luchthaven</th><th>Personenauto</th><th>Taxibus</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><a href="<?= h(url('?luchthaven=' . $r['airport_id'])) ?>"><?= h($r['airport_name']) ?></a></td>
          <td><?= h(format_price($r['car'])) ?></td>
          <td><?= h(format_price($r['van'])) ?></td>
          <td><a class="btn btn-primary btn-sm" href="<?= h(url('#boeken')) ?>">Boek</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
