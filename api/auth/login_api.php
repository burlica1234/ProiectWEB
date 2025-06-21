<?php
require_once '../../auth/db.php';
require_once '../../auth/jwt_utils.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Completează toate câmpurile."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(["error" => "Email sau parolă incorecte."]);
        exit;
    }

    $token = generate_jwt([
        'sub' => $user['id'],
        'username' => $user['username'],
        'email' => $email,
        'role' => $user['role']
    ]);

    session_start();
    $_SESSION['user'] = $user['username'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['token'] = $token;

    echo json_encode(["success" => true, "redirect" => "../index.php"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Eroare server: " . $e->getMessage()]);
}
