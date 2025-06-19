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
$text  = $data['text'] ?? '';

if (!$title || !is_string($text)) {
  echo json_encode(['error' => 'Date invalide.']);
  exit;
}

try {
  $stmt = $pdo->prepare("
    INSERT INTO generated_data (user_id, title, data_type, data_json)
    VALUES (:uid, :title, 'string', :json)
  ");
  $stmt->execute([
    ':uid'   => $_SESSION['user_id'],
    ':title' => $title,
    ':json'  => json_encode(['text' => $text])
  ]);
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['error' => 'Salvare eșuată.']);
}
