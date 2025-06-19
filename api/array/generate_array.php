<?php
header('Content-Type: application/json');

$length = intval($_POST['length'] ?? 0);
$min = isset($_POST['min']) && $_POST['min'] !== '' ? intval($_POST['min']) : -1000000;
$max = isset($_POST['max']) && $_POST['max'] !== '' ? intval($_POST['max']) : 1000000;
$order = $_POST['order'] ?? 'none';

if ($length <= 0 || $min > $max) {
    echo json_encode(['error' => 'Date invalide']);
    exit;
}

$array = [];
for ($i = 0; $i < $length; $i++) {
    $array[] = rand($min, $max);
}

if ($order === 'asc') {
    sort($array);
} elseif ($order === 'desc') {
    rsort($array);
}

echo json_encode(['array' => $array]);
