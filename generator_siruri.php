<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Șiruri</title>
    <link rel="stylesheet" href="mainpag.css">
</head>
<body>
    <div class="container">
        <h2 class="section-title">🔢 Generator Șiruri de Numere</h2>

        <form id="sirForm">
            <label>Lungime:</label>
            <input type="number" name="length" min="1" max="1000" required><br><br>

            <label>Valoare minimă:</label>
            <input type="number" name="min" required><br><br>

            <label>Valoare maximă:</label>
            <input type="number" name="max" required><br><br>

            <label>Sortare:</label>
            <select name="order">
                <option value="none">Fără</option>
                <option value="asc">Crescător</option>
                <option value="desc">Descrescător</option>
            </select><br><br>

            <button type="submit">Generează</button>
        </form>

        <div id="result" style="margin-top:20px;"></div>
    </div>

    <script>
        document.getElementById('sirForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('api/generator_sir.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('result').innerHTML =
                    "<strong>Șir generat:</strong><br>" + data.array.join(', ');
            })
            .catch(err => {
                document.getElementById('result').textContent = "Eroare: " + err;
            });
        });
    </script>
</body>
</html>
