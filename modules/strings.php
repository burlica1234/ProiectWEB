<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Șiruri de Caractere</title>
    <link rel="stylesheet" href="../assets/css/strings.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <h1 class="logo"> Generator Informatica</h1>
        <div class="nav-links">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="welcome">Bine ai venit, <?= htmlspecialchars($_SESSION['user']) ?>!</span>
                <a href="../auth/logout.php">Delogare</a>
            <?php else: ?>
                <a href="../auth/login.php">Autentificare</a>
                <a href="../auth/register.php">Înregistrare</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="string-generator-card">
    <h2> Generator Șiruri de Caractere</h2>

    <form id="textForm">
        <label>Lungime:</label>
        <input type="number" name="length" min="1" max="1000" required>

        <label>Tip caractere:</label>
        <select name="charset">
            <option value="letters">Litere (a-z, A-Z)</option>
            <option value="letters_digits">Litere și cifre</option>
            <option value="digits">Doar cifre</option>
            <option value="all">Litere, cifre și simboluri</option>
        </select>

        <button type="submit" class="primary-btn">Generează</button>

        <div class="button-group">
            <button id="saveBtn" type="button" disabled> Salvează</button>
            <button id="loadBtn" type="button" disabled> Încarcă</button>
        </div>
        <br><br>

        <select id="savedStrings">
            <option value="">-- Alege un șir salvat --</option>
        </select>
        <button id="deleteBtn" type="button" disabled> Sterge</button>
    </form>

    <div id="result" class="result-box">Rezultatul va apărea aici.</div>

    <a href="../index.php" class="back-button">⬅ Înapoi la pagina principală</a>
</div>

<script>
    let currentString = '';

    // generare
    document.getElementById('textForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('../api/string/generate_string.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                const result = document.getElementById('result');
                result.textContent = "Eroare: " + data.error;
                return;
            }


            currentString = data.text;

            const result = document.getElementById('result');
            result.innerHTML = "<strong>Șir generat:</strong><br><code></code>";
            result.querySelector('code').textContent = data.text;

            document.getElementById('saveBtn').disabled = false;
        });
    });

    function fetchSaved() {
        fetch('../api/string/list_saved_strings.php')
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('savedStrings');
            sel.innerHTML = '<option value="">-- Alege un șir salvat --</option>';
            data.forEach(item => {
                const o = document.createElement('option');
                o.value = item.id;
                o.textContent = item.title;
                sel.append(o);
            });
            document.getElementById('loadBtn').disabled = true;
        });
    }

    // salvare
    document.getElementById('saveBtn').addEventListener('click', () => {
        const title = prompt('Titlu pentru acest șir:');
        if (!title) return;
        fetch('../api/string/save_string.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ title, text: currentString })
        }).then(r => r.json()).then(resp => {
            alert(resp.success ? 'Salvat!' : resp.error);
            if (resp.success) fetchSaved();
        });
    });

    // lista de salvari
    document.getElementById('savedStrings').addEventListener('change', e => {
        const hasSelection = !!e.target.value;
        document.getElementById('loadBtn').disabled = !hasSelection;
        document.getElementById('deleteBtn').disabled = !hasSelection;
    });

    // incarcare
    document.getElementById('loadBtn').addEventListener('click', () => {
        const id = document.getElementById('savedStrings').value;
        fetch(`../api/string/load_string.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) return alert(data.error);
            currentString = data.text;

            const result = document.getElementById('result');
            result.innerHTML = "<strong>Șir încărcat:</strong><br><code></code>";
            result.querySelector('code').textContent = currentString;

            document.getElementById('saveBtn').disabled = false;
        });
    });

    // stergere
    document.getElementById('deleteBtn').addEventListener('click', () => {
        const id = document.getElementById('savedStrings').value;
        if (!id) return;

        if (!confirm('Sigur vrei sa stergi acest sir salvat?')) return;

        fetch(`../api/string/delete_string.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Sir sters cu succes.');
                fetchSaved();
            } else {
                alert(data.error || 'Eroare la stergere.');
            }
        });
    });

    fetchSaved();
</script>
</body>
</html>
