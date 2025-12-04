<?php
// BACKEND/Privada/usuarios_listar.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$rolSesion    = $_SESSION['rol']    ?? 'usuario';
$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';

// Solo admins pueden ver esta lista
if ($rolSesion !== 'admin') {
    header('Location: panel_usuario.php');
    exit;
}

require_once __DIR__ . '/../DATABASE/conexion.php';

$usuarios = [];
$error = null;

try {
    $stmt = $pdo->query("
        SELECT id_usuario, nombre, correo, rol
        FROM usuarios
        ORDER BY id_usuario DESC
    ");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener usuarios: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/dashboard.css">
</head>
<body class="dashboard-bg d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar dashboard-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
      <span class="logo-pill">AC</span>
      <span>Panel AdoptaConAmor</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <span class="navbar-text me-3">
        Hola, <?php echo htmlspecialchars($nombreSesion, ENT_QUOTES, 'UTF-8'); ?>
        <span class="badge bg-light text-dark ms-2">Admin</span>
      </span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">
        Cerrar sesión
      </a>
    </div>
  </div>
</nav>

<main class="flex-grow-1">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Usuarios del panel</h1>
        <a href="dashboard.php" class="btn btn-sm btn-secondary">
            ← Volver al panel
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($usuarios)): ?>
        <div class="alert alert-info">
            No hay usuarios registrados aún.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?php echo (int)$u['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($u['rol'] ?? 'usuario', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
