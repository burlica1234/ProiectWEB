<?php
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/jwt_utils.php';

header('Content-Type: application/json');

function get_bearer_token(): ?string {
    $headers = apache_request_headers();
    return $headers['X-Auth-Token'] ?? null;
}

$token = get_bearer_token();
$payload = verify_jwt($token);

if (!$payload || $payload->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Acces interzis"]);
    exit;
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID utilizator lipsă"]);
    exit;
}

try {
    
    $stmt1 = $pdo->prepare("SELECT * FROM arrays WHERE user_id = ?");
    $stmt1->execute([$user_id]);
    $arrays = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM matrices WHERE user_id = ?");
    $stmt2->execute([$user_id]);
    $matrices = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $stmt3 = $pdo->prepare("SELECT * FROM graphs WHERE user_id = ?");
    $stmt3->execute([$user_id]);
    $graphs = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "arrays" => $arrays,
        "matrices" => $matrices,
        "graphs" => $graphs
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Eroare server: " . $e->getMessage()]);
}
