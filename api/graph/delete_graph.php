<?php
session_start();
require __DIR__ . '/../../auth/db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Nu esti autentificat.']);
  exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
  echo json_encode(['error' => 'ID invalid.']);
  exit;
}

$stmt = $pdo->prepare("
  DELETE FROM generated_data
  WHERE id = ? AND user_id = ? AND data_type = 'graph'
");
$stmt->execute([$id, $_SESSION['user_id']]);

if ($stmt->rowCount()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['error' => 'Nu ai permisiunea sa stergi acest graf sau nu exista.']);
}
