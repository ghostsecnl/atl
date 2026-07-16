<!doctype html>
<html lang="nl"><head><meta charset="utf-8"><title>Admin login</title>
<link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>"></head>
<body class="admin-body">
<div class="login-shell">
  <form class="login-card" method="post" action="<?= h(url('admin.php?p=login')) ?>">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <h1>Admin login</h1>
    <?php if (!empty($err)): ?><div class="msg msg-err"><?= h($err) ?></div><?php endif; ?>
    <div class="field"><label>Gebruikersnaam</label><input type="text" name="username" required autofocus></div>
    <div class="field"><label>Wachtwoord</label><input type="password" name="password" required></div>
    <button class="btn btn-primary btn-block" type="submit">Inloggen</button>
    <p style="margin-top:16px;font-size:12px;color:#64748b;text-align:center">Standaard: admin / admin (wijzig via Instellingen)</p>
  </form>
</div>
</body></html>
