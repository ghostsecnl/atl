<?php
$slug = (string)($_GET['slug'] ?? '');
$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ?'); $stmt->execute([$slug]); $p = $stmt->fetch();
if (!$p) { echo '<h1>Niet gevonden</h1>'; return; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $pdo->prepare('UPDATE pages SET title=?, meta_title=?, meta_description=?, h1=?, body=?, in_nav=?, sort_order=?, updated_at=? WHERE slug=?')
      ->execute([
        (string)($_POST['title'] ?? ''),
        (string)($_POST['meta_title'] ?? ''),
        (string)($_POST['meta_description'] ?? ''),
        (string)($_POST['h1'] ?? ''),
        (string)($_POST['body'] ?? ''),
        !empty($_POST['in_nav']) ? 1 : 0,
        (int)($_POST['sort_order'] ?? 0),
        date('c'),
        $slug,
    ]);
    $_SESSION['flash'] = ['t'=>'ok','m'=>'Pagina opgeslagen.'];
    header('Location: ' . url('admin.php?p=page_edit&slug=' . urlencode($slug))); exit;
}
?>
<div class="admin-topbar"><h1>Pagina bewerken: <?= h($p['title']) ?></h1>
<a class="btn btn-ghost-ink btn-sm" href="<?= h(url('?p=' . $p['slug'])) ?>" target="_blank">→ Bekijk</a></div>
<form method="post" class="card"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
  <div class="field"><label>Titel</label><input name="title" value="<?= h($p['title']) ?>"></div>
  <div class="field"><label>H1</label><input name="h1" value="<?= h($p['h1']) ?>"></div>
  <div class="field"><label>Meta titel (SEO)</label><input name="meta_title" value="<?= h($p['meta_title']) ?>"></div>
  <div class="field"><label>Meta beschrijving (SEO)</label><input name="meta_description" value="<?= h($p['meta_description']) ?>"></div>
  <div style="display:flex;gap:16px">
    <div class="field" style="flex:1"><label>Volgorde</label><input type="number" name="sort_order" value="<?= (int)$p['sort_order'] ?>"></div>
    <div class="field" style="flex:1"><label>&nbsp;</label><label style="flex-direction:row;align-items:center;gap:8px;font-weight:400"><input type="checkbox" name="in_nav" <?= (int)$p['in_nav']?'checked':'' ?>> In navigatie</label></div>
  </div>
  <div class="field"><label>Inhoud (HTML toegestaan)</label><textarea name="body" rows="20"><?= h($p['body']) ?></textarea></div>
  <button class="btn btn-primary">Opslaan</button>
</form>
