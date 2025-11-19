<?php
// BACKEND/Privada/forgot_password.php
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../DATABASE/conexion.php';

    $correo = trim($_POST['correo'] ?? '');

    try {
        // Buscar usuario por correo (columna email, PK id_usuario)
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generar token seguro
            $token  = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600); // 1 hora

            // Guardar token
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)
            ");
            $stmt->execute([
                ':user_id'    => $user['id_usuario'],
                ':token'      => $token,
                ':expires_at' => $expira
            ]);

            // Construir link absoluto respetando subcarpeta del proyecto
            $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host     = $_SERVER['HTTP_HOST'];
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); // ej: /PROYECTO-WEB/BACKEND/Privada

            $link = $scheme . $host . $basePath . "/reset_password.php?token=" . urlencode($token);

            // Modo escuela: mostrar link en la pantalla
            $mensaje = "Copia este enlace para restablecer tu contraseña:<br>" .
                       htmlspecialchars($link);
        } else {
            $mensaje = "Si el correo existe, se enviará un enlace de recuperación.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
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
                    <h1 class="h4 mb-3 text-center">Recuperar contraseña</h1>
                    <p class="text-muted text-center mb-4">
                        Escribe tu correo y se generará un enlace de recuperación.
                    </p>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Enviar enlace
                        </button>
                    </form>

                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-info mt-3">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">
                    <p class="text-center mb-0">
                        <a href="login.php">← Volver al login</a>
                    </p>
                    <p class="text-center mt-2 mb-0">
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
