<?php
// BACKEND/Privada/solicitud_editar.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../DATABASE/conexion.php';

$nombreSesion = $_SESSION['nombre'] ?? 'Administrador';
$idSolicitud = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idSolicitud) {
    header('Location: solicitudes_listar.php?err=sin_id');
    exit;
}

$error = null;
$solicitud = null;

try {
    $sql = "SELECT 
                s.*,
                a.nombre AS nombre_animal
            FROM solicitudes_adopcion s
            JOIN animales a ON a.id_animal = s.id_animal
            WHERE s.id_solicitud = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idSolicitud]);
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud) {
        header('Location: solicitudes_listar.php?err=no_encontrada');
        exit;
    }
} catch (PDOException $e) {
    $error = 'Error al cargar la solicitud: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $nombre   = trim($_POST['nombre_solicitante']  ?? '');
    $correo   = trim($_POST['correo_solicitante']  ?? '');
    $telefono = trim($_POST['telefono']            ?? '');
    $mensaje  = trim($_POST['mensaje']             ?? '');

    if ($nombre === '' || $correo === '' || $telefono === '' || $mensaje === '') {
        $error = 'Por favor completa todos los campos obligatorios.';
        $solicitud['nombre_solicitante'] = $nombre;
        $solicitud['correo_solicitante'] = $correo;
        $solicitud['telefono']           = $telefono;
        $solicitud['mensaje']            = $mensaje;
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no tiene un formato válido.';

        $solicitud['nombre_solicitante'] = $nombre;
        $solicitud['correo_solicitante'] = $correo;
        $solicitud['telefono']           = $telefono;
        $solicitud['mensaje']            = $mensaje;
    } else {
        try {
            $sql = "UPDATE solicitudes_adopcion
                    SET nombre_solicitante  = :nombre,
                        correo_solicitante  = :correo,
                        telefono            = :telefono,
                        mensaje             = :mensaje
                    WHERE id_solicitud      = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre'   => $nombre,
                ':correo'   => $correo,
                ':telefono' => $telefono,
                ':mensaje'  => $mensaje,
                ':id'       => $idSolicitud,
            ]);
            header('Location: solicitudes_listar.php?msg=datos_actualizados');
            exit;
        } catch (PDOException $e) {
            $error = 'Error al guardar los cambios: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar solicitud #<?= htmlspecialchars($idSolicitud) ?> - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/dashboard.css">
</head>
<body class="dashboard-bg d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar dashboard-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
      <span class="logo-pill">AC</span>
      <span>Panel administrador</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="animales_listar.php" class="nav-link">Animales</a></li>
        <li class="nav-item"><a href="solicitudes_listar.php" class="nav-link active">Solicitudes</a></li>
        <li class="nav-item"><a href="usuarios_admin.php" class="nav-link">Usuarios</a></li>
      </ul>
      <div class="d-flex align-items-center">
        <span class="navbar-text me-3">
          Hola, <?= htmlspecialchars($nombreSesion, ENT_QUOTES, 'UTF-8') ?>
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>
<main class="flex-grow-1">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-1">Editar solicitud #<?= (int)$idSolicitud ?></h1>
        <p class="text-muted mb-0">
          Mascota: #<?= (int)$solicitud['id_animal'] ?> ·
          <?= htmlspecialchars($solicitud['nombre_animal'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>
      <a href="solicitudes_listar.php" class="btn btn-sm btn-secondary">
        ← Volver a solicitudes
      </a>
    </div>
    <?php if ($error): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <form method="post" novalidate>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Fecha de solicitud</label>
              <input type="text" class="form-control form-control-sm"
                     value="<?= htmlspecialchars($solicitud['fecha_solicitud'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     disabled>
            </div>
            <div class="col-md-8">
              <label class="form-label">Estado actual</label>
              <input type="text" class="form-control form-control-sm"
                     value="ID estado: <?= (int)($solicitud['id_estado_solicitud'] ?? 0) ?>"
                     disabled>
              <div class="form-text">
                El cambio de estado se realiza desde la tabla de <strong>Solicitudes</strong>.
              </div>
            </div>
            <div class="col-md-6">
              <label for="nombre_solicitante" class="form-label">Nombre del solicitante *</label>
              <input type="text"
                     class="form-control"
                     id="nombre_solicitante"
                     name="nombre_solicitante"
                     required
                     value="<?= htmlspecialchars($solicitud['nombre_solicitante'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
              <label for="correo_solicitante" class="form-label">Correo electrónico *</label>
              <input type="email"
                     class="form-control"
                     id="correo_solicitante"
                     name="correo_solicitante"
                     required
                     value="<?= htmlspecialchars($solicitud['correo_solicitante'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono de contacto *</label>
              <input type="text"
                     class="form-control"
                     id="telefono"
                     name="telefono"
                     required
                     value="<?= htmlspecialchars($solicitud['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-12">
              <label for="mensaje" class="form-label">
                Mensaje del solicitante / Notas *
              </label>
              <textarea
                class="form-control"
                id="mensaje"
                name="mensaje"
                rows="4"
                required
              ><?= htmlspecialchars($solicitud['mensaje'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="solicitudes_listar.php" class="btn btn-outline-secondary">
              Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
              Guardar cambios
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>
<footer class="text-white text-center py-3 mt-auto site-footer">
  <div class="container">
    <small>&copy; <?= date('Y') ?> AdoptaConAmor · Panel administrador</small>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>