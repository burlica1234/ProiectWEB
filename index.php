<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌿 Generator Informatică</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- HEADER / HERO -->
    <header class="hero">
        <div class="overlay">
            <h1 class="hero-title">🌿 Generator de Date pentru Informatică</h1>
            <p class="hero-subtitle">Generează rapid șiruri, matrici, grafuri și stringuri pentru testare!</p>
            <div class="hero-actions">
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="welcome">👋 Salut, <?= htmlspecialchars($_SESSION['user']) ?>!</span>
                    <a href="auth/logout.php" class="btn">Delogare</a>
                    <a href="admin/admin_users.php" id="adminLink" class="btn hidden">Admin Panel</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn">Autentificare</a>
                    <a href="auth/register.php" class="btn">Înregistrare</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- MODUL SELECTOR -->
    <main class="modules-section">
        <h2 class="section-title">Alege un modul:</h2>
        <div class="modules-grid">
            <a href="modules/arrays.php" class="module-card">
                <h3>🔢 Șiruri</h3>
                <p>Valori random, crescătoare sau descrescătoare.</p>
            </a>
            <a href="modules/matrices.php" class="module-card">
                <h3>🔲 Matrici</h3>
                <p>Umpleri, hărți, generare aleatorie și personalizată.</p>
            </a>
            <a href="modules/strings.php" class="module-card">
                <h3>🔤 Stringuri</h3>
                <p>Testează algoritmi de manipulare stringuri.</p>
            </a>
            <a href="modules/graphs.php" class="module-card">
                <h3>🕸️ Grafuri / Arbori</h3>
                <p>Conexe, orientate, bipartite, reprezentare SVG.</p>
            </a>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <p>&copy; <?= date("Y") ?> Generator Informatica. Creat cu ❤️.</p>
    </footer>

    <?php if (isset($_SESSION['token'])): ?>
        <script>
            const token = '<?= $_SESSION['token'] ?>';
            try {
                const payload = JSON.parse(atob(token.split('.')[1]));
                if (payload.role === 'admin') {
                    document.getElementById('adminLink').classList.remove('hidden');
                }
            } catch (e) {
                console.warn("Token JWT invalid.");
            }
        </script>
    <?php endif; ?>
</body>
</html>
