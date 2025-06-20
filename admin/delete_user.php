<?php
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/jwt_utils.php';

function get_bearer_token(): ?string {
    $headers = apache_request_headers();
    if (isset($headers['X-Auth-Token'])) {
        return $headers['X-Auth-Token'];
    }
    return null;
}

header("Content-Type: application/json");

$token = get_bearer_token();
$payload = verify_jwt($token);
if (!$payload || $payload->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id"]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

if ($user_id == $payload->sub) {
    session_start();
    session_destroy();
    echo json_encode(["selfDeleted" => true]);
    exit;
}

echo json_encode(["success" => true]);
