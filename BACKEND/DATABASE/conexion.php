<?php
$host = 'mysql-adopta-con-amor.alwaysdata.net';   // El host que viste en Databases → MySQL
$db   = 'adopta-con-amor_adopta_db';              // Nombre completo de la BD
$user = '442854';                 // Usuario MySQL
$pass = 'DAVIDCHAN2306';                // Password MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
