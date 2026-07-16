<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/pricing.php';

$raw = file_get_contents('php://input');
$data = [];
if ($raw) $data = json_decode($raw, true) ?: [];
if (empty($data)) $data = $_POST;

$result = compute_quote($data);
echo json_encode($result);
