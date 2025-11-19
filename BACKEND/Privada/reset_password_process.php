<?php
// reset_password_process.php
session_start();
require_once __DIR__ . '/BACKEND/DATABASE/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$token     = $_POST['token'] ?? '';
$password  = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

if ($token === '' || $password === '' || $password !== $password2) {
    die('Datos inválidos o las contraseñas no coinciden.');
}

// 1. Verificar token y que no haya expirado
$sql = "SELECT id_usuario FROM usuarios 
        WHERE reset_token = ? 
          AND reset_expira > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die('El enlace de recuperación es inválido o ha expirado.');
}

// 2. Hashear la nueva contraseña
$hash = password_hash($password, PASSWORD_BCRYPT);

// 3. Actualizar contraseña y limpiar token
$sqlUpdate = "UPDATE usuarios 
              SET password = ?, reset_token = NULL, reset_expira = NULL 
              WHERE id_usuario = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $hash, $usuario['id_usuario']);
$stmtUpdate->execute();

echo "Tu contraseña ha sido actualizada. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
