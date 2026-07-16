<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM bookings WHERE id = ?'); $stmt->execute([$id]); $b = $stmt->fetch();
if (!$b) { echo '<h1>Niet gevonden</h1>'; return; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $s = substr((string)($_POST['status'] ?? ''), 0, 24);
    db()->prepare('UPDATE bookings SET status = ? WHERE id = ?')->execute([$s, $id]);
    $_SESSION['flash'] = ['t'=>'ok','m'=>'Status bijgewerkt.'];
    header('Location: ' . url('admin.php?p=booking_view&id=' . $id)); exit;
}
?>
<div class="admin-topbar"><h1>Boeking <?= h($b['reference']) ?></h1>
  <a class="btn btn-ghost-ink btn-sm" href="<?= h(url('admin.php')) ?>">← Terug</a></div>
<div class="card">
  <form method="post" style="display:flex;gap:10px;align-items:end;margin-bottom:12px">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <div class="field" style="margin:0">
      <label>Status</label>
      <select name="status">
        <?php foreach (['new','confirmed','completed','cancelled'] as $s): ?>
          <option value="<?= h($s) ?>" <?= $b['status']===$s?'selected':'' ?>><?= h($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary btn-sm">Opslaan</button>
  </form>
  <table class="details">
    <?php foreach ([
      'Klant'=>$b['customer_name'],'E-mail'=>$b['customer_email'],'Telefoon'=>$b['customer_phone'],
      'Aangemaakt'=>$b['created_at'],'Referentie'=>$b['reference'],'Status'=>$b['status'],
      'Ophaaladres'=>$b['address'],'Postcode / huisnr'=>$b['postcode'].' '.$b['house_number'],
      'Regio'=>$b['region_name'],'Luchthaven'=>$b['airport_name'],
      'Richting'=>($b['direction']==='to_home'?'Vanaf luchthaven':'Naar luchthaven'),
      'Type rit'=>($b['trip_type']==='return'?'Retour':'Enkele reis'),
      'Ophalen'=>$b['pickup_date'].' '.$b['pickup_time'],
      'Retour'=>$b['return_date'].' '.$b['return_time'],
      'Personen / bagage'=>$b['passengers'].' / '.$b['luggage'],
      'Voertuig'=>($b['vehicle']==='van'?'Taxibus':'Personenauto'),
      'Vluchtnummer'=>$b['flight_number'],
      'Betaling'=>($b['payment_method']==='pin'?'Pin':'Contant'),
      'Prijs'=>format_price($b['price']),
      'Opmerkingen'=>$b['notes'],
    ] as $lbl => $val): ?>
      <tr><td><?= h($lbl) ?></td><td><?= h((string)$val) ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
