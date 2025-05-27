<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Șiruri de Caractere</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2 class="section-title">🔤 Generator Șiruri de Caractere</h2>

        <form id="textForm">
            <label>Lungime:</label>
            <input type="number" name="length" min="1" max="1000" required><br><br>

            <label>Tip caractere:</label>
            <select name="charset">
                <option value="letters">Litere (a-z, A-Z)</option>
                <option value="letters_digits">Litere și cifre</option>
                <option value="digits">Doar cifre</option>
                <option value="all">Litere, cifre și simboluri</option>
            </select><br><br>

            <button type="submit">Generează</button>
        </form>

        <div id="result" style="margin-top:20px;"></div>
    </div>

    <script>
        document.getElementById('textForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../api/string_generator.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('result').innerHTML = "Eroare: " + data.error;
                    return;
                }

                document.getElementById('result').innerHTML =
                    "<strong>Șir generat:</strong><br>" + `<code>${data.text}</code>`;
            })
            .catch(err => {
                document.getElementById('result').textContent = "Eroare la generare.";
            });
        });
    </script>
</body>
</html>
