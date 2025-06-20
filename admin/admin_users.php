<?php
session_start();
if (!isset($_SESSION['token'])) {
    header("Location: ../auth/login.php");
    exit;
}
$token = $_SESSION['token'];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestionare Utilizatori</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script>
        const token = <?= json_encode($token) ?>;

        async function fetchUsers() {
            const res = await fetch('get_users.php', {
                headers: { 'X-Auth-Token': token }
            });
            const users = await res.json();
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            if (!Array.isArray(users)) {
                tbody.innerHTML = '<tr><td colspan="6">Eroare: nu s-au putut încărca utilizatorii.</td></tr>';
                return;
            }

            users.forEach(user => {
                const row = `<tr>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>
                        <select onchange="changeRole(${user.id}, this.value)">
                            <option value="user" ${user.role === 'user' ? 'selected' : ''}>user</option>
                            <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>admin</option>
                        </select>
                    </td>
                    <td>
                        <a href="admin_user_data.php?id=${user.id}" class="btn green">Date</a>
                        <button onclick="resetPassword(${user.id})" class="btn yellow">Reset</button>
                        <button onclick="deleteUser(${user.id})" class="btn red">Șterge</button>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        }

        async function changeRole(userId, role) {
            const res = await fetch('change_role.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Auth-Token': token
                },
                body: JSON.stringify({ user_id: userId, role })
            });

            const data = await res.json();

            if (data.selfRoleChange) {
                alert("Ți-ai schimbat propriul rol. Vei fi delogat.");
                window.location.href = '../auth/logout.php';
                return;
            }

            fetchUsers();
        }

        async function deleteUser(userId) {
            if (!confirm('Ești sigur că vrei să ștergi acest utilizator?')) return;

            await fetch('delete_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Auth-Token': token
                },
                body: JSON.stringify({ user_id: userId })
            });

            fetchUsers();
        }

        async function resetPassword(userId) {
            const newPassword = prompt("Introdu parola nouă:");
            if (!newPassword || newPassword.length < 4) {
                alert("Parola trebuie să aibă cel puțin 4 caractere.");
                return;
            }

            const res = await fetch('reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Auth-Token': token
                },
                body: JSON.stringify({ user_id: userId, new_password: newPassword })
            });

            const result = await res.json();

            if (result.success) {
                alert("Parola a fost resetată cu succes!");
            } else {
                alert("Eroare la resetare: " + result.error);
            }
        }

        window.onload = fetchUsers;
    </script>
</head>
<body>
    <div class="admin-container">
        <a href="../index.php" class="back-btn">⬅ Înapoi la Pagina Principală</a>
        <h2 class="admin-title">⚙️ Administrare Utilizatori</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Setează Rol</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody id="userTableBody"></tbody>
        </table>
    </div>
</body>
</html>
