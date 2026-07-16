<?php
declare(strict_types=1);
$slug = preg_replace('/[^a-z0-9\-_]/i', '', (string)($page ?? ''));
$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ?');
$stmt->execute([$slug]);
$p = $stmt->fetch();
if (!$p) {
    http_response_code(404);
    $page_title = 'Pagina niet gevonden';
    ?>
    <section class="section"><div class="container"><h1>Pagina niet gevonden</h1><p><a href="<?= h(url('')) ?>">Terug naar home</a></p></div></section>
    <?php
    return;
}
$page_title = $p['meta_title'] ?: $p['title'];
$page_desc  = $p['meta_description'];
?>
<section class="section">
  <div class="container article">
    <a class="back-link" href="<?= h(url('')) ?>">← Terug</a>
    <h1><?= h($p['h1'] ?: $p['title']) ?></h1>
    <div class="rich-text"><?= $p['body'] // trusted admin content ?></div>
  </div>
</section>
