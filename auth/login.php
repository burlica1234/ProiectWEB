<?php session_start(); ?>
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
            <h2 class="title"> Autentificare</h2>
            <form id="loginForm">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Parolă:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Autentifică-te</button>
            </form>

            <p class="error-msg" id="errorMsg" style="display:none;"></p>

            <a class="secondary-link" href="register.php">Nu ai cont? Creează unul</a>
        </div>
    </div>

    <script>
    document.getElementById("loginForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        const errorMsg = document.getElementById("errorMsg");
        errorMsg.style.display = "none";

        try {
            const res = await fetch('../api/auth/login_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();

            if (res.ok) {
                window.location.href = data.redirect;
            } else {
                errorMsg.textContent = data.error || "Eroare necunoscută.";
                errorMsg.style.display = "block";
            }
        } catch (err) {
            errorMsg.textContent = "Eroare la conectare cu serverul.";
            errorMsg.style.display = "block";
        }
    });
    </script>
</body>
</html>
