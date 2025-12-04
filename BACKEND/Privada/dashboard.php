<?php
// BACKEND/Privada/dashboard.php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$nombre = $_SESSION['nombre'] ?? 'Usuario';
require_once __DIR__ . '/../DATABASE/conexion.php';

$totalAnimales = 0;
$totalUsuarios = 0;
try {
    $stmt = $pdo->query('SELECT COUNT(*) AS total FROM animales');
    $totalAnimales = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
}
try {
    $stmt = $pdo->query('SELECT COUNT(*) AS total FROM usuarios');
    $totalUsuarios = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
}
$perfilActualizado = isset($_GET['perfil']) && $_GET['perfil'] === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/dashboard.css">
</head>
<body class="d-flex flex-column min-vh-100 dashboard-bg">
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar dashboard-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
      <span class="logo-pill">AC</span>
      <span>Panel AdoptaConAmor</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="ms-auto d-flex align-items-center">
        <span class="navbar-text me-3">
          Hola, <?php echo htmlspecialchars($nombre); ?>
        </span>
        <a href="/BACKEND/Privada/perfil.php" class="btn btn-outline-light btn-sm me-2">
          Mi perfil
        </a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">
          Cerrar sesión
        </a>
      </div>
    </div>
  </div>
</nav>
<?php if ($perfilActualizado): ?>
  <div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      Tu perfil se actualizó correctamente.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  </div>
<?php endif; ?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 dashboard-header-card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h4 mb-1">
                            Hola, <?php echo htmlspecialchars($nombre); ?> 👋
                        </h1>
                        <p class="mb-0 text-muted">
                            Desde este panel podrás gestionar los animales en adopción, las solicitudes
                            y los usuarios del sistema.
                        </p>
                    </div>
                    <div class="mt-3 mt-md-0 text-md-end">
                        <span class="badge bg-success mb-1">
                            Sesión activa
                        </span>
                        <p class="mb-0 small text-muted">
                            Hoy es <?php echo date('d/m/Y'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-heart-pulse fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Animales registrados</h5>
                        <p class="display-6 mb-0">
                            <?php echo $totalAnimales; ?>
                        </p>
                        <small class="text-muted">Total de animales cargados en el sistema.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-people fs-4 text-info"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Usuarios registrados</h5>
                        <p class="display-6 mb-0">
                            <?php echo $totalUsuarios; ?>
                        </p>
                        <small class="text-muted">Personas con acceso al panel.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-chat-left-text fs-4 text-warning"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Solicitudes de adopción</h5>
                        <p class="display-6 mb-0">
                            0
                        </p>
                        <small class="text-muted">
                            Aquí podrás ver las solicitudes pendientes (módulo en construcción).
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-12">
            <h2 class="h5 mb-3">Secciones del panel</h2>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-section-card">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Animales</h5>
                    <p class="card-text flex-grow-1">
                        Registro y gestión de animales en adopción (altas, bajas, cambios, fotos, estado).
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">Módulo activo</span>
                        <a href="animales_listar.php" class="btn btn-primary btn-sm">Entrar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-section-card">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Solicitudes de adopción</h5>
                    <p class="card-text flex-grow-1">
                        Revisión básica de solicitudes enviadas desde la parte pública
                        (módulo en versión simple para el proyecto).
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-warning text-dark">Versión básica</span>
                        <a href="solicitudes.php" class="btn btn-primary btn-sm">Entrar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-section-card">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text flex-grow-1">
                        Administración básica de los usuarios que pueden entrar al panel.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-warning text-dark">Versión básica</span>
                        <a href="usuarios_admin.php" class="btn btn-primary btn-sm">Entrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
