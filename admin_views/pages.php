<?php
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (!empty($_POST['delete'])) {
        foreach ((array)$_POST['delete'] as $slug) $pdo->prepare('DELETE FROM pages WHERE slug = ?')->execute([$slug]);
        $_SESSION['flash'] = ['t'=>'ok','m'=>'Pagina(\'s) verwijderd.'];
        header('Location: ' . url('admin.php?p=pages')); exit;
    }
    if (!empty($_POST['new_slug'])) {
        $slug = strtolower(preg_replace('/[^a-z0-9\-]/i', '-', trim((string)$_POST['new_slug'])));
        $title = (string)($_POST['new_title'] ?? $slug);
        $pdo->prepare('INSERT OR IGNORE INTO pages(slug,title,meta_title,h1,body,in_nav,sort_order,updated_at) VALUES(?,?,?,?,?,?,?,?)')
            ->execute([$slug, $title, $title, $title, '<p>Nieuwe pagina.</p>', 1, 99, date('c')]);
        header('Location: ' . url('admin.php?p=page_edit&slug=' . urlencode($slug))); exit;
    }
}
$rows = $pdo->query('SELECT slug,title,in_nav,sort_order,updated_at FROM pages ORDER BY sort_order')->fetchAll();
?>
<div class="admin-topbar"><h1>Pagina's</h1></div>
<div class="card"><h2>Nieuwe pagina</h2>
<form method="post" style="display:flex;gap:10px">
<input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<input name="new_slug" placeholder="voorbeeld-slug" required style="flex:1;padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<input name="new_title" placeholder="Titel" required style="flex:1;padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<button class="btn btn-primary btn-sm">Aanmaken</button>
</form></div>
<form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<div class="card" style="padding:0"><table class="table">
<thead><tr><th>Slug</th><th>Titel</th><th>In menu</th><th>Volgorde</th><th>Bewerkt</th><th></th><th>Verwijder</th></tr></thead><tbody>
<?php foreach ($rows as $p): ?>
<tr>
  <td><code><?= h($p['slug']) ?></code></td>
  <td><?= h($p['title']) ?></td>
  <td><?= (int)$p['in_nav'] ? '✓' : '' ?></td>
  <td><?= (int)$p['sort_order'] ?></td>
  <td><?= h(substr((string)$p['updated_at'],0,16)) ?></td>
  <td><a class="btn btn-ghost-ink btn-sm" href="<?= h(url('admin.php?p=page_edit&slug=' . urlencode($p['slug']))) ?>">Bewerk</a></td>
  <td style="text-align:center"><input type="checkbox" name="delete[]" value="<?= h($p['slug']) ?>"></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<button class="btn btn-ghost-ink" style="color:#991b1b" onclick="return confirm('Verwijderen?')">Geselecteerde verwijderen</button>
</form>
