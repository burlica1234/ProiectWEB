<?php
session_start();
require __DIR__ . '/../../auth/db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Nu ești autentificat.']);
  exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
  SELECT data_json
  FROM generated_data
  WHERE id = ? AND user_id = ? AND data_type = 'string'
");
$stmt->execute([$id, $_SESSION['user_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  echo json_encode(['error' => 'Șirul nu există.']);
} else {
  $json = json_decode($row['data_json'], true);
  echo json_encode(['text' => $json['text']]);
}
