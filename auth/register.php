<?php
session_start();
require_once 'db.php';
require_once 'jwt_utils.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$email || !$password) {
        $msg = "Toate câmpurile sunt obligatorii.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);

            if ($stmt->fetch()) {
                $msg = "Emailul sau username-ul există deja.";
            } else {
                $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                $role = ($count == 0) ? 'admin' : 'user';

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hash, $role]);

                // obtine user nou inregistrat
                $userId = $pdo->lastInsertId();

                $token = generate_jwt([
                    'sub' => $userId,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role
                ]);

                //seteaza in sesiune
                $_SESSION['user'] = $username;
                $_SESSION['user_id'] = $userId;
                $_SESSION['role'] = $role;
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
    <title>Înregistrare</title>
    <link rel="stylesheet" href="../assets/css/registerpg.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h2 class="title">📝 Înregistrare</h2>
            <form method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="password">Parolă:</label>
                <input type="password" name="password" required>

                <button type="submit">Creează cont</button>
            </form>

            <?php if (!empty($msg)): ?>
                <p class="error-msg"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <a class="secondary-link" href="login.php">Ai deja cont? Autentifică-te</a>
        </div>
    </div>
</body>
</html>

