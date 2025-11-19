<?php
// BACKEND/Privada/reset_password.php
require __DIR__ . '/../DATABASE/conexion.php';

$token  = $_GET['token'] ?? '';
$error  = '';
$exito  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token     = $_POST['token'] ?? '';
    $password  = $_POST['password'] ?? '';
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
                        SET password_hash = :password
                        WHERE id_usuario = :id
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
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../FRONTEND/CSS/styles.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3 text-center">Restablecer contraseña</h1>

                    <?php if (!empty($exito)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($exito); ?>
                            <div class="mt-2">
                                <a href="login.php" class="btn btn-sm btn-primary">Ir al login</a>
                            </div>
                        </div>
                    <?php elseif (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php else: ?>
                        <?php
                        // Validar token para mostrar el formulario al entrar por GET
                        if ($token) {
                            $stmt = $pdo->prepare("
                                SELECT user_id, expires_at
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

                                <div class="mb-3">
                                    <label class="form-label">Nueva contraseña</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirmar contraseña</label>
                                    <input type="password" name="password2" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Guardar nueva contraseña
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                El enlace no es válido o ha expirado.
                            </div>
                        <?php endif; } else { ?>
                            <div class="alert alert-danger">
                                Token no proporcionado.
                            </div>
                        <?php } ?>
                    <?php endif; ?>

                    <hr class="my-4">
                    <p class="text-center mb-0">
                        <a href="../../index.html">← Volver al sitio público</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
