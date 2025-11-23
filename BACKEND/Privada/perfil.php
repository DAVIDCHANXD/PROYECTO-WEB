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

$idUsuario    = $_SESSION['id_usuario'];
$mensajeError = '';

// 3. Si viene por POST, procesamos actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $email === '') {
        $mensajeError = 'El nombre y el correo son obligatorios.';
    } else {
        try {
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
        } catch (PDOException $e) {
            $ok = false;
            // Si quieres ver el error exacto mientras desarrollas, descomenta la línea de abajo:
            // $mensajeError = 'Error al guardar: ' . $e->getMessage();
            $mensajeError = 'Ocurrió un error al guardar los cambios.';
        }

        if ($ok) {
            // actualizamos el nombre de la sesión para el navbar / dashboard
            $_SESSION['nombre'] = $nombre;

            // Redirigimos al dashboard con un indicador de "perfil actualizado"
            header('Location: dashboard.php?perfil=1');
            exit;
        }
    }
}

// 4. Obtener datos actuales del usuario para mostrarlos en el formulario
try {
    $sqlDatos = 'SELECT nombre, email, telefono 
                FROM usuarios 
                WHERE id_usuario = :id';

    $stmtDatos = $pdo->prepare($sqlDatos);
    $stmtDatos->execute([':id' => $idUsuario]);
    $usuario = $stmtDatos->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $usuario = false;
    $mensajeError = 'Ocurrió un error al cargar tus datos.';
    // Si quieres ver el error exacto:
    // $mensajeError = 'Error al cargar datos: ' . $e->getMessage();
}

// Si por algún motivo no encuentra el usuario, cerramos sesión
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Para el "avatar", tomamos la primera letra del nombre (sin mb_*)
$nombreUsuario = $usuario['nombre'] ?? 'Usuario';
$inicial       = strtoupper(substr($nombreUsuario, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil - AdoptaConAmor</title>
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

<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Panel AdoptaConAmor</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
            <a class="nav-link active" href="perfil.php">
                <i class="bi bi-person-circle me-1"></i>Mi perfil
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
            </a>
            </li>
        </ul>
        </div>
    </div>
</nav>

<!-- CONTENIDO -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <!-- Cabecera de perfil con avatar y datos básicos -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                            style="width:64px; height:64px; font-size:1.8rem;">
                            <?php echo htmlspecialchars($inicial); ?>
                        </div>
                        <div>
                            <h1 class="h5 mb-1">
                                <?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>
                            </h1>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-envelope me-1"></i>
                                <?php echo htmlspecialchars($usuario['email'] ?? ''); ?>
                            </p>
                            <?php if (!empty($usuario['telefono'])): ?>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-telephone me-1"></i>
                                    <?php echo htmlspecialchars($usuario['telefono']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <h2 class="h6 text-uppercase text-muted mb-3">
                        Datos personales
                    </h2>

                    <?php if ($mensajeError): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($mensajeError); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="perfil.php" novalidate>
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

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Volver al panel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Guardar cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <p class="text-center mt-3 text-muted small">
                Estás conectado como
                <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>
            </p>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
