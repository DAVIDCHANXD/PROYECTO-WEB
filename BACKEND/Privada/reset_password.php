<?php
// reset_password.php
session_start();
require_once __DIR__ . '/BACKEND/DATABASE/conexion.php';

$token = $_GET['token'] ?? '';

if ($token === '') {
    die('Token no válido.');
}

// Verificar token y que no haya expirado
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-3 text-center">Restablecer contraseña</h3>

                    <form action="reset_password_process.php" method="post">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password2" class="form-label">Repite la contraseña</label>
                            <input type="password" name="password2" id="password2" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Guardar nueva contraseña
                        </button>
                    </form>

                    <a href="login.php" class="btn btn-link w-100 mt-2">Volver al login</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
