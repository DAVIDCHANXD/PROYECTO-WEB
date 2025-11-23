<?php
// BACKEND/Privada/login.php
session_start();
require_once __DIR__ . '/../DATABASE/conexion.php';

$errores = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errores = 'Por favor ingresa tu correo y contraseña.';
    } else {
        // Buscar usuario activo por correo
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email AND activo = 1');
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $errores = 'Usuario o contraseña incorrectos.';
        } else {
            $hash = $usuario['password_hash'] ?? '';

            $ok = false;
            if (!empty($hash)) {
                // Normal: password almacenada con password_hash
                if (password_verify($password, $hash) || $password === $hash) {
                    $ok = true;
                }
            }

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

<!-- Tema general -->
<link rel="stylesheet" href="/FRONTEND/CSS/index.css">

<!-- Estilos página de autenticación (login / registro) -->
<link rel="stylesheet" href="/FRONTEND/CSS/auth.css">
</head>

<body class="d-flex flex-column min-vh-100 auth-bg">

<!-- NAVBAR igual al INDEX -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../../index.html">
      <span class="logo-pill">AC</span> AdoptaConAmor
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="../../index.html">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="/FRONTEND/Publico/animales.php">Animales</a></li>
        <li class="nav-item"><a class="nav-link" href="/FRONTEND/Publico/como-adoptar.php">Cómo adoptar</a></li>
        <li class="nav-item"><a class="nav-link" href="/FRONTEND/Publico/contacto.php">Contacto</a></li>
        <li class="nav-item ms-lg-3">
          <a class="btn btn-outline-light btn-sm active" href="/BACKEND/Privada/login.php">
            Iniciar sesión
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<!-- HERO igual al inicio -->
<section class="hero-section d-flex align-items-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <!-- TARJETA GLASS -->
                <div class="card shadow-lg border-0 glass-card p-4">

                    <h1 class="h4 mb-3 text-center fw-bold">Iniciar sesión</h1>
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
                                   required value="<?php echo htmlspecialchars($email); ?>">
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
                    <p class="text-center mt-2 mb-0">
                        <a href="../../index.html">← Volver al sitio público</a>
                    </p>

                </div>

            </div>
        </div>
    </div>
</section>




<footer class="text-white text-center py-3 mt-auto site-footer">
  <div class="container">
    <small>&copy; <?php echo date('Y'); ?> AdoptaConAmor </small>
  </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

