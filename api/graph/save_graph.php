<?php
session_start();
require __DIR__ . '/../../auth/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$graph = $data['graph'] ?? [];

$type = $data['meta']['type'] ?? 'normal';
$orientation = $data['meta']['orientation'] ?? 'undirected';
$format = $data['meta']['format'] ?? 'edges';

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$graph = $data['graph'] ?? [];

if (!$title || !isset($graph['edges'])) {
  echo json_encode(['error' => 'Date invalide.']);
  exit;
}

// Adaugam tipul si formatul in structura JSON salvata
$graph['__meta__'] = [
  'type' => $type,
  'orientation' => $orientation,
  'format' => $format
];

try {
  $stmt = $pdo->prepare("
    INSERT INTO generated_data (user_id, title, data_type, data_json)
    VALUES (:uid, :title, 'graph', :json)
  ");
  $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':title' => $title,
    ':json' => json_encode($graph)
  ]);
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['error' => 'Eroare la salvare.']);
}

