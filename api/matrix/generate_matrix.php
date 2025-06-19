<?php
header('Content-Type: application/json');

$rows = intval($_POST['rows'] ?? 0);
$cols = intval($_POST['cols'] ?? 0);
$min = isset($_POST['min']) && $_POST['min'] !== '' ? intval($_POST['min']) : -1000000;
$max = isset($_POST['max']) && $_POST['max'] !== '' ? intval($_POST['max']) : 1000000;
$mode = $_POST['mode'] ?? 'random';

if ($rows <= 0 || $cols <= 0 || $min > $max) {
    echo json_encode(['error' => 'Date invalide pentru generarea matricei.']);
    exit;
}

$matrix = [];
for ($i = 0; $i < $rows; $i++) {
    $row = [];
    for ($j = 0; $j < $cols; $j++) {
        if ($mode === 'map') {
            // 0 = liber, 1 = obstacol, 30% șanse să fie obstacol
            $row[] = rand(1, 100) <= 30 ? 1 : 0;
        } else {
            $row[] = rand($min, $max);
        }
    }
    $matrix[] = $row;
}

echo json_encode(['matrix' => $matrix]);
