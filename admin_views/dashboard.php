<?php
// Bookings list / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($act === 'delete' && $id) {
        $stmt = db()->prepare('DELETE FROM bookings WHERE id = ?'); $stmt->execute([$id]);
        $_SESSION['flash'] = ['t'=>'ok','m'=>'Boeking verwijderd.'];
    } elseif ($act === 'status' && $id) {
        $s = substr((string)($_POST['status'] ?? ''), 0, 24);
        $stmt = db()->prepare('UPDATE bookings SET status = ? WHERE id = ?'); $stmt->execute([$s, $id]);
        $_SESSION['flash'] = ['t'=>'ok','m'=>'Status bijgewerkt.'];
    }
    header('Location: ' . url('admin.php')); exit;
}
$rows = db()->query('SELECT * FROM bookings ORDER BY id DESC LIMIT 200')->fetchAll();
?>
<div class="admin-topbar"><h1>Boekingen (<?= count($rows) ?>)</h1></div>
<div class="card" style="padding:0">
<table class="table">
  <thead><tr><th>Ref</th><th>Datum</th><th>Klant</th><th>Rit</th><th>Prijs</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php if (!$rows): ?><tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">Nog geen boekingen.</td></tr><?php endif; ?>
  <?php foreach ($rows as $b): ?>
    <tr>
      <td><a href="<?= h(url('admin.php?p=booking_view&id=' . (int)$b['id'])) ?>"><strong><?= h($b['reference']) ?></strong></a></td>
      <td><?= h(substr((string)$b['created_at'],0,16)) ?></td>
      <td><?= h($b['customer_name']) ?><br><small><?= h($b['customer_email']) ?></small></td>
      <td><?= h($b['region_name']) ?> → <?= h($b['airport_name']) ?><br><small><?= h($b['pickup_date'] . ' ' . $b['pickup_time']) ?> · <?= $b['vehicle']==='van'?'Bus':'Auto' ?></small></td>
      <td><?= h(format_price($b['price'])) ?></td>
      <td><span class="badge badge-<?= h($b['status']) ?>"><?= h($b['status']) ?></span></td>
      <td class="actions">
        <a class="btn btn-ghost-ink btn-sm" href="<?= h(url('admin.php?p=booking_view&id=' . (int)$b['id'])) ?>">Bekijk</a>
        <form method="post" onsubmit="return confirm('Verwijderen?')" style="display:inline">
          <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
          <button class="btn btn-ghost-ink btn-sm" style="color:#991b1b">✕</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
