<?php
// Editable email templates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $keys = ['customer_subject','customer_html','customer_text','company_subject','company_html','company_text'];
    foreach ($keys as $k) {
        if (isset($_POST['tpl_' . $k])) settings_set('tpl_' . $k, (string)$_POST['tpl_' . $k]);
    }
    $_SESSION['flash'] = ['t'=>'ok','m'=>'E-mailsjablonen opgeslagen.'];
    header('Location: ' . url('admin.php?p=email_templates')); exit;
}
$s = settings_all();
$placeholders = '{reference} {customer_name} {customer_email} {customer_phone} {address} {postcode} {house_number} {region_name} {airport_name} {direction} {trip_type} {passengers} {luggage} {vehicle} {pickup_date} {pickup_time} {return_date} {return_time} {flight_number} {payment_method} {notes} {price} {payment_note} {company_name} {company_phone} {company_email}';
?>
<div class="admin-topbar"><h1>E-mailsjablonen</h1></div>
<div class="card"><p style="margin:0;color:#556274;font-size:14px">Beschikbare placeholders (automatisch vervangen):<br><code style="display:block;background:#f1f5f9;padding:10px;border-radius:6px;margin-top:8px;font-size:12px;line-height:1.8"><?= h($placeholders) ?></code></p></div>

<form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
  <div class="card">
    <h2>Klantbevestiging</h2>
    <div class="field"><label>Onderwerp</label><input name="tpl_customer_subject" value="<?= h((string)$s['tpl_customer_subject']) ?>"></div>
    <div class="field"><label>HTML body</label><textarea name="tpl_customer_html" rows="18"><?= h((string)$s['tpl_customer_html']) ?></textarea></div>
    <div class="field"><label>Platte tekst body</label><textarea name="tpl_customer_text" rows="10"><?= h((string)$s['tpl_customer_text']) ?></textarea></div>
  </div>
  <div class="card">
    <h2>Bedrijfsnotificatie</h2>
    <div class="field"><label>Onderwerp</label><input name="tpl_company_subject" value="<?= h((string)$s['tpl_company_subject']) ?>"></div>
    <div class="field"><label>HTML body</label><textarea name="tpl_company_html" rows="18"><?= h((string)$s['tpl_company_html']) ?></textarea></div>
    <div class="field"><label>Platte tekst body</label><textarea name="tpl_company_text" rows="10"><?= h((string)$s['tpl_company_text']) ?></textarea></div>
  </div>
  <button class="btn btn-primary">Opslaan</button>
</form>
