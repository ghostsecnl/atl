<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $editable = ['company_name','company_email','company_phone','from_name','from_email',
                 'car_max_passengers','car_max_luggage','collect_luggage','return_multiplier',
                 'price_per_km_car','price_per_km_van','one_off_fee','price_round_step',
                 'booking_ref_prefix','payment_note',
                 'smtp_host','smtp_port','smtp_username','smtp_password','smtp_encryption'];
    foreach ($editable as $k) {
        if (array_key_exists($k, $_POST)) settings_set($k, (string)$_POST[$k]);
    }
    settings_set('collect_luggage', !empty($_POST['collect_luggage']) ? '1' : '0');
    // password change
    if (!empty($_POST['new_password']) && $_POST['new_password'] === ($_POST['new_password_confirm'] ?? '')) {
        $id = (int)($_SESSION['admin_id'] ?? 0);
        if ($id) admin_change_password($id, (string)$_POST['new_password']);
    }
    $_SESSION['flash'] = ['t'=>'ok','m'=>'Instellingen opgeslagen.'];
    header('Location: ' . url('admin.php?p=settings')); exit;
}
$s = settings_all();
$fields = [
    'Bedrijf' => [
        ['company_name','Bedrijfsnaam'],
        ['company_email','Bedrijf e-mail (ontvangt boekingen)'],
        ['company_phone','Bedrijf telefoon'],
        ['booking_ref_prefix','Boeking referentie prefix'],
        ['payment_note','Betaalnotitie (in mails)'],
    ],
    'Prijzen' => [
        ['price_per_km_car','Prijs per km (personenauto) €'],
        ['price_per_km_van','Prijs per km (taxibus) €'],
        ['one_off_fee','Vaste starttarief €'],
        ['return_multiplier','Retourfactor (2 = 2× enkele reis)'],
        ['price_round_step','Afrondstap € (0 = geen)'],
        ['car_max_passengers','Max personen in auto'],
        ['car_max_luggage','Max bagage in auto'],
    ],
    'E-mail (SMTP via PHPMailer)' => [
        ['from_name','Afzender naam'],
        ['from_email','Afzender e-mail'],
        ['smtp_host','SMTP host'],
        ['smtp_port','SMTP poort'],
        ['smtp_username','SMTP gebruikersnaam'],
        ['smtp_password','SMTP wachtwoord'],
        ['smtp_encryption','SMTP encryptie (tls, ssl of leeg)'],
    ],
];
?>
<div class="admin-topbar"><h1>Instellingen</h1></div>
<form method="post" class="card"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<?php foreach ($fields as $group => $list): ?>
  <h2 style="margin-top:22px;font-size:16px;color:#0b1730;border-bottom:1px solid #e2e8f0;padding-bottom:6px"><?= h($group) ?></h2>
  <?php foreach ($list as [$k,$lbl]):
      $isPw = str_contains($k, 'password'); ?>
    <div class="field"><label><?= h($lbl) ?></label>
      <input name="<?= h($k) ?>" type="<?= $isPw?'password':'text' ?>" value="<?= h((string)($s[$k] ?? '')) ?>">
    </div>
  <?php endforeach; ?>
<?php endforeach; ?>

  <h2 style="margin-top:22px;font-size:16px;color:#0b1730;border-bottom:1px solid #e2e8f0;padding-bottom:6px">Overig</h2>
  <div class="field"><label style="flex-direction:row;align-items:center;gap:8px;font-weight:400">
    <input type="checkbox" name="collect_luggage" <?= ($s['collect_luggage']??'1')==='1'?'checked':'' ?>> Bagage-aantal vragen op boekingsformulier
  </label></div>

  <h2 style="margin-top:22px;font-size:16px;color:#0b1730;border-bottom:1px solid #e2e8f0;padding-bottom:6px">Wachtwoord wijzigen</h2>
  <div class="field"><label>Nieuw wachtwoord</label><input type="password" name="new_password"></div>
  <div class="field"><label>Bevestig</label><input type="password" name="new_password_confirm"></div>

  <button class="btn btn-primary" style="margin-top:14px">Opslaan</button>
</form>
