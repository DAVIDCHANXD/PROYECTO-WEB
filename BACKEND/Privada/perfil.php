<?php
// BACKEND/Privada/perfil.php
session_start();

// 1. Si no hay sesión, lo mandamos a login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

// 2. Conexión a la base de datos
require_once __DIR__ . '/../DATABASE/conexion.php';

$idUsuario = $_SESSION['id_usuario'];
$mensajeOk = '';
$mensajeError = '';

// 3. Si viene por POST, procesamos actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $email === '') {
        $mensajeError = 'El nombre y el correo son obligatorios.';
    } else {
        // OJO: ajusta id_usuario, nombre, email, telefono al nombre real de tus columnas
        $sql = 'UPDATE usuarios 
                SET nombre = :nombre,
                    email = :email,
                    telefono = :telefono
                WHERE id_usuario = :id';

        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':nombre'   => $nombre,
            ':email'    => $email,
            ':telefono' => $telefono !== '' ? $telefono : null,
            ':id'       => $idUsuario
        ]);

        if ($ok) {
            $mensajeOk = 'Perfil actualizado correctamente.';
            // actualizamos el nombre de la sesión para el navbar / dashboard
            $_SESSION['nombre'] = $nombre;
        } else {
            $mensajeError = 'Ocurrió un error al guardar los cambios.';
        }
    }
}

// 4. Obtener datos actuales del usuario para mostrarlos en el formulario
$sqlDatos = 'SELECT nombre, email, telefono 
             FROM usuarios 
             WHERE id_usuario = :id'; // ajusta si tu PK tiene otro nombre

$stmtDatos = $pdo->prepare($sqlDatos);
$stmtDatos->execute([':id' => $idUsuario]);
$usuario = $stmtDatos->fetch(PDO::FETCH_ASSOC);

// Si por algún motivo no encuentra el usuario, cerramos sesión
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Panel AdoptaConAmor</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Link a perfil -->
        <li class="nav-item">
          <a class="nav-link active" href="perfil.php">Mi perfil</a>
        </li>
        <!-- Aquí puedes tener tu botón de salir -->
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Cerrar sesión</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTENIDO -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h1 class="mb-4 text-center">Mi perfil</h1>

            <?php if ($mensajeOk): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mensajeOk); ?>
                </div>
            <?php endif; ?>

            <?php if ($mensajeError): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($mensajeError); ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="perfil.php">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono (opcional)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="telefono"
                                name="telefono"
                                value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"
                            >
                        </div>

                        <!-- Más campos si quieres, por ejemplo dirección, ciudad, etc. -->

                        <button type="submit" class="btn btn-primary w-100">
                            Guardar cambios
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-3 text-muted">
                Estás conectado como <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
