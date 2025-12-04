<?php
// BACKEND/Privada/usuarios_admin.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../DATABASE/conexion.php';

$idRolSesion = (int)($_SESSION['id_rol'] ?? 0);
$rolesAdmin = [1, 2, 3, 4, 7, 8, 9, 10]; // mismos que usamos en login

if (!in_array($idRolSesion, $rolesAdmin, true)) {
    header('Location: panel_usuario.php');
    exit;
}

$nombreSesion = $_SESSION['nombre'] ?? 'Administrador';
$rolesDisponibles = [
    1  => 'Administrador',
    2  => 'Coordinador de refugio',
    3  => 'Voluntario',
    4  => 'Veterinario',
    5  => 'Adoptante',
    6  => 'Visitante',
    7  => 'Moderador',
    8  => 'Recepción',
    9  => 'Soporte técnico',
    10 => 'Superadmin',
];

function rolTexto($idRol, $map) {
    $idRol = (int)$idRol;
    return $map[$idRol] ?? ('Rol #' . $idRol);
}

$msg  = $_GET['msg']  ?? null;
$err  = $_GET['err']  ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'crear') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $rol      = (int)($_POST['id_rol'] ?? 5);
            $activo   = isset($_POST['activo']) ? 1 : 0;

            if ($nombre === '' || $email === '' || $password === '') {
                header('Location: usuarios_admin.php?err=campos');
                exit;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, email, password_hash, id_rol, activo)
                    VALUES (:nombre, :email, :hash, :id_rol, :activo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':email'  => $email,
                ':hash'   => $hash,
                ':id_rol' => $rol,
                ':activo' => $activo,
            ]);
            header('Location: usuarios_admin.php?msg=creado');
            exit;

        } elseif ($accion === 'actualizar') {
            $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
            $nombre    = trim($_POST['nombre'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $rol       = (int)($_POST['id_rol'] ?? 5);
            $activo    = isset($_POST['activo']) ? 1 : 0;
            $newPass   = $_POST['new_password'] ?? '';

            if (!$idUsuario || $nombre === '' || $email === '') {
                header('Location: usuarios_admin.php?err=campos');
                exit;
            }

            if ($idUsuario == $_SESSION['id_usuario'] && !in_array($rol, $rolesAdmin, true)) {
                header('Location: usuarios_admin.php?err=rol_propio');
                exit;
            }

            if ($newPass !== '') {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios
                        SET nombre = :nombre,
                            email  = :email,
                            id_rol = :id_rol,
                            activo = :activo,
                            password_hash = :hash
                        WHERE id_usuario = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email'  => $email,
                    ':id_rol' => $rol,
                    ':activo' => $activo,
                    ':hash'   => $hash,
                    ':id'     => $idUsuario,
                ]);
            } else {
                $sql = "UPDATE usuarios
                        SET nombre = :nombre,
                            email  = :email,
                            id_rol = :id_rol,
                            activo = :activo
                        WHERE id_usuario = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email'  => $email,
                    ':id_rol' => $rol,
                    ':activo' => $activo,
                    ':id'     => $idUsuario,
                ]);
            }
            header('Location: usuarios_admin.php?msg=actualizado');
            exit;
        } elseif ($accion === 'eliminar') {
            $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
            if (!$idUsuario) {
                header('Location: usuarios_admin.php?err=sin_id');
                exit;
            }
            if ($idUsuario == $_SESSION['id_usuario']) {
                header('Location: usuarios_admin.php?err=no_self');
                exit;
            }

            $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $idUsuario]);

            header('Location: usuarios_admin.php?msg=eliminado');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: usuarios_admin.php?err=bd');
        exit;
    }
}

$usuarios = [];
try {
    $sql = "SELECT id_usuario, nombre, email, id_rol, activo, fecha_registro
            FROM usuarios
            ORDER BY fecha_registro DESC, id_usuario DESC";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $err = 'Error al obtener usuarios: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de usuarios - AdoptaConAmor</title>
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
        <li class="nav-item"><a href="solicitudes.php" class="nav-link">Solicitudes</a></li>
        <li class="nav-item"><a href="usuarios_admin.php" class="nav-link active">Usuarios</a></li>
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
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-1">Gestión de usuarios</h1>
        <p class="text-muted mb-0">
          Administra cuentas, roles y estado de acceso al panel.
        </p>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="dashboard.php" class="btn btn-sm btn-secondary">
          ← Volver al panel
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevo">
          + Nuevo usuario
        </button>
      </div>
    </div>
    <?php if ($msg): ?>
      <?php if ($msg === 'creado'): ?>
        <div class="alert alert-success">Usuario creado correctamente.</div>
      <?php elseif ($msg === 'actualizado'): ?>
        <div class="alert alert-success">Usuario actualizado correctamente.</div>
      <?php elseif ($msg === 'eliminado'): ?>
        <div class="alert alert-success">Usuario eliminado correctamente.</div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($err): ?>
      <div class="alert alert-danger">
        <?php
        switch ($err) {
          case 'campos':
            echo 'Por favor completa los campos obligatorios.';
            break;
          case 'no_self':
            echo 'No puedes eliminar tu propio usuario mientras estás logueado.';
            break;
          case 'rol_propio':
            echo 'No puedes quitarte a ti mismo todos los permisos de administrador desde aquí.';
            break;
          case 'bd':
            echo 'Ocurrió un error al guardar en la base de datos.';
            break;
          default:
            echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8');
        }
        ?>
      </div>
    <?php endif; ?>
    <!-- Tabla -->
    <?php if (empty($usuarios)): ?>
      <div class="alert alert-info">No hay usuarios registrados aún.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle bg-white rounded-3 overflow-hidden">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Registro</th>
              <th style="width: 150px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($usuarios as $u): ?>
            <?php
              $idU    = (int)$u['id_usuario'];
              $rolT   = rolTexto($u['id_rol'], $rolesDisponibles);
              $activo = (int)$u['activo'] === 1;
            ?>
            <tr>
              <td><?= $idU ?></td>
              <td><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($rolT, ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <?php if ($activo): ?>
                  <span class="badge bg-success">Activo</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Inactivo</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($u['fecha_registro'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <button
                    type="button"
                    class="btn btn-warning btn-editar"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?= $idU ?>"
                    data-nombre="<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>"
                    data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                    data-rol="<?= (int)$u['id_rol'] ?>"
                    data-activo="<?= $activo ? 1 : 0 ?>"
                  >
                    Editar
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger btn-eliminar"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEliminar"
                    data-id="<?= $idU ?>"
                    data-nombre="<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>"
                  >
                    Eliminar
                  </button>
                </div>
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
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="accion" value="crear">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNuevoLabel">Nuevo usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre completo *</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Correo electrónico *</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña *</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol *</label>
            <select name="id_rol" class="form-select" required>
              <?php foreach ($rolesDisponibles as $idRol => $txtRol): ?>
                <option value="<?= $idRol ?>"><?= htmlspecialchars($txtRol, ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="activo" id="nuevoActivo" checked>
            <label class="form-check-label" for="nuevoActivo">
              Usuario activo (puede iniciar sesión)
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="id_usuario" id="edit_id_usuario">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarLabel">Editar usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre completo *</label>
            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Correo electrónico *</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rol *</label>
            <select name="id_rol" id="edit_id_rol" class="form-select" required>
              <?php foreach ($rolesDisponibles as $idRol => $txtRol): ?>
                <option value="<?= $idRol ?>"><?= htmlspecialchars($txtRol, ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">
              Nueva contraseña (opcional)
            </label>
            <input type="password" name="new_password" class="form-control"
                   placeholder="Deja en blanco para no cambiarla">
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="activo" id="edit_activo">
            <label class="form-check-label" for="edit_activo">
              Usuario activo (puede iniciar sesión)
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id_usuario" id="delete_id_usuario">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEliminarLabel">Eliminar usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p>¿Seguro que deseas eliminar al usuario <strong id="delete_nombre_usuario"></strong>?</p>
          <p class="text-muted small mb-0">
            Esta acción no se puede deshacer. Si solo quieres desactivar su acceso,
            edita el usuario y desmarca la opción <strong>Usuario activo</strong>.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('click', function (ev) {
  const btn = ev.target.closest('.btn-editar');
  if (!btn) return;

  const id     = btn.dataset.id;
  const nombre = btn.dataset.nombre || '';
  const email  = btn.dataset.email || '';
  const rol    = btn.dataset.rol || '';
  const activo = btn.dataset.activo === '1';

  document.getElementById('edit_id_usuario').value = id;
  document.getElementById('edit_nombre').value     = nombre;
  document.getElementById('edit_email').value      = email;
  document.getElementById('edit_id_rol').value     = rol;
  document.getElementById('edit_activo').checked   = activo;
});

document.addEventListener('click', function (ev) {
  const btn = ev.target.closest('.btn-eliminar');
  if (!btn) return;

  const id     = btn.dataset.id;
  const nombre = btn.dataset.nombre || '';

  document.getElementById('delete_id_usuario').value = id;
  document.getElementById('delete_nombre_usuario').textContent = nombre;
});
</script>
</body>
</html>
