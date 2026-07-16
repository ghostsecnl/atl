<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

// Optional Composer autoload for PHPMailer
$__autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($__autoload)) require_once $__autoload;

function render_template(string $tpl, array $data): string {
    $out = $tpl;
    foreach ($data as $k => $v) {
        $out = str_replace('{' . $k . '}', (string)$v, $out);
    }
    return $out;
}

function booking_template_vars(array $b, string $payment_note, array $settings): array {
    $direction = ($b['direction'] ?? '') === 'to_home' ? 'Vanaf de luchthaven' : 'Naar de luchthaven';
    $trip_type = ($b['trip_type'] ?? '') === 'return' ? 'Retour' : 'Enkele reis';
    $vehicle = ($b['vehicle'] ?? '') === 'van' ? 'Taxibus' : 'Personenauto';
    $payment = ($b['payment_method'] ?? '') === 'pin' ? 'Pinnen in de taxi' : 'Contant in de taxi';
    
    return [
        'reference'      => $b['reference'] ?? '',
        'customer_name'  => $b['customer_name'] ?? '',
        'customer_email' => $b['customer_email'] ?? '',
        'customer_phone' => $b['customer_phone'] ?? '',
        'address'        => $b['address'] ?? '',
        'postcode'       => $b['postcode'] ?? '',
        'house_number'   => $b['house_number'] ?? '',
        'region_name'    => $b['region_name'] ?? '',
        'airport_name'   => $b['airport_name'] ?? '',
        'direction'      => $direction,
        'trip_type'      => $trip_type,
        'passengers'     => (string)($b['passengers'] ?? 0),
        'luggage'        => (string)($b['luggage'] ?? 0),
        'luggage1'       => (string)($b['luggage1'] ?? '0'),
        'vehicle'        => $vehicle,
        'pickup_date'    => !empty($b['pickup_date']) ? date('d/m/Y', strtotime($b['pickup_date'])) : '',
        'pickup_time'    => $b['pickup_time'] ?? '',
        'return_date'    => !empty($b['return_date']) ? date('d/m/Y', strtotime($b['return_date'])) : '',
        'return_time'    => $b['return_time'] ?? '',
        'flight_number'  => $b['flight_number'] ?? '',
        'payment_method' => $payment,
        'notes'          => $b['notes'] ?? '',
        'price'          => format_price($b['price'] ?? 0),
        'payment_note'   => $payment_note,
        'company_name'   => $settings['company_name'] ?? '',
        'company_phone'  => $settings['company_phone'] ?? '',
        'company_email'  => $settings['company_email'] ?? '',
        'city'           => $b['city'] ?? '',
    ];
}

function mail_log(string $msg): void {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    @file_put_contents($dir . '/mail.log', '[' . date('d-m-Y H:i:s') . '] ' . $msg . "\n", FILE_APPEND);
}

function send_mail(string $to_email, string $subject, string $html, string $text): bool {
    $s = settings_all();
    $from_name  = $s['from_name']  ?: ($s['company_name'] ?: 'Airport Taxi Limburg');
    $from_email = $s['from_email'] ?: ('noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

    // Prefer PHPMailer if available and SMTP configured
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer') && !empty($s['smtp_host'])) {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = $s['smtp_host'];
            $mail->Port       = (int)($s['smtp_port'] ?: 587);
            if (!empty($s['smtp_username'])) {
                $mail->SMTPAuth = true;
                $mail->Username = $s['smtp_username'];
                $mail->Password = $s['smtp_password'];
            }
            $enc = strtolower($s['smtp_encryption'] ?? 'tls');
            if ($enc === 'ssl') $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            elseif ($enc === 'tls') $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            else $mail->SMTPSecure = false;

            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to_email);
            $mail->addReplyTo($s['company_email'] ?: $from_email, $from_name);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = $text;
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            mail_log('PHPMailer failed to ' . $to_email . ': ' . $e->getMessage());
            // fall through to mail()
        }
    }

    // Fallback: PHP mail()
    $boundary = '=_' . bin2hex(random_bytes(8));
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n";
    $reply = $s['company_email'] ?: $from_email;
    $headers .= 'Reply-To: ' . $reply . "\r\n";
    $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";
    $body  = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . $text . "\r\n\r\n";
    $body .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . $html . "\r\n\r\n";
    $body .= "--$boundary--";
    $enc_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $ok = @mail($to_email, $enc_subject, $body, $headers);
    if (!$ok) mail_log('mail() failed to ' . $to_email);
    return (bool)$ok;
}

function send_booking_emails(array $booking): void {
    $s = settings_all();
    $vars = booking_template_vars($booking, $s['payment_note'] ?? '', $s);

    // Customer
    if (!empty($booking['customer_email'])) {
        $subject = render_template(settings_get('tpl_customer_subject', 'Boekingsbevestiging {reference}'), $vars);
        $html = render_template(settings_get('tpl_customer_html', ''), $vars);
        $text = render_template(settings_get('tpl_customer_text', ''), $vars);
        try { send_mail($booking['customer_email'], $subject, $html, $text); }
        catch (\Throwable $e) { mail_log('Customer mail exception: ' . $e->getMessage()); }
    }

    // Company
    $company_email = $s['company_email'] ?? '';
    if (!empty($company_email)) {
        $subject = render_template(settings_get('tpl_company_subject', 'Nieuwe boeking {reference}'), $vars);
        $html = render_template(settings_get('tpl_company_html', ''), $vars);
        $text = render_template(settings_get('tpl_company_text', ''), $vars);
        try { send_mail($company_email, $subject, $html, $text); }
        catch (\Throwable $e) { mail_log('Company mail exception: ' . $e->getMessage()); }
    }
}
