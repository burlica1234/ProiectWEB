<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Matrici</title>
    <link rel="stylesheet" href="../assets/css/matrici.css">
    
</head>
<body>
    <div class="container">
        <h2 class="section-title">🔲 Generator Matrici</h2>

        <form id="matrixForm">
            <label>Linii:</label>
            <input type="number" name="rows" min="1" max="100" required><br><br>

            <label>Coloane:</label>
            <input type="number" name="cols" min="1" max="100" required><br><br>

            <label>Valoare minimă:</label>
            <input type="number" name="min" required><br><br>

            <label>Valoare maximă:</label>
            <input type="number" name="max" required><br><br>

            <button type="submit">Generează matricea</button>
        </form>
        <a href="../index.php" class="back-button">← Înapoi la pagina principală</a>

        <div id="result" style="margin-top:20px;"></div>
    </div>

    <script>
        document.getElementById('matrixForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../api/matrix_generator.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('result').innerHTML = "Eroare: " + data.error;
                    return;
                }

                let html = "<strong>Matrice generată:</strong><br><table border='1' cellpadding='5'>";
                for (const row of data.matrix) {
                    html += "<tr>";
                    for (const val of row) {
                        html += `<td>${val}</td>`;
                    }
                    html += "</tr>";
                }
                html += "</table>";
                document.getElementById('result').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('result').textContent = "Eroare la generare.";
            });
        });
    </script>
</body>
</html>
