<?php
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/jwt_utils.php';


ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");


function get_bearer_token(): ?string {
    return $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
}

$token = get_bearer_token();

if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "Token missing (X-Auth-Token)"]);
    exit;
}

$payload = verify_jwt($token);


if (!is_object($payload) || !isset($payload->role) || $payload->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied"]);
    exit;
}


try {
    $stmt = $pdo->query("SELECT id, username, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Eroare la interogare: " . $e->getMessage()]);
}
