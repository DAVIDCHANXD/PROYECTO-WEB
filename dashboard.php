<?php
// dashboard.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$nombre = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Panel AdoptaConAmor</a>
    <div class="ms-auto">
      <span class="navbar-text me-3">
        Hola, <?php echo htmlspecialchars($nombre); ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Animales</h5>
                    <p class="card-text">
                        Aquí podrás gestionar el registro de animales (alta, baja, cambios).
                    </p>
                    <a href="#" class="btn btn-primary btn-sm disabled">Próximamente</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Solicitudes de adopción</h5>
                    <p class="card-text">
                        Revisa y responde las solicitudes enviadas por los adoptantes.
                    </p>
                    <a href="#" class="btn btn-primary btn-sm disabled">Próximamente</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">
                        Administración básica de usuarios y roles del sistema.
                    </p>
                    <a href="#" class="btn btn-primary btn-sm disabled">Próximamente</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
