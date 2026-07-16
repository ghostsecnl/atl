<?php
// lookup.php
header('Content-Type: application/json');

$postcode = $_GET['postcode'] ?? '';
$number = $_GET['number'] ?? '';
$apiKey = '9ccbc7a5-a102-4028-8066-194a26ddce5e'; // Plaats hier je echte key

$url = "https://api.postcode.tech/v1/postcode?postcode=$postcode&number=$number";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>