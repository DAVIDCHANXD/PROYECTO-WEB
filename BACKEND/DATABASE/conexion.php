<?php
// BACKEND/DATABASE/conexion.php

$DB_PATH = __DIR__ . '/DATABASE.db';

try {
    $pdo = new PDO('sqlite:' . $DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Error de conexión a la base de datos: ' . $e->getMessage();
    exit;
}
?>