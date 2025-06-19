<?php
// auth/db.php

$host = 'localhost';
$dbname = 'proiect_web';
$user = 'root';          
$pass = '';              

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Conexiunea la baza de date a eșuat: ' . $e->getMessage()
    ]);
    exit;
}
