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
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $msg = "Email sau parolă incorecte.";
            } else {
                $token = generate_jwt([
                    'sub' => $user['id'],
                    'username' => $user['username'],
                    'email' => $email
                ]);

                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
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
    <main class="container">
        <h2 class="section-title">Autentificare</h2>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Parolă:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <?php if (!empty($msg)): ?>
            <p style="color: red; text-align:center"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <a class="back-button" href="../auth/register.php">Nu ai cont? Înregistrează-te</a>
    </main>
</body>
</html>
