<?php
// auth/login.php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

// Preia datele trimise prin POST (JSON)
$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email și parolă sunt necesare.']);
    exit;
}

try {
    // Caută utilizatorul după email
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifică dacă utilizatorul există și parola e corectă
    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Email sau parolă incorecte.']);
        exit;
    }

    // Creează token JWT
    $token = generate_jwt([
        'sub' => $user['id'],
        'username' => $user['username'],
        'email' => $email
    ]);

    echo json_encode([
        'message' => 'Autentificare reușită.',
        'token' => $token
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Eroare server: ' . $e->getMessage()]);
}
