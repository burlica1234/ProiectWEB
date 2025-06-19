<?php
session_start();
require __DIR__ . '/../../auth/db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT id, title
  FROM generated_data
  WHERE user_id = ? AND data_type = 'string'
  ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
