<?php
// BACKEND/Privada/solicitud_guardar.php
session_start();
require_once __DIR__ . '/../DATABASE/conexion.php';
$redirect = '/FRONTEND/Publico/solicitud_adopcion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['redirect'])) {
    $tmp = $_POST['redirect'];
    if (is_string($tmp) && strlen($tmp) > 0 && $tmp[0] === '/') {
        $redirect = $tmp;
    }
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$correo          = trim($_POST['correo'] ?? '');
$telefono        = trim($_POST['telefono'] ?? '');
$id_animal       = filter_input(INPUT_POST, 'id_animal', FILTER_VALIDATE_INT);
$mensaje_usuario = trim($_POST['mensaje'] ?? '');
if (
    $nombre_completo === '' ||
    $correo === '' ||
    $telefono === '' ||
    !$id_animal ||
    $mensaje_usuario === ''
) {
    header('Location: ' . $redirect . '?error=1');
    exit;
}
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = (int)$_SESSION['id_usuario'];
} else {
    $id_usuario = 1;
}
$mensaje = "Nombre: {$nombre_completo}\n";
$mensaje .= "Correo: {$correo}\n";
$mensaje .= "Teléfono: {$telefono}\n\n";
$mensaje .= "Mensaje:\n{$mensaje_usuario}";
try {
    $sql = "INSERT INTO solicitudes_adopcion 
                (id_animal, id_usuario, mensaje, id_estado_solicitud)
            VALUES
                (:id_animal, :id_usuario, :mensaje, :estado)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_animal'  => $id_animal,
        ':id_usuario' => $id_usuario,
        ':mensaje'    => $mensaje,
        ':estado'     => 1,
    ]);

    header('Location: ' . $redirect . '?ok=1');
    exit;
} catch (PDOException $e) {
    header('Location: ' . $redirect . '?error=1');
    exit;
}
