<?php
header('Content-Type: application/json');

$nodes = intval($_POST['nodes'] ?? 0);
$edges = intval($_POST['edges'] ?? 0);
$type = $_POST['type'] ?? 'normal';
$orientation = $_POST['orientation'] ?? 'undirected';
$format = $_POST['format'] ?? 'edges';

if ($nodes <= 0 || $edges < 0) {
    echo json_encode(['error' => 'Date invalide pentru generarea grafului/arborului.']);
    exit;
}

$edgesList = [];

if ($type === 'tree') {
    // generare arbore
    $edges = $nodes - 1;
    $available = range(1, $nodes - 1);
    shuffle($available);
    $connected = [0];
    while (!empty($available)) {
        $from = $connected[array_rand($connected)];
        $to = array_shift($available);
        $edgesList[] = [$from, $to];
        $connected[] = $to;
    }
} else if ($type === 'bipartite') {
    // verificare input muchii
    $part1 = range(0, intdiv($nodes, 2) - 1);
    $part2 = range(intdiv($nodes, 2), $nodes - 1);
    $maxEdges = $orientation === 'undirected' ? count($part1) * count($part2) : 2 * count($part1) * count($part2);
    if ($edges > $maxEdges) {
        echo json_encode(['error' => 'Prea multe muchii pentru un graf bipartit cu acest număr de noduri.']);
        exit;
    }

    // generare graf bipartit
    $allPossible = [];
    foreach ($part1 as $i) {
        foreach ($part2 as $j) {
            $allPossible[] = [$i, $j];
            if ($orientation === 'directed') {
                $allPossible[] = [$j, $i]; // part2 → part1
            }
        }
    }

    shuffle($allPossible);
    $edgesList = array_slice($allPossible, 0, min($edges, count($allPossible)));
} else {
    // verificare input muchii
    $maxEdges = $orientation === 'undirected' ? $nodes * ($nodes - 1) / 2 : $nodes * ($nodes - 1);
    if ($edges > $maxEdges) {
        echo json_encode(['error' => 'Prea multe muchii pentru numărul de noduri.']);
        exit;
    }

    // generare graf normal
    $allPossible = [];
    for ($i = 0; $i < $nodes; $i++) {
        for ($j = 0; $j < $nodes; $j++) {
            if ($i === $j) continue;
            if ($orientation === 'undirected' && $j < $i) continue;
            $allPossible[] = [$i, $j];
        }
    }

    shuffle($allPossible);
    $edgesList = array_slice($allPossible, 0, $edges);
}

$response = [];

if ($type === 'tree') {
    $parents = array_fill(0, $nodes, -1);
    foreach ($edgesList as [$from, $to]) {
        $parents[$to] = $from;
    }
    $response['parents'] = $parents;
}

if ($format === 'adjacency') {
    $matrix = array_fill(0, $nodes, array_fill(0, $nodes, 0));
    foreach ($edgesList as [$from, $to]) {
        $matrix[$from][$to] = 1;
        if ($orientation === 'undirected') {
            $matrix[$to][$from] = 1;
        }
    }
    $response['adjacency'] = $matrix;
} else if ($format === 'edges' || $format === 'parents') {
    $response['edges'] = $edgesList;
}

$response['edges'] = $edgesList;
echo json_encode($response);
