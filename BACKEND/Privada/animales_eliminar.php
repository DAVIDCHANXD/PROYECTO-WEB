<?php
// BACKEND/Privada/animales_eliminar.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../DATABASE/conexion.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: animales_listar.php?msg=error');
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("DELETE FROM fotos_animal WHERE id_animal = :id");
    $stmt->execute([':id' => $id]);
    $stmt2 = $pdo->prepare("DELETE FROM animales WHERE id_animal = :id");
    $stmt2->execute([':id' => $id]);
    $pdo->commit();
    header('Location: animales_listar.php?msg=eliminar_ok');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: animales_listar.php?msg=error');
    exit;
}
