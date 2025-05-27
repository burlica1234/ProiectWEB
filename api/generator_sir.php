<?php
header('Content-Type: application/json');

$length = intval($_POST['length'] ?? 0);
$min = intval($_POST['min'] ?? 0);
$max = intval($_POST['max'] ?? 0);
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
