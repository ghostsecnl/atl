<?php
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $ids = $_POST['id'] ?? []; $names = $_POST['name'] ?? []; $qs = $_POST['q'] ?? []; $orders = $_POST['sort'] ?? [];
    if (!empty($_POST['delete'])) {
        foreach ((array)$_POST['delete'] as $del) $pdo->prepare('DELETE FROM airports WHERE id = ?')->execute([$del]);
    }
    foreach ($ids as $i => $id) {
        $id = trim((string)$id); if ($id === '') continue;
        $name = (string)($names[$i] ?? ''); $q = (string)($qs[$i] ?? ''); $sort = (int)($orders[$i] ?? 0);
        $exists = $pdo->prepare('SELECT 1 FROM airports WHERE id = ?'); $exists->execute([$id]);
        if ($exists->fetch()) {
            $pdo->prepare('UPDATE airports SET name=?, place_query=?, sort_order=? WHERE id=?')->execute([$name,$q,$sort,$id]);
        } else {
            $pdo->prepare('INSERT INTO airports(id,name,place_query,sort_order) VALUES(?,?,?,?)')->execute([$id,$name,$q,$sort]);
            foreach ($pdo->query('SELECT id FROM regions')->fetchAll() as $r) {
                $pdo->prepare('INSERT OR IGNORE INTO prices(region_id,airport_id) VALUES(?,?)')->execute([$r['id'], $id]);
            }
        }
    }
    $_SESSION['flash'] = ['t'=>'ok','m'=>'Luchthavens opgeslagen.'];
    header('Location: ' . url('admin.php?p=airports')); exit;
}
$rows = $pdo->query('SELECT * FROM airports ORDER BY sort_order, name')->fetchAll();
?>
<div class="admin-topbar"><h1>Luchthavens</h1></div>
<form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<div class="card" style="padding:0"><table class="table">
<thead><tr><th>ID</th><th>Naam</th><th>Place query</th><th>Volgorde</th><th>Verwijder</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
  <td><input name="id[]" value="<?= h($r['id']) ?>" readonly style="background:#f1f5f9;border:0;padding:6px;width:80px"></td>
  <td><input name="name[]" value="<?= h($r['name']) ?>" style="width:100%;padding:6px"></td>
  <td><input name="q[]" value="<?= h($r['place_query']) ?>" style="width:100%;padding:6px"></td>
  <td><input name="sort[]" type="number" value="<?= (int)$r['sort_order'] ?>" style="width:60px;padding:6px"></td>
  <td style="text-align:center"><input type="checkbox" name="delete[]" value="<?= h($r['id']) ?>"></td>
</tr>
<?php endforeach; ?>
<tr style="background:#f1f5f9">
  <td><input name="id[]" placeholder="slug" style="width:100%;padding:6px"></td>
  <td><input name="name[]" placeholder="Naam" style="width:100%;padding:6px"></td>
  <td><input name="q[]" style="width:100%;padding:6px"></td>
  <td><input name="sort[]" type="number" value="99" style="width:60px;padding:6px"></td>
  <td></td>
</tr>
</tbody></table></div>
<button class="btn btn-primary">Opslaan</button>
</form>
