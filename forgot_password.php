<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/BACKEND/DATABASE/conexion.php';

    $correo = trim($_POST['correo']);

    try {
        // Buscar usuario por correo
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $stmt->execute([':correo' => $correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generar token seguro
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600); // 1 hora

            // Guardar token
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)
            ");
            $stmt->execute([
                ':user_id'    => $user['id'],
                ':token'      => $token,
                ':expires_at' => $expira
            ]);

            // Link de recuperación
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host   = $_SERVER['HTTP_HOST']; // puede ser 192.168.x.x, localhost, dominio, etc.

            $link = $scheme . $host . "/reset_password.php?token=" . urlencode($token);


            // 🔹 MODO ESCUELA: mostrar link en pantalla
            $mensaje = "Copia este enlace para restablecer tu contraseña:<br>$link";

            // 🔹 MODO PRO: aquí enviarías un correo real con $link
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
</head>
<body>
    <h1>Recuperar contraseña</h1>
    <form method="POST">
        <label>Correo electrónico:</label>
        <input type="email" name="correo" required>
        <button type="submit">Enviar enlace</button>
    </form>

    <?php if (!empty($mensaje)): ?>
        <p><?php echo $mensaje; ?></p>
    <?php endif; ?>
</body>
</html>