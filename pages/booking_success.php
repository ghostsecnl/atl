<?php
declare(strict_types=1);
$ref = trim((string)($_GET['ref'] ?? ''));
$stmt = db()->prepare('SELECT * FROM bookings WHERE reference = ?');
$stmt->execute([$ref]);
$b = $stmt->fetch();
$page_title = 'Boekingsbevestiging';
?>
<section class="section">
  <div class="container article">
    <a class="back-link" href="<?= h(url('')) ?>">← Terug</a>
    <?php if (!$b): ?>
      <h1>Boeking niet gevonden</h1>
      <p>De referentie <?= h($ref) ?> is onbekend.</p>
    <?php else: ?>
      <h1>✅ Bedankt voor uw boeking</h1>
      <p>Uw boeking met referentie <strong><?= h($b['reference']) ?></strong> is bevestigd. U ontvangt een bevestiging per e-mail op <strong><?= h($b['customer_email']) ?></strong>.</p>
      <div class="success-card">
        <h2>Uw rit</h2>
        <table class="details">
          <tr><td>Ophaaladres</td><td><?= h($b['address'] ?: ($b['postcode'] . ' ' . $b['house_number'])) ?></td></tr>
          <tr><td>Luchthaven</td><td><?= h($b['airport_name']) ?></td></tr>
          <tr><td>Richting</td><td><?= $b['direction']==='to_home'?'Vanaf de luchthaven':'Naar de luchthaven' ?></td></tr>
          <tr><td>Type rit</td><td><?= $b['trip_type']==='return'?'Retour':'Enkele reis' ?></td></tr>
          <tr><td>Ophalen</td><td><?= h($b['pickup_date'] . ' ' . $b['pickup_time']) ?></td></tr>
          <?php if ($b['return_date']): ?><tr><td>Retour</td><td><?= h($b['return_date'] . ' ' . $b['return_time']) ?></td></tr><?php endif; ?>
          <tr><td>Personen / bagage</td><td><?= (int)$b['passengers'] ?> / <?= (int)$b['luggage'] ?></td></tr>
          <tr><td>Voertuig</td><td><?= $b['vehicle']==='van'?'Taxibus':'Personenauto' ?></td></tr>
          <?php if ($b['flight_number']): ?><tr><td>Vluchtnummer</td><td><?= h($b['flight_number']) ?></td></tr><?php endif; ?>
          <tr><td>Betaling</td><td><?= $b['payment_method']==='pin'?'Pinnen in de taxi':'Contant in de taxi' ?></td></tr>
          <tr><td><strong>Totaalprijs</strong></td><td><strong><?= h(format_price($b['price'])) ?></strong></td></tr>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>
