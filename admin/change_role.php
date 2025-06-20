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
$role = $data['role'] ?? null;

if (!$user_id || !in_array($role, ['user', 'admin'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
}


$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$role, $user_id]);

// daca utilizatorul isi schimba propriul rol, il delogam
if ($user_id == $payload->sub) {
    session_start();
    session_destroy();
    echo json_encode(["selfRoleChange" => true]);
    exit;
}

echo json_encode(["success" => true]);
