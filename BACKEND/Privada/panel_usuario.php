<?php
// BACKEND/Privada/panel_usuario.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
$emailSesion  = $_SESSION['email']  ?? '';
$idUsuario    = (int)($_SESSION['id_usuario'] ?? 0);

require_once __DIR__ . '/../DATABASE/conexion.php';

$ok        = isset($_GET['ok']);
$error     = isset($_GET['error']);
$idAnimalSeleccionado = filter_input(INPUT_GET, 'id_animal', FILTER_VALIDATE_INT);

// =======================
// Animales disponibles
// =======================
$animales = [];
try {
    $sqlAnimales = "SELECT id_animal, nombre, id_tipo, edad_anios
                    FROM animales
                    WHERE visible = 1 AND adoptado = 0
                    ORDER BY nombre";
    $stmtA = $pdo->query($sqlAnimales);
    $animales = $stmtA->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Puedes depurar si quieres: var_dump($e->getMessage());
}

// =======================
// Solicitudes del usuario
// =======================
$solicitudes = [];
try {
    $sql = "SELECT 
                s.id_solicitud,
                s.fecha_solicitud,
                s.id_estado_solicitud,
                a.nombre AS nombre_animal
            FROM solicitudes_adopcion s
            JOIN animales a ON a.id_animal = s.id_animal
            WHERE s.id_usuario = :id
            ORDER BY s.fecha_solicitud DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idUsuario]);
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Depuración opcional
}

function estadoTexto($id)
{
    switch ((int)$id) {
        case 1: return 'Pendiente';
        case 2: return 'En revisión';
        case 3: return 'Aprobada';
        case 4: return 'Rechazada';
        case 5: return 'Cancelada';
        default: return 'Desconocido';
    }
}

function tipoTexto($idTipo) {
    switch ((int)$idTipo) {
        case 1: return 'Perro';
        case 2: return 'Gato';
        case 3: return 'Conejo / pequeños mamíferos';
        case 4: return 'Ave';
        case 7: return 'Pez';
        default: return 'Otro';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi panel - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/dashboard.css">
</head>
<body class="dashboard-bg">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar dashboard-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/index.html">
      <span class="logo-pill">AC</span>
      <span>AdoptaConAmor</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <span class="navbar-text me-3">
        Hola, <?= htmlspecialchars($nombreSesion) ?>
      </span>
      <a href="panel_usuario.php" class="btn btn-outline-light btn-sm me-2">Mi panel</a>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container py-4">
    <div class="mb-4">
        <h1 class="h4 mb-1">Mi panel de adopción</h1>
        <p class="text-muted mb-0">
            Desde aquí puedes enviar solicitudes de adopción y revisar el estado de las que ya hiciste.
        </p>
    </div>

    <?php if ($ok): ?>
        <div class="alert alert-success">
            Tu solicitud de adopción se envió correctamente. Un administrador la revisará pronto.
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger">
            Ocurrió un error al enviar tu solicitud. Revisa los datos e inténtalo de nuevo.
        </div>
    <?php endif; ?>

    <!-- Tarjetas rápidas -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 dashboard-section-card">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Ver catálogo de animales</h5>
                    <p class="card-text flex-grow-1">
                        Consulta todos los animales disponibles actualmente solo desde tu cuenta.
                    </p>
                    <a href="/BACKEND/Privada/animales_usuario.php" class="btn btn-primary btn-sm mt-auto">
                        Abrir catálogo
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- FORMULARIO INTERNO + LISTA DE SOLICITUDES -->
    <div class="row g-4">
        <!-- Formulario interna de solicitud -->
        <div class="col-lg-6">
            <div class="card border-0 dashboard-section-card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Nueva solicitud de adopción</h2>
                    <p class="text-muted small">
                        Elige la mascota que te interesa y cuéntanos por qué deseas adoptarla.
                    </p>
                    <?php if (empty($animales)): ?>
                        <div class="alert alert-info">
                            Por el momento no hay animales disponibles para adopción.
                        </div>
                    <?php else: ?>
                        <form method="post" action="solicitud_guardar.php" class="row g-3">
                            <!-- importante: para que al terminar regrese a este panel -->
                            <input type="hidden" name="redirect" value="/BACKEND/Privada/panel_usuario.php">

                            <div class="col-12">
                                <label for="id_animal" class="form-label">Mascota *</label>
                                <select class="form-select" id="id_animal" name="id_animal" required>
                                    <option value="">-- Selecciona una mascota --</option>
                                    <?php foreach ($animales as $a): ?>
                                        <?php
                                            $idA   = (int)$a['id_animal'];
                                            $label = $a['nombre'] . ' (' . tipoTexto($a['id_tipo']) . ', ' . $a['edad_anios'] . ' años)';
                                            $selected = ($idAnimalSeleccionado === $idA) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $idA ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nombre completo *</label>
                                <input type="text"
                                       name="nombre_completo"
                                       class="form-control"
                                       required
                                       value="<?= htmlspecialchars($nombreSesion) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo electrónico *</label>
                                <input type="email"
                                       name="correo"
                                       class="form-control"
                                       required
                                       value="<?= htmlspecialchars($emailSesion) ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Teléfono *</label>
                                <input type="text"
                                       name="telefono"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Mensaje *</label>
                                <textarea name="mensaje"
                                          class="form-control"
                                          rows="4"
                                          required
                                          placeholder="Explica por qué quieres adoptar y cómo cuidarías a la mascota"></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">
                                    Enviar solicitud
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Tabla de solicitudes del usuario -->
        <div class="col-lg-6">
            <div class="card border-0 dashboard-section-card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Mis solicitudes de adopción</h2>

                    <?php if (empty($solicitudes)): ?>
                        <div class="alert alert-info">
                            Aún no has realizado solicitudes de adopción desde tu cuenta.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Mascota</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($solicitudes as $s): ?>
                                    <tr>
                                        <td><?= (int)$s['id_solicitud'] ?></td>
                                        <td><?= htmlspecialchars($s['nombre_animal']) ?></td>
                                        <td><?= htmlspecialchars($s['fecha_solicitud']) ?></td>
                                        <td><?= htmlspecialchars(estadoTexto($s['id_estado_solicitud'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
