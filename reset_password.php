<?php
require __DIR__ . '/BACKEND/DATABASE/conexion.php';

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password !== $password2) {
        $error = "Las contraseñas no coinciden.";
    } else {
        try {
            // Buscar token
            $stmt = $pdo->prepare("
                SELECT pr.user_id, pr.expires_at
                FROM password_resets pr
                WHERE pr.token = :token
                LIMIT 1
            ");
            $stmt->execute([':token' => $token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $error = "Token inválido.";
            } else {
                // Verificar si ya expiró
                if (strtotime($row['expires_at']) < time()) {
                    $error = "El enlace ha expirado.";
                } else {
                    // Actualizar contraseña del usuario
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("
                        UPDATE usuarios
                        SET password = :password
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':password' => $hash,
                        ':id'       => $row['user_id']
                    ]);

                    // Borrar token usado
                    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
                    $stmt->execute([':token' => $token]);

                    $exito = "Contraseña actualizada correctamente. Ya puedes iniciar sesión.";
                }
            }

        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
</head>
<body>
    <h1>Restablecer contraseña</h1>

    <?php if (!empty($exito)): ?>
        <p><?php echo $exito; ?></p>
    <?php elseif (!empty($error)): ?>
        <p><?php echo $error; ?></p>
    <?php else: ?>
        <?php
        // Validar token para mostrar el formulario al entrar por GET
        if ($token) {
            $stmt = $pdo->prepare("
                SELECT id, expires_at
                FROM password_resets
                WHERE token = :token
                LIMIT 1
            ");
            $stmt->execute([':token' => $token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && strtotime($row['expires_at']) >= time()): 
        ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <label>Nueva contraseña:</label>
                <input type="password" name="password" required>

                <label>Confirmar contraseña:</label>
                <input type="password" name="password2" required>

                <button type="submit">Guardar nueva contraseña</button>
            </form>
        <?php else: ?>
            <p>El enlace no es válido o ha expirado.</p>
        <?php endif; } else { ?>
            <p>Token no proporcionado.</p>
        <?php } ?>
    <?php endif; ?>

</body>
</html>
