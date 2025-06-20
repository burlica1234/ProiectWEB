<?php
session_start();
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/jwt_utils.php';

if (!isset($_SESSION['token'])) {
    header("Location: ../auth/login.php");
    exit;
}

$payload = verify_jwt($_SESSION['token']);
if (!$payload || $payload->role !== 'admin') {
    http_response_code(403);
    echo "Acces interzis.";
    exit;
}

$user_id = $_GET['id'] ?? null;
if (!$user_id || !is_numeric($user_id)) {
    echo "ID utilizator lipsă sau invalid.";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT title, data_type, data_json, created_at FROM generated_data WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p class='error'>Eroare la interogare: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Date Generate de Utilizator</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <a href="admin_users.php" class="back-btn">⬅ Înapoi la gestionare utilizatori</a>
        <h2 class="admin-title">📄 Date Generate de Utilizator</h2>

        <?php if (empty($results)): ?>
            <p class="info-message">Acest utilizator nu a generat date.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titlu</th>
                        <th>Tip</th>
                        <th>Generat la</th>
                        <th>Conținut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['data_type']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <div class="data-box">
                                    <?php
                                        $decoded = json_decode($row['data_json'], true);
                                        if (is_array($decoded)) {
                                            if (is_array($decoded[0])) {
                                                echo '<table class="mini-table">';
                                                foreach ($decoded as $inner) {
                                                    echo '<tr>';
                                                    foreach ($inner as $val) {
                                                        echo '<td>' . htmlspecialchars($val) . '</td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                                echo '</table>';
                                            } else {
                                                echo implode(', ', array_map('htmlspecialchars', $decoded));
                                            }
                                        } else {
                                            echo htmlspecialchars($row['data_json']);
                                        }
                                    ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
