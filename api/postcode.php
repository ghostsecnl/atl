<?php
// 1. Laad config
require_once __DIR__.'/../config/postcode.php';

// 2. Probeer de API aanroep
$postcode = '6131AA';
$number = '1';
$url = "https://api.postcode.tech/v1/postcode?postcode=" . urlencode($postcode) . "&number=" . urlencode($number);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . POSTCODETECH_API_KEY,
    "Accept: application/json"
]);

$response = curl_exec($ch);

if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $response;
}
curl_close($ch);
?>