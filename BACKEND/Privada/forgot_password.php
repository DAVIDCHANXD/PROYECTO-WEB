<?php
// forgot_password_process.php
session_start();
require_once __DIR__ . '/BACKEND/DATABASE/conexion.php'; // AJUSTA RUTA

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    header('Location: forgot_password.php?msg=' . urlencode('Ingresa un correo válido.'));
    exit;
}

// --- 1. Verificar que el correo existe ---
$sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);   // $conn: tu conexión (mysqli)
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    // Por seguridad no digas "no existe ese correo"
    header('Location: forgot_password.php?msg=' . urlencode('Si el correo existe, se enviará un enlace de recuperación.'));
    exit;
}

// --- 2. Generar token y fecha de expiración ---
$token = bin2hex(random_bytes(32)); // 64 caracteres hex
$expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

$sqlUpdate = "UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE id_usuario = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("ssi", $token, $expira, $usuario['id_usuario']);
$stmtUpdate->execute();

// --- 3. Crear enlace de recuperación ---
$baseUrl = 'https://TU_DOMINIO_O_IP/reset_password.php'; // AJUSTA
$link = $baseUrl . '?token=' . urlencode($token);

// --- 4. Enviar correo (o mostrar en pantalla para pruebas) ---

// OPCIÓN A: para pruebas de escuela, mostrar el enlace en pantalla
/*
echo "Enlace de recuperación:<br>";
echo '<a href="'.$link.'">'.$link.'</a>';
exit;
*/

// OPCIÓN B: enviar correo (si ya tienes configurado mail en el servidor)
$asunto = "Recuperación de contraseña";
$mensaje = "Hola,\n\nHaz clic en el siguiente enlace para restablecer tu contraseña:\n$link\n\nEste enlace expira en 1 hora.";
$encabezados = "From: no-reply@tu-dominio.com\r\n";

@mail($email, $asunto, $mensaje, $encabezados);

header('Location: forgot_password.php?msg=' . urlencode('Si el correo existe, se enviará un enlace de recuperación.'));
exit;
