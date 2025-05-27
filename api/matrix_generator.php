<?php
header('Content-Type: application/json');

$rows = intval($_POST['rows'] ?? 0);
$cols = intval($_POST['cols'] ?? 0);
$min = intval($_POST['min'] ?? 0);
$max = intval($_POST['max'] ?? 0);

if ($rows <= 0 || $cols <= 0 || $min > $max) {
    echo json_encode(['error' => 'Date invalide pentru generarea matricei.']);
    exit;
}

$matrix = [];
for ($i = 0; $i < $rows; $i++) {
    $row = [];
    for ($j = 0; $j < $cols; $j++) {
        $row[] = rand($min, $max);
    }
    $matrix[] = $row;
}

echo json_encode(['matrix' => $matrix]);
