<?php
session_start();
require __DIR__ . '/../../auth/db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Nu ești autentificat.']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$matrix = $data['matrix'] ?? [];

if (!$title || !is_array($matrix) || !is_array($matrix[0])) {
  echo json_encode(['error' => 'Date invalide.']);
  exit;
}

try {
  $stmt = $pdo->prepare("
    INSERT INTO generated_data (user_id, title, data_type, data_json)
    VALUES (:uid, :title, 'matrix', :json)
  ");
  $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':title' => $title,
    ':json' => json_encode($matrix)
  ]);
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['error' => 'Eroare la salvare.']);
}
