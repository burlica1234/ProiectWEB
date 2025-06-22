<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Șiruri</title>
    <link rel="stylesheet" href="../assets/css/arrays.css">
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

    <div class="array-generator-card">
        <h2> Generator Șiruri de Numere</h2>

        <form id="sirForm">
            <label for="length">Lungime:</label>
            <input type="number" name="length" min="1" max="1000" required>

            <label>Valoare minimă:</label>
            <input type="number" name="min" min="-1000000" max="1000000">

            <label>Valoare maximă:</label>
            <input type="number" name="max" min="-1000000" max="1000000">

            <label>Sortare:</label>
            <select name="order">
                <option value="none">Fără</option>
                <option value="asc">Crescător</option>
                <option value="desc">Descrescător</option>
            </select>

            <button type="submit" class="primary-btn">Generează</button>

            <div class="button-group">
                <button id="saveBtn" type="button" disabled> Salvează</button>
                <button id="loadBtn" type="button" disabled> Încarcă</button>
            </div>
            <br><br>

            <select id="savedLists">
                <option value="">-- Alege un șir salvat --</option>
            </select>
            <button id="deleteBtn" type="button" disabled> Sterge</button>
        </form>

        <div id="result" class="result-box">Rezultatul va apărea aici.</div>

        <a href="../index.php" class="back-button">⬅ Înapoi la pagina principală</a>
    </div>

    <script>
        let currentArray = [];

        // generare
        document.getElementById('sirForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../api/array/generate_array.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.array) {
                    currentArray = data.array;

                    const result = document.getElementById('result');
                    result.innerHTML = "<strong>Șir generat:</strong><br><code></code>";
                    result.querySelector('code').textContent = currentArray.join(', ');

                    document.getElementById('saveBtn').disabled = false;
                } else {
                    document.getElementById('result').textContent = "Eroare la generare.";
                }
            });
        });

        function fetchSaved() {
            fetch('../api/array/list_saved_arrays.php')
            .then(r => r.json())
            .then(data => {
                const sel = document.getElementById('savedLists');
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
            fetch('../api/array/save_array.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, array: currentArray })
            })
            .then(r => r.json())
            .then(resp => {
                alert(resp.success ? 'Salvat!' : resp.error);
                if (resp.success) fetchSaved();
            });
        });

        // lista de salvari
        document.getElementById('savedLists').addEventListener('change', e => {
            const hasSelection = !!e.target.value;
            document.getElementById('loadBtn').disabled = !hasSelection;
            document.getElementById('deleteBtn').disabled = !hasSelection;
        });

        // incarcare
        document.getElementById('loadBtn').addEventListener('click', () => {
            const id = document.getElementById('savedLists').value;
            fetch(`../api/array/load_array.php?id=${id}`)
            .then(r => r.json())
            .then(data => {
                if (data.error) return alert(data.error);
                currentArray = data.array;

                const result = document.getElementById('result');
                result.innerHTML = "<strong>Șir încărcat:</strong><br><code></code>";
                result.querySelector('code').textContent = currentArray.join(', ');

                document.getElementById('saveBtn').disabled = false;
            });
        });

        // stergere
        document.getElementById('deleteBtn').addEventListener('click', () => {
            const id = document.getElementById('savedLists').value;
            if (!id) return;

            if (!confirm('Sigur vrei sa stergi acest sir?')) return;

            fetch(`../api/array/delete_array.php?id=${id}`, {
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
