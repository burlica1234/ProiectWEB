<?php session_start(); ?>
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
            <form id="registerForm">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Parolă:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Creează cont</button>
            </form>

            <p class="error-msg" id="errorMsg" style="display:none;"></p>

            <a class="secondary-link" href="login.php">Ai deja cont? Autentifică-te</a>
        </div>
    </div>

    <script>
    document.getElementById("registerForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        const errorMsg = document.getElementById("errorMsg");
        errorMsg.style.display = "none";

        try {
            const res = await fetch('../api/auth/register_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, email, password })
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
