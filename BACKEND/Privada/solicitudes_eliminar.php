<?php
// BACKEND/Privada/solicitudes_eliminar.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../DATABASE/conexion.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: solicitudes.php?msg=error');
    exit;
}
try {
    $stmt = $pdo->prepare("DELETE FROM solicitudes_adopcion WHERE id_solicitud = :id");
    $stmt->execute([':id' => $id]);
    header('Location: solicitudes.php?msg=eliminar_ok');
    exit;
} catch (PDOException $e) {
    header('Location: solicitudes.php?msg=error');
    exit;
}
