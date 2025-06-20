<?php
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/jwt_utils.php';

function get_bearer_token(): ?string {
    $headers = apache_request_headers();
    if (isset($headers['X-Auth-Token'])) {
        return trim($headers['X-Auth-Token']);
    }
    return null;
}

header('Content-Type: application/json');

$token = get_bearer_token();
$payload = verify_jwt($token);
if (!$payload || $payload->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;
$new_password = $data['new_password'] ?? null;

if (!$user_id || !$new_password) {
    http_response_code(400);
    echo json_encode(["error" => "Lipsesc datele necesare."]);
    exit;
}

$hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
$stmt->execute([$hash, $user_id]);

echo json_encode(["success" => true]);
