<?php
session_start();
require_once 'db.php';
require_once 'jwt_utils.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $msg = "Completează toate câmpurile.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $msg = "Email sau parolă incorecte.";
            } else {
                $token = generate_jwt([
                    'sub' => $user['id'],
                    'username' => $user['username'],
                    'email' => $email,
                    'role' => $user['role']
                ]);

                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['token'] = $token;

                header("Location: ../index.php");
                exit;
            }
        } catch (Exception $e) {
            $msg = "Eroare server: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 class="title">🔒 Autentificare</h2>
            <form method="POST">
                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="password">Parolă:</label>
                <input type="password" name="password" required>

                <button type="submit">Autentifică-te</button>
            </form>

            <?php if (!empty($msg)): ?>
                <p class="error-msg"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <a class="secondary-link" href="register.php">Nu ai cont? Creează unul</a>
        </div>
    </div>
</body>
</html>
