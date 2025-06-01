<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Grafuri / Arbori</title>
   
    <link rel="stylesheet" href="../assets/css/graphs.css">
    
</head>
<body>
    <div class="container">
        <h2 class="section-title">🕸️ Generator Grafuri / Arbori</h2>

        <form id="graphForm">
            <label for="nodes">Număr noduri:</label>
            <input type="number" name="nodes" id="nodes" min="1" max="100" required>

            <label for="edges">Număr muchii:</label>
            <input type="number" name="edges" id="edges" min="0" required>

            <label for="type">Tip:</label>
            <select name="type" id="type">
                <option value="undirected">Neorientat</option>
                <option value="directed">Orientat</option>
                <option value="tree">Arbore</option>
            </select>

            <button type="submit">Generează graf</button>
        </form>

        <a href="../index.php" class="back-button">← Înapoi la pagina principală</a>

        <div id="result"></div>
        <svg id="graphCanvas"></svg>
    </div>

    <script>
        document.getElementById('graphForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../api/graph_generator.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                const result = document.getElementById('result');
                const canvas = document.getElementById('graphCanvas');
                canvas.innerHTML = '';
                if (data.error) {
                    result.innerHTML = "Eroare: " + data.error;
                    return;
                }

                result.innerHTML = "<strong>Muchii generate:</strong><br><ul>" +
                    data.edges.map(e => `<li>${e[0]} - ${e[1]}</li>`).join('') + "</ul>";

                const nodeCount = parseInt(document.getElementById('nodes').value);
                const radius = 170;
                const centerX = 300;
                const centerY = 200;
                const nodePos = [];

                const padding = 40;
                const width = 600;
                const height = 400;

                for (let i = 0; i < nodeCount; i++) {
                    const x = Math.random() * (width - 2 * padding) + padding;
                    const y = Math.random() * (height - 2 * padding) + padding;
                    nodePos.push({x, y});
                }


                for (const [from, to] of data.edges) {
                    const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
                    line.setAttribute("x1", nodePos[from].x);
                    line.setAttribute("y1", nodePos[from].y);
                    line.setAttribute("x2", nodePos[to].x);
                    line.setAttribute("y2", nodePos[to].y);
                    canvas.appendChild(line);
                }

                for (let i = 0; i < nodePos.length; i++) {
                    const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
                    circle.setAttribute("cx", nodePos[i].x);
                    circle.setAttribute("cy", nodePos[i].y);
                    circle.setAttribute("r", "20");
                    canvas.appendChild(circle);

                    const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
                    text.setAttribute("x", nodePos[i].x);
                    text.setAttribute("y", nodePos[i].y);
                    text.textContent = i;
                    canvas.appendChild(text);
                }
            })
            .catch(err => {
                document.getElementById('result').textContent = "Eroare la generare.";
            });
        });
    </script>
</body>
</html>
