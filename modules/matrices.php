<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Matrici</title>
    <link rel="stylesheet" href="../assets/css/matrices.css">
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

<div class="matrix-generator-card">
    <h2> Generator Matrici</h2>

    <form id="matrixForm">
        <label>Linii:</label>
        <input type="number" name="rows" min="1" max="50" required>

        <label>Coloane:</label>
        <input type="number" name="cols" min="1" max="50" required>

        <div id="range-options">
            <label>Valoare minimă:</label>
            <input type="number" name="min" min="-1000000" max="1000000">

            <label>Valoare maximă:</label>
            <input type="number" name="max" min="-1000000" max="1000000">
        </div>

        <label>Tip matrice:</label>
        <select name="mode">
            <option value="random">Aleator</option>
            <option value="map">Hartă (0 = liber, 1 = obstacol)</option>
        </select>

        <button type="submit" class="primary-btn">Generează matricea</button>
        <br><br>

        <div class="button-group">
            <button id="saveBtn" type="button" disabled> Salvează</button>
            <button id="loadBtn" type="button" disabled> Încarcă</button>
        </div>
        <br><br>

        <select id="savedMatrices">
            <option value="">-- Alege o matrice salvată --</option>
        </select>
        <button id="deleteBtn" type="button" disabled> Sterge</button>
    </form>

    <div id="result" class="result-box">Rezultatul va apărea aici.</div>

    <a href="../index.php" class="back-button">⬅ Înapoi la pagina principală</a>
</div>

<script>
    const modeSelect = document.querySelector('select[name="mode"]');
    const rangeOptions = document.getElementById('range-options');
    function toggleRangeInputs() {
        rangeOptions.style.display = modeSelect.value === 'map' ? 'none' : 'block';
    }
    modeSelect.addEventListener('change', toggleRangeInputs);
    window.addEventListener('DOMContentLoaded', toggleRangeInputs);

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }


    let currentMatrix = [];

    document.getElementById('matrixForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('../api/matrix/generate_matrix.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                document.getElementById('result').innerHTML = "Eroare: " + data.error;
                return;
            }

            currentMatrix = data.matrix;
            document.getElementById('saveBtn').disabled = false;

            const maxSize = Math.max(currentMatrix.length, currentMatrix[0].length);
            const scale = maxSize > 30 ? 30 / maxSize : 1;
            const tdSize = Math.floor(2 * scale) + 'em';
            const fontSize = (1 * scale).toFixed(2) + 'em';

            let html = `<div style="overflow:auto; text-align:center;"><table><tr><th></th>` +
                currentMatrix[0].map((_, j) => `<th>${escapeHtml(j)}</th>`).join('') + '</tr>';

            currentMatrix.forEach((row, i) => {
                html += `<tr><th>${i}</th>` + row.map(val =>
                    `<td style="width:${tdSize}; font-size:${fontSize};">${escapeHtml(val)}</td>`
                ).join('') + '</tr>';
            });

            html += '</table></div>';
            document.getElementById('result').innerHTML = html;
        });
    });

    function fetchSaved() {
        fetch('../api/matrix/list_saved_matrices.php')
            .then(r => r.json())
            .then(data => {
                const sel = document.getElementById('savedMatrices');
                sel.innerHTML = '<option value="">-- Alege o matrice salvată --</option>';
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
        const title = prompt('Titlu pentru această matrice:');
        if (!title) return;
        fetch('../api/matrix/save_matrix.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, matrix: currentMatrix })
        })
        .then(r => r.json())
        .then(resp => {
            alert(resp.success ? 'Matrice salvată!' : resp.error);
            if (resp.success) fetchSaved();
        });
    });

    // lista de salvari
    document.getElementById('savedMatrices').addEventListener('change', e => {
        const hasSelection = !!e.target.value;
        document.getElementById('loadBtn').disabled = !hasSelection;
        document.getElementById('deleteBtn').disabled = !hasSelection;
    });

    // incarcare
    document.getElementById('loadBtn').addEventListener('click', () => {
        const id = document.getElementById('savedMatrices').value;
        fetch(`../api/matrix/load_matrix.php?id=${id}`)
            .then(r => r.json())
            .then(data => {
                if (data.error) return alert(data.error);
                currentMatrix = data.matrix;
                const maxSize = Math.max(currentMatrix.length, currentMatrix[0].length);
                const scale = maxSize > 30 ? 30 / maxSize : 1;
                const tdSize = Math.floor(2 * scale) + 'em';
                const fontSize = (1 * scale).toFixed(2) + 'em';

                let html = `<div style="overflow:auto; text-align:center;"><table><tr><th></th>` +
                    currentMatrix[0].map((_, j) => `<th>${escapeHtml(j)}</th>`).join('') + '</tr>';

                currentMatrix.forEach((row, i) => {
                    html += `<tr><th>${i}</th>` + row.map(val =>
                        `<td style="width:${tdSize}; font-size:${fontSize};">${escapeHtml(val)}</td>`
                    ).join('') + '</tr>';
                });

                html += '</table></div>';
                document.getElementById('result').innerHTML = html;
                document.getElementById('saveBtn').disabled = false;
            });
    });

    //stergere
    document.getElementById('deleteBtn').addEventListener('click', () => {
        const id = document.getElementById('savedMatrices').value;
        if (!id) return;

        if (!confirm('Sigur vrei sa stergi aceasta matrice?')) return;

        fetch(`../api/matrix/delete_matrix.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Matrice stearsa cu succes.');
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