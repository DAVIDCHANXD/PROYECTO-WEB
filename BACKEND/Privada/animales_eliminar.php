<?php
// BACKEND/Privada/animales_eliminar.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../DATABASE/conexion.php';

// id viene por GET: animales_eliminar.php?id=123
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: animales_listar.php?msg=error');
    exit;
}

try {
    // Opcional pero recomendable: transacción
    $pdo->beginTransaction();

    // 1) Borramos sus fotos asociadas
    $stmt = $pdo->prepare("DELETE FROM fotos_animal WHERE id_animal = :id");
    $stmt->execute([':id' => $id]);

    // 2) Borramos el animal (OJO: la columna es id_animal)
    $stmt2 = $pdo->prepare("DELETE FROM animales WHERE id_animal = :id");
    $stmt2->execute([':id' => $id]);

    $pdo->commit();

    header('Location: animales_listar.php?msg=eliminar_ok');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Si quieres ver el error mientras desarrollas, descomenta esta línea:
    // die('Error al eliminar: ' . $e->getMessage());

    header('Location: animales_listar.php?msg=error');
    exit;
}
