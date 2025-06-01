<?php
header('Content-Type: application/json');

$nodes = intval($_POST['nodes'] ?? 0);
$edges = intval($_POST['edges'] ?? 0);
$type = $_POST['type'] ?? 'undirected';

if ($nodes <= 0 || $edges < 0 || ($type === 'tree' && $edges !== $nodes - 1)) {
    echo json_encode(['error' => 'Date invalide pentru generarea grafului/arborului.']);
    exit;
}

$maxEdges = $type === 'undirected' ? $nodes * ($nodes - 1) / 2 : $nodes * ($nodes - 1);
if ($edges > $maxEdges) {
    echo json_encode(['error' => 'Prea multe muchii pentru numărul de noduri.']);
    exit;
}

$allPossible = [];
for ($i = 0; $i < $nodes; $i++) {
    for ($j = 0; $j < $nodes; $j++) {
        if ($i === $j) continue;
        if ($type === 'undirected' && $j < $i) continue;
        $allPossible[] = [$i, $j];
    }
}

shuffle($allPossible);
$edgesList = [];

if ($type === 'tree') {
    // Algoritm simplu pentru arbore: DFS-like
    $available = range(0, $nodes - 1);
    shuffle($available);
    $connected = [array_shift($available)];

    while (!empty($available)) {
        $from = $connected[array_rand($connected)];
        $to = array_shift($available);
        $edgesList[] = [$from, $to];
        $connected[] = $to;
    }
} else {
    $edgesList = array_slice($allPossible, 0, $edges);
}

echo json_encode(['edges' => $edgesList]);
