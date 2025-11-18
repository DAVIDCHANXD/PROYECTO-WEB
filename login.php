<?php
// login.php
session_start();
require_once __DIR__ . '/BACKEND/DATABASE/conexion.php';

$errores = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errores = 'Por favor ingresa tu correo y contraseña.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email AND activo = 1');
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $errores = 'Usuario o contraseña incorrectos.';
        } else {
            $hash = $usuario['password_hash'];

            // Normal: usar password_verify, pero dejo compatibilidad si el admin viejo tiene contraseña en texto plano
            $ok = password_verify($password, $hash) || $password === $hash;

            if ($ok) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['id_rol']     = $usuario['id_rol'];

                header('Location: dashboard.php');
                exit;
            } else {
                $errores = 'Usuario o contraseña incorrectos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="FRONTEND/CSS/styles.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3 text-center">Iniciar sesión</h1>
                    <p class="text-center text-muted mb-4">
                        Accede al panel para administrar los animales en adopción.
                    </p>

                    <?php if ($errores): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errores); ?></div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control"
                                required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Entrar
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        ¿No tienes cuenta?
                        <a href="register.php">Registrarte</a>
                    </p>
                    <p class="text-center mb-0">
                        <a href="forgot_password.php">¿Olvidaste tu contraseña?</a>
                    </p>
                    <p class="text-center mt-2 mb-0">
                        <a href="/index.html">← Volver al sitio público</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
