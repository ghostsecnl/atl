<?php
// Regions bulk edit
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $ids = $_POST['id'] ?? []; $names = $_POST['name'] ?? []; $reps = $_POST['rep'] ?? [];
    $gems = $_POST['gemeenten'] ?? []; $plaatsen = $_POST['plaatsen'] ?? []; $orders = $_POST['sort'] ?? [];
    if (!empty($_POST['delete'])) {
        foreach ((array)$_POST['delete'] as $del) {
            $pdo->prepare('DELETE FROM regions WHERE id = ?')->execute([$del]);
        }
    }
    foreach ($ids as $i => $id) {
        $id = trim((string)$id); if ($id === '') continue;
        $name = (string)($names[$i] ?? ''); $rep = (string)($reps[$i] ?? '');
        $gemArr = array_filter(array_map('trim', explode(',', (string)($gems[$i] ?? ''))));
        $pltArr = array_filter(array_map('trim', explode(',', (string)($plaatsen[$i] ?? ''))));
        $sort = (int)($orders[$i] ?? 0);
        $exists = $pdo->prepare('SELECT 1 FROM regions WHERE id = ?'); $exists->execute([$id]);
        if ($exists->fetch()) {
            $pdo->prepare('UPDATE regions SET name=?, representative_place=?, municipalities=?, plaatsen=?, sort_order=? WHERE id=?')
                ->execute([$name, $rep, json_encode(array_values($gemArr), JSON_UNESCAPED_UNICODE), json_encode(array_values($pltArr), JSON_UNESCAPED_UNICODE), $sort, $id]);
        } else {
            $pdo->prepare('INSERT INTO regions(id,name,representative_place,municipalities,plaatsen,sort_order) VALUES(?,?,?,?,?,?)')
                ->execute([$id, $name, $rep, json_encode(array_values($gemArr), JSON_UNESCAPED_UNICODE), json_encode(array_values($pltArr), JSON_UNESCAPED_UNICODE), $sort]);
            // seed prices for new region
            foreach ($pdo->query('SELECT id FROM airports')->fetchAll() as $a) {
                $pdo->prepare('INSERT OR IGNORE INTO prices(region_id,airport_id) VALUES(?,?)')->execute([$id, $a['id']]);
            }
        }
    }
    $_SESSION['flash'] = ['t'=>'ok','m'=>'Regio\'s opgeslagen.'];
    header('Location: ' . url('admin.php?p=regions')); exit;
}
$rows = $pdo->query('SELECT * FROM regions ORDER BY sort_order, name')->fetchAll();
?>
<div class="admin-topbar"><h1>Regio's</h1></div>
<form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<div class="card" style="padding:0"><div style="overflow-x:auto"><table class="table">
<thead><tr><th>ID (slug)</th><th>Naam</th><th>Rep. plaats</th><th>Gemeenten (komma)</th><th>Plaatsen (komma)</th><th>Volgorde</th><th>Verwijder</th></tr></thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr>
  <td><input name="id[]" value="<?= h($r['id']) ?>" readonly style="background:#f1f5f9;border:0;padding:6px"></td>
  <td><input name="name[]" value="<?= h($r['name']) ?>" style="width:100%;padding:6px"></td>
  <td><input name="rep[]" value="<?= h($r['representative_place']) ?>" style="width:100%;padding:6px"></td>
  <td><input name="gemeenten[]" value="<?= h(implode(', ', json_decode_list($r['municipalities']))) ?>" style="width:100%;padding:6px"></td>
  <td><input name="plaatsen[]" value="<?= h(implode(', ', json_decode_list($r['plaatsen']))) ?>" style="width:100%;padding:6px"></td>
  <td><input name="sort[]" type="number" value="<?= (int)$r['sort_order'] ?>" style="width:60px;padding:6px"></td>
  <td style="text-align:center"><input type="checkbox" name="delete[]" value="<?= h($r['id']) ?>"></td>
</tr>
<?php endforeach; ?>
<tr style="background:#f1f5f9">
  <td><input name="id[]" placeholder="nieuwe-slug" style="width:100%;padding:6px"></td>
  <td><input name="name[]" placeholder="Naam" style="width:100%;padding:6px"></td>
  <td><input name="rep[]" style="width:100%;padding:6px"></td>
  <td><input name="gemeenten[]" style="width:100%;padding:6px"></td>
  <td><input name="plaatsen[]" style="width:100%;padding:6px"></td>
  <td><input name="sort[]" type="number" value="99" style="width:60px;padding:6px"></td>
  <td></td>
</tr>
</tbody></table></div></div>
<button class="btn btn-primary">Opslaan</button>
</form>
