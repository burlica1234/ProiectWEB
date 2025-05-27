<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplicație pentru generarea de date pentru informatică.">
    <title>Generator de Date</title>
    <link rel="stylesheet" href="mainpag.css">
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <h1 class="logo">🧮 Generator Informatica</h1>
        <div class="nav-links">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="welcome">Bine ai venit, <?= htmlspecialchars($_SESSION['user'] ?? 'utilizator') ?>!</span>
            <?php else: ?>
                <a href="login.php">Autentificare</a>
                <a href="register.php">Înregistrare</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container">
    <h2 class="section-title">Ce tip de date vrei să generezi?</h2>
    <div class="card-container">
        <a class="card-box" href="modules/arrays.php">
            <h3>🔢 Șiruri de numere</h3>
            <p>Generează șiruri cu valori random, sortate sau personalizate.</p>
        </a>
        <a class="card-box" href="modules/matrices.php">
            <h3>🔲 Matrici</h3>
            <p>Matrici pentru testare: umplere, parcurgere, hartă etc.</p>
        </a>
        <a class="card-box" href="modules/strings.php">
            <h3>🔤 Șiruri de caractere</h3>
            <p>Testează algoritmi pe stringuri generate automat.</p>
        </a>
        <a class="card-box" href="modules/graphs.php">
            <h3>🕸️ Grafuri / Arbori</h3>
            <p>Grafuri conexe, bipartite, orientate sau neorientate.</p>
        </a>
    </div>
</main>

</body>
</html>
