<?php
// BACKEND/Privada/solicitudes.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../DATABASE/conexion.php';

$idRolSesion   = (int)($_SESSION['id_rol'] ?? 0);
$nombreSesion  = $_SESSION['nombre'] ?? 'Administrador';

// ================= FUNCIONES AUXILIARES =================
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

function estadoBadgeClass($id)
{
    switch ((int)$id) {
        case 1: return 'bg-secondary';
        case 2: return 'bg-warning text-dark';
        case 3: return 'bg-success';
        case 4: return 'bg-danger';
        case 5: return 'bg-dark';
        default: return 'bg-secondary';
    }
}

function resumenMensaje($texto, $max = 160)
{
    if ($texto === null) return '';

    $limpio = preg_replace('/\s+/', ' ', trim($texto));

    if (mb_strlen($limpio) > $max) {
        return mb_substr($limpio, 0, $max - 3) . '...';
    }
    return $limpio;
}

$msg = $_GET['msg'] ?? null;
$err = $_GET['err'] ?? null;

// ================== POST: CAMBIAR ESTADO / ELIMINAR ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'cambiar_estado') {
            $id    = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
            $nuevo = filter_input(INPUT_POST, 'id_estado_solicitud', FILTER_VALIDATE_INT);

            if (!$id || !$nuevo) {
                header('Location: solicitudes.php?err=campos');
                exit;
            }

            $sql = "UPDATE solicitudes_adopcion
                    SET id_estado_solicitud = :estado
                    WHERE id_solicitud = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':estado' => $nuevo,
                ':id'     => $id,
            ]);

            header('Location: solicitudes.php?msg=actualizado');
            exit;

        } elseif ($accion === 'eliminar') {
            $id = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
            if (!$id) {
                header('Location: solicitudes.php?err=sin_id');
                exit;
            }

            $sql = "DELETE FROM solicitudes_adopcion WHERE id_solicitud = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            header('Location: solicitudes.php?msg=eliminado');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: solicitudes.php?err=bd');
        exit;
    }
}

// ================== OBTENER SOLICITUDES ==================
$solicitudes = [];
try {
    $sql = "SELECT 
                s.id_solicitud,
                s.id_animal,
                s.id_usuario,
                s.mensaje,
                s.fecha_solicitud,
                s.id_estado_solicitud,
                a.nombre AS nombre_animal
            FROM solicitudes_adopcion s
            JOIN animales a ON a.id_animal = s.id_animal
            ORDER BY s.fecha_solicitud DESC, s.id_solicitud DESC";
    $stmt = $pdo->query($sql);
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $err = 'Error al obtener solicitudes: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes de adopción - AdoptaConAmor</title>
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
        <li class="nav-item"><a href="solicitudes.php" class="nav-link active">Solicitudes</a></li>
        <li class="nav-item"><a href="usuarios_listar.php" class="nav-link">Usuarios</a></li>
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

    <h1 class="h4 mb-1">Solicitudes de adopción</h1>
    <p class="text-muted mb-3">
      Revisa y actualiza el estado de las solicitudes enviadas por los usuarios.
    </p>

    <?php if ($msg): ?>
      <?php if ($msg === 'actualizado'): ?>
        <div class="alert alert-success">Estado actualizado correctamente.</div>
      <?php elseif ($msg === 'eliminado'): ?>
        <div class="alert alert-success">Solicitud eliminada correctamente.</div>
      <?php elseif ($msg === 'datos_actualizados'): ?>
        <div class="alert alert-success">Datos de la solicitud actualizados correctamente.</div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($err): ?>
      <div class="alert alert-danger">
        <?php
        switch ($err) {
            case 'campos':   echo 'Por favor selecciona un estado válido.'; break;
            case 'sin_id':   echo 'No se recibió el ID de la solicitud.'; break;
            case 'bd':       echo 'Ocurrió un error al guardar en la base de datos.'; break;
            default:         echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8');
        }
        ?>
      </div>
    <?php endif; ?>

    <?php if (empty($solicitudes)): ?>
      <div class="alert alert-info">
        No hay solicitudes registradas por el momento.
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle bg-white rounded-3 overflow-hidden">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Mascota</th>
              <th>Mensaje</th>
              <th>Estado</th>
              <th>Cambiar estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($solicitudes as $s): ?>
            <?php
              $id         = (int)$s['id_solicitud'];
              $msgCorto   = resumenMensaje($s['mensaje']);
              $claseBadge = estadoBadgeClass($s['id_estado_solicitud']);
            ?>
            <tr>
              <td><?= $id ?></td>
              <td><?= htmlspecialchars($s['fecha_solicitud'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                #<?= (int)$s['id_animal'] ?> - 
                <?= htmlspecialchars($s['nombre_animal'], ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td style="max-width: 420px;">
                <span class="d-block">
                  <?= htmlspecialchars($msgCorto, ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td>
                <span class="badge <?= $claseBadge ?>">
                  <?= htmlspecialchars(estadoTexto($s['id_estado_solicitud']), ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td>
                <form method="post" class="d-flex gap-2 align-items-center">
                  <input type="hidden" name="accion" value="cambiar_estado">
                  <input type="hidden" name="id_solicitud" value="<?= $id ?>">

                  <select name="id_estado_solicitud" class="form-select form-select-sm">
                    <option value="1" <?= $s['id_estado_solicitud']==1 ? 'selected' : '' ?>>Pendiente</option>
                    <option value="2" <?= $s['id_estado_solicitud']==2 ? 'selected' : '' ?>>En revisión</option>
                    <option value="3" <?= $s['id_estado_solicitud']==3 ? 'selected' : '' ?>>Aprobada</option>
                    <option value="4" <?= $s['id_estado_solicitud']==4 ? 'selected' : '' ?>>Rechazada</option>
                    <option value="5" <?= $s['id_estado_solicitud']==5 ? 'selected' : '' ?>>Cancelada</option>
                  </select>

                  <button type="submit" class="btn btn-sm btn-primary">
                    Guardar
                  </button>
                </form>
              </td>
              <td class="text-nowrap">
                <form method="post" onsubmit="return confirm('¿Eliminar esta solicitud?');" class="d-inline">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="id_solicitud" value="<?= $id ?>">
                  <button type="submit" class="btn btn-sm btn-danger">
                    Eliminar
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

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
