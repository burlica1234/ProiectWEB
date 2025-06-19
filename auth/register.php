<?php
session_start();
require_once 'db.php'; // cale corecta: fisierul e în acelasi folder
require_once 'jwt_utils.php';

$msg = "";

// Verifica daca formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$email || !$password) {
        $msg = "Toate câmpurile sunt obligatorii.";
    } else {
        try {
            // Verifica daca user/email exista deja
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);

            if ($stmt->fetch()) {
                $msg = "Emailul sau username-ul există deja.";
            } else {
                // Creeaza utilizator
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hash]);

                // Genereaza JWT
                $userId = $pdo->lastInsertId();
                $token = generate_jwt([
                    'sub' => $userId,
                    'username' => $username,
                    'email' => $email
                ]);

                // Stocheaza în sesiune
                $_SESSION['user'] = $username;
                $_SESSION['token'] = $token;

                // Redirectioneaza catre index
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
    <main class="container">
        <h2 class="section-title">Înregistrare</h2>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <label for="password">Parolă:</label>
            <input type="password" name="password" required>
            <button type="submit">Înregistrează-te</button>
        </form>
        <?php if (!empty($msg)): ?>
            <p style="color: red; text-align: center"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>
        <a class="back-button" href="login.php">Ai deja cont? Login</a>
    </main>
</body>
</html>
