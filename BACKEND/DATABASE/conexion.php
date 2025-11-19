<?php
// conexion.php
// Conexión centralizada a MariaDB para el proyecto AdoptaConAmor

$DB_HOST = '127.0.0.1';       // Servidor MariaDB (mismo servidor que Apache)
$DB_PORT = 3306;              // Puerto por defecto de MariaDB/MySQL
$DB_NAME = 'adoptaconamor';   // Nombre de la base de datos
$DB_USER = 'mdj';             // Usuario que creaste
$DB_PASS = '2323';            // Contraseña del usuario
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=$DB_CHARSET";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Errores como excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Prepared reales
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    // Si quieres depurar, descomenta esta línea:
    // echo "Conexión OK a MariaDB";
} catch (PDOException $e) {
    // En producción mejor no mostrar el detalle completo
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>