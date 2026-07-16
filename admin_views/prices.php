<?php
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $mat = $_POST['m'] ?? [];
    foreach ($mat as $rid => $airports) {
        foreach ($airports as $aid => $vals) {
            $car = (float)($vals['car'] ?? 0); $van = (float)($vals['van'] ?? 0);
            $existing = $pdo->prepare('SELECT 1 FROM prices WHERE region_id = ? AND airport_id = ?');
            $existing->execute([$rid, $aid]);
            if ($existing->fetch()) {
                $pdo->prepare('UPDATE prices SET car = ?, van = ? WHERE region_id = ? AND airport_id = ?')
                    ->execute([$car, $van, $rid, $aid]);
            } else {
                $pdo->prepare('INSERT INTO prices(region_id, airport_id, car, van) VALUES(?,?,?,?)')
                    ->execute([$rid, $aid, $car, $van]);
            }
        }
    }
    $_SESSION['flash'] = ['t'=>'ok','m'=>'Prijzen opgeslagen.'];
    header('Location: ' . url('admin.php?p=prices')); exit;
}
$regions = $pdo->query('SELECT id,name FROM regions ORDER BY sort_order')->fetchAll();
$airports = $pdo->query('SELECT id,name FROM airports ORDER BY sort_order')->fetchAll();
$prices = [];
foreach ($pdo->query('SELECT region_id,airport_id,car,van FROM prices')->fetchAll() as $p) {
    $prices[$p['region_id']][$p['airport_id']] = ['car'=>$p['car'],'van'=>$p['van']];
}
?>
<div class="admin-topbar"><h1>Prijzen</h1></div>
<div class="card"><p style="margin:0;color:#556274;font-size:14px">Per cel: bovenste = personenauto (car), onderste = taxibus (van). Alle bedragen in €.</p></div>
<form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<div class="card matrix" style="padding:0">
<table><thead><tr><th style="min-width:180px">Regio</th>
<?php foreach ($airports as $a): ?><th><?= h($a['name']) ?></th><?php endforeach; ?>
</tr></thead><tbody>
<?php foreach ($regions as $r): ?>
<tr><td style="background:#f1f5f9"><strong><?= h($r['name']) ?></strong></td>
<?php foreach ($airports as $a):
    $c = (float)($prices[$r['id']][$a['id']]['car'] ?? 0);
    $v = (float)($prices[$r['id']][$a['id']]['van'] ?? 0); ?>
  <td>
    <div class="cell-pair">
      <input type="number" step="1" min="0" name="m[<?= h($r['id']) ?>][<?= h($a['id']) ?>][car]" value="<?= h((string)$c) ?>" title="Personenauto">
      <input type="number" step="1" min="0" name="m[<?= h($r['id']) ?>][<?= h($a['id']) ?>][van]" value="<?= h((string)$v) ?>" title="Taxibus">
    </div>
  </td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<button class="btn btn-primary">Opslaan</button>
</form>
