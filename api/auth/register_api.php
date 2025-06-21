<?php
require_once '../../auth/db.php';
require_once '../../auth/jwt_utils.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Toate câmpurile sunt obligatorii."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);

    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(["error" => "Emailul sau username-ul există deja."]);
        exit;
    }

    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $role = ($count == 0) ? 'admin' : 'user';

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hash, $role]);

    $userId = $pdo->lastInsertId();

    $token = generate_jwt([
        'sub' => $userId,
        'username' => $username,
        'email' => $email,
        'role' => $role
    ]);

    session_start();
    $_SESSION['user'] = $username;
    $_SESSION['user_id'] = $userId;
    $_SESSION['role'] = $role;
    $_SESSION['token'] = $token;

    echo json_encode(["success" => true, "redirect" => "../index.php"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Eroare server: " . $e->getMessage()]);
}
