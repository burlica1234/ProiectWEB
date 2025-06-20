<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Grafuri / Arbori</title>
    <link rel="stylesheet" href="../assets/css/graphs.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="logo">🧮 Generator Informatica</h1>
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

    <div class="container">
        <h2 class="section-title">🕸️ Generator Grafuri / Arbori</h2>

        <form id="graphForm">
            <label for="nodes">Număr noduri:</label>
            <input type="number" name="nodes" id="nodes" min="1" max="100" required>

            <div id="edges-group">
                <label for="edges">Număr muchii:</label>
                <input type="number" name="edges" id="edges" min="0">
            </div>

            <label for="type">Tip graf:</label>
            <select name="type" id="type">
                <option value="normal">Normal</option>
                <option value="tree">Arbore</option>
                <option value="bipartite">Bipartit</option>
            </select>

            <label for="orientation">Orientare:</label>
            <select name="orientation" id="orientation">
                <option value="undirected">Neorientat</option>
                <option value="directed">Orientat</option>
            </select>

            <div id="format-group">
                <label for="format">Format afișare:</label>
                <select name="format" id="format">
                    <option value="edges">Listă de muchii</option>
                    <option value="adjacency">Matrice de adiacență</option>
                    <option value="parents" class="tree-only">Vectori de tați (doar arbori)</option>
                </select>
            </div>

            <button type="submit">Generează graf</button>
            <br><br>
            
            <button id="saveBtn" type="button" disabled>Salvare</button>
            <br><br>
            <select id="savedGraphs">
                <option value="">-- Încarcă un graf salvat --</option>
            </select>
            
            <button id="loadBtn" type="button" disabled>Încarcă</button>
        </form>

        <script>
            const typeSelect = document.getElementById('type');
            const edgesGroup = document.getElementById('edges-group');

            function toggleEdgesField() {
                if (typeSelect.value === 'tree') {
                    edgesGroup.style.display = 'none';
                } else {
                    edgesGroup.style.display = 'block';
                }

                const parentsOption = document.querySelector('#format option[value="parents"]');
                if (typeSelect.value === 'tree') {
                    parentsOption.style.display = 'block';
                } else {
                    parentsOption.style.display = 'none';
                    if (document.getElementById('format').value === 'parents') {
                        document.getElementById('format').value = 'edges';
                    }
                }
            }

            typeSelect.addEventListener('change', toggleEdgesField);
            window.addEventListener('DOMContentLoaded', toggleEdgesField);
        </script>

        <a href="../index.php" class="back-button">← Înapoi la pagina principală</a>

        <div id="result"></div>
        <svg id="graphCanvas"></svg>
    </div>

    <script>
        let currentGraph = {};

        document.getElementById('graphForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../api/graph/generate_graph.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('result').innerHTML = "Eroare: " + data.error;
                    return;
                }
                
                currentGraph = data;
                document.getElementById('saveBtn').disabled = false;

                const result = document.getElementById('result');
                const canvas = document.getElementById('graphCanvas');
                canvas.innerHTML = '';
                const isDirected = document.getElementById('orientation').value === 'directed';
                if (isDirected) {
                    canvas.innerHTML = `
                        <defs>
                            <marker id="arrow" markerWidth="10" markerHeight="10" refX="20" refY="5"
                                    orient="auto" markerUnits="strokeWidth">
                                <path d="M0,0 L0,10 L10,5 Z" fill="#34495e" />
                            </marker>
                        </defs>
                    `;
                }

                const format = document.getElementById('format').value;
                if (format === 'adjacency' && data.adjacency) {
                    const matrix = data.adjacency;
                    const maxSize = matrix.length;
                    const scale = maxSize > 30 ? 30 / maxSize : 1;
                    const tdSize = Math.floor(2 * scale) + 'em';
                    const fontSize = (1 * scale).toFixed(2) + 'em';

                    let html = `<div style="overflow:auto; text-align:center;">
                                <strong>Matrice de adiacență:</strong><br>
                                <table style="margin:0 auto; table-layout:fixed; border-collapse:collapse;">
                                <tr><th style="width:${tdSize}; font-size:${fontSize}; border:1px solid #333;"></th>` +
                                matrix[0].map((_, j) => `<th style="width:${tdSize}; font-size:${fontSize}; border:1px solid #333;">${j}</th>`).join('') +
                                '</tr>';

                    matrix.forEach((row, i) => {
                        html += `<tr><th style="width:${tdSize}; font-size:${fontSize}; border:1px solid #333;">${i}</th>` +
                                row.map(val =>
                                    `<td style="width:${tdSize}; padding:3px; font-size:${fontSize}; border:1px solid #333;">${val}</td>`
                                ).join('') +
                                '</tr>';
                    });

                    html += '</table></div>';
                    result.innerHTML = html;
                } else if (format === 'parents' && data.parents) {
                    const columns = 4;
                    const perCol = Math.ceil(data.parents.length / columns);
                    let cols = Array.from({ length: columns }, () => '');

                    data.parents.forEach((p, i) => {
                        const col = Math.floor(i / perCol);
                        if(p === -1)
                            cols[col] += `<div>radacina ${i}</div>`;
                        else 
                            cols[col] += `<div>nod ${i} → tata ${p}</div>`;
                    });

                    result.innerHTML = `
                        <strong>Vectori de tați:</strong><br>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            ${cols.map(col => `<div style="flex: 1; font-family: monospace; font-size: 13px;">${col}</div>`).join('')}
                        </div>
                    `;
                } else if (data.edges) {
                    const columns = 4;
                    const edgesPerCol = Math.ceil(data.edges.length / columns);
                    let columnsHtml = Array.from({ length: columns }, () => '');

                    const isDirected = document.getElementById('orientation').value === 'directed';
                    data.edges.forEach((e, i) => {
                        const colIndex = Math.floor(i / edgesPerCol);
                        columnsHtml[colIndex] += `<div>${e[0]} ${isDirected ? '→' : '-'} ${e[1]}</div>`;
                    });

                    result.innerHTML = `
                        <strong>Muchii generate:</strong><br>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            ${columnsHtml.map(col => `<div style="flex: 1; font-family: monospace; font-size: 13px;">${col}</div>`).join('')}
                        </div>
                    `;
                }

                const nodeCount = parseInt(document.getElementById('nodes').value);

                if (nodeCount > 10) {
                    result.innerHTML += "<br><em>Graful nu poate fi afișat grafic, având peste 10 noduri.</em>";
                    return;
                }

                const nodePos = [];
                const width = 600;
                const height = 400;

                if (document.getElementById('type').value === 'tree' && data.parents) {
                    const parents = data.parents;
                    const levels = {};
                    const children = Array.from({ length: nodeCount }, () => []);

                    for (let i = 0; i < parents.length; i++) {
                        const p = parents[i];
                        if (p !== -1) children[p].push(i);
                    }

                    function assignLevels(node, level = 0) {
                        if (!levels[level]) levels[level] = [];
                        levels[level].push(node);
                        for (const c of children[node]) {
                            assignLevels(c, level + 1);
                        }
                    }

                    const root = parents.findIndex(p => p === -1);
                    assignLevels(root);

                    const levelHeight = 100;
                    const levelCount = Object.keys(levels).length;
                    canvas.setAttribute("width", width);
                    canvas.setAttribute("height", 50 + levelCount * levelHeight);
                    for (const [level, nodes] of Object.entries(levels)) {
                        const y = 50 + levelHeight * parseInt(level);
                        const spacing = width / (nodes.length + 1);
                        nodes.forEach((node, i) => {
                            const x = spacing * (i + 1);
                            nodePos[node] = { x, y };
                        });
                    }
                } else if (document.getElementById('type').value === 'bipartite') {
                    canvas.setAttribute("width", width);
                    canvas.setAttribute("height", height);
                    const part1 = [];
                    const part2 = [];

                    for (let i = 0; i < nodeCount; i++) {
                        if (i < Math.floor(nodeCount / 2)) {
                            part1.push(i);
                        } else {
                            part2.push(i);
                        }
                    }

                    const spacingY1 = height / (part1.length + 1);
                    const spacingY2 = height / (part2.length + 1);
                    part1.forEach((node, idx) => {
                        nodePos[node] = { x: 150, y: spacingY1 * (idx + 1) };
                    });
                    part2.forEach((node, idx) => {
                        nodePos[node] = { x: 450, y: spacingY2 * (idx + 1) };
                    });
                } else {
                    canvas.setAttribute("width", width);
                    canvas.setAttribute("height", height + 50);
                    const centerX = width / 2;
                    const centerY = height / 2;
                    const radius = 150;

                    for (let i = 0; i < nodeCount; i++) {
                        const angle = 2 * Math.PI * i / nodeCount;
                        const x = centerX + radius * Math.cos(angle);
                        const y = centerY + radius * Math.sin(angle);
                        nodePos.push({ x, y });
                    }
                }

                for (const [from, to] of data.edges) {
                    const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
                    line.setAttribute("x1", nodePos[from].x);
                    line.setAttribute("y1", nodePos[from].y);
                    line.setAttribute("x2", nodePos[to].x);
                    line.setAttribute("y2", nodePos[to].y);
                    if (isDirected) {
                        line.setAttribute("marker-end", "url(#arrow)");
                    }
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
                    text.setAttribute("dy", ".3em");
                    text.setAttribute("text-anchor", "middle");
                    text.textContent = i;
                    canvas.appendChild(text);
                }
            })
            .catch(err => {
                document.getElementById('result').textContent = "Eroare la generare.";
            });
        });

        function fetchSaved() {
            fetch('../api/graph/list_saved_graphs.php')
                .then(r => r.json())
                .then(data => {
                    const sel = document.getElementById('savedGraphs');
                    sel.innerHTML = '<option value="">-- Încarcă un șir salvat --</option>';
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
            const title = prompt('Titlu pentru acest graf:');
            if (!title) return;
            fetch('../api/graph/save_graph.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, graph: currentGraph })
            })
            .then(r => r.json())
            .then(resp => {
                alert(resp.success ? 'Graf salvat!' : resp.error);
                if (resp.success) fetchSaved();
            });
        });

        // lista de incarcari
        document.getElementById('savedGraphs').addEventListener('change', e => {
            document.getElementById('loadBtn').disabled = !e.target.value;
        });

        // incarcare
        document.getElementById('loadBtn').addEventListener('click', () => {
            const id = document.getElementById('savedGraphs').value;
            fetch(`../api/graph/load_graph.php?id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.error) return alert(data.error);
                    currentGraph = data.graph;
                });
        });

        fetchSaved();
    </script>
</body>
</html>
