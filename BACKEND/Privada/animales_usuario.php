<?php
// BACKEND/Privada/animales_usuario.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';

require_once __DIR__ . '/../DATABASE/conexion.php';

$animales = [];
$error = null;

try {
    $sql = "SELECT 
                a.id_animal,
                a.nombre,
                a.id_tipo,
                a.id_tamano,
                a.edad_anios,
                a.sexo,
                a.id_estado_salud,
                a.descripcion,
                a.adoptado,
                a.visible,
                f.url AS foto_url
            FROM animales a
            LEFT JOIN fotos_animal f
                ON f.id_animal = a.id_animal
               AND f.es_principal = 1
            WHERE a.visible = 1
            ORDER BY a.fecha_registro DESC, a.id_animal DESC";
    $stmt = $pdo->query($sql);
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error al obtener animales: ' . $e->getMessage();
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

function tamanoTexto($idTamano) {
    switch ((int)$idTamano) {
        case 1: return 'Muy pequeño';
        case 2: return 'Pequeño';
        case 3: return 'Mediano';
        case 4: return 'Grande';
        default: return 'Otro';
    }
}

function estadoSaludTexto($idEstado) {
    switch ((int)$idEstado) {
        case 1: return 'Saludable';
        case 2: return 'Vacunas en proceso';
        case 3: return 'En recuperación';
        case 4: return 'Tratamiento especial';
        case 5: return 'Condición crónica';
        case 6: return 'Observación';
        case 7: return 'Revisar';
        default: return 'Revisar';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de animales - Mi cuenta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/dashboard.css">
</head>
<body class="animales-bg d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar dashboard-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/index.php">
      <span class="logo-pill">AC</span>
      <span>AdoptaConAmor</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a href="/BACKEND/Privada/panel_usuario.php" class="nav-link">Mi panel</a>
        </li>
        <li class="nav-item">
          <a href="/BACKEND/Privada/animales_usuario.php" class="nav-link active">Catálogo de animales</a>
        </li>
      </ul>
      <div class="d-flex align-items-center">
        <span class="navbar-text me-3">
          Hola, <?= htmlspecialchars($nombreSesion) ?>
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>

<main class="flex-grow-1 animales-main">

  <!-- HERO DE ANIMALES (PRIVADO) -->
  <section class="animales-hero">
    <div class="container">
      <h1 class="mb-2">Catálogo de animales</h1>
      <p class="mb-0">
        Desde aquí puedes ver todas las mascotas disponibles.  
        Usa el botón <strong>Quiero adoptar</strong> para regresar a tu panel con esa mascota seleccionada.
      </p>
    </div>
  </section>

  <!-- LISTA DE TARJETAS -->
  <section class="py-4">
    <div class="container">

      <?php if ($error): ?>
        <div class="alert alert-danger">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if (empty($animales)): ?>
        <div class="alert alert-info">
          Por el momento no hay animales disponibles para adopción.
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($animales as $a): ?>
            <?php
              $foto     = $a['foto_url'] ?: '/FRONTEND/IMG/sin-foto.png';
              $adoptado = (int)$a['adoptado'] === 1;
            ?>
            <div class="col-md-4">
              <div class="card h-100 animal-card">
                <img src="<?= htmlspecialchars($foto) ?>"
                     class="card-img-top"
                     alt="Foto de <?= htmlspecialchars($a['nombre']) ?>"
                     style="height:220px;object-fit:cover;">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title mb-1"><?= htmlspecialchars($a['nombre']) ?></h5>
                  <p class="card-text small text-muted mb-2">
                    <?= htmlspecialchars(tipoTexto($a['id_tipo'])) ?> ·
                    <?= htmlspecialchars(tamanoTexto($a['id_tamano'])) ?> ·
                    <?= (int)$a['edad_anios'] ?> años
                  </p>

                  <?php if ($adoptado): ?>
                    <span class="badge bg-secondary mb-2">Ya adoptado</span>
                  <?php else: ?>
                    <span class="badge bg-success mb-2">Disponible</span>
                  <?php endif; ?>

                  <p class="card-text small flex-grow-1">
                    <?= htmlspecialchars($a['descripcion'] ?: 'Sin descripción adicional.') ?>
                  </p>

                  <div class="mt-auto d-flex gap-2">
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-sm btn-mas-info"
                      data-bs-toggle="modal"
                      data-bs-target="#modalAnimal"
                      data-id="<?= (int)$a['id_animal'] ?>"
                      data-nombre="<?= htmlspecialchars($a['nombre']) ?>"
                      data-tipo="<?= htmlspecialchars(tipoTexto($a['id_tipo'])) ?>"
                      data-tamano="<?= htmlspecialchars(tamanoTexto($a['id_tamano'])) ?>"
                      data-edad="<?= (int)$a['edad_anios'] ?>"
                      data-sexo="<?= htmlspecialchars($a['sexo']) ?>"
                      data-salud="<?= htmlspecialchars(estadoSaludTexto($a['id_estado_salud'])) ?>"
                      data-descripcion="<?= htmlspecialchars($a['descripcion'] ?? '') ?>"
                      data-foto="<?= htmlspecialchars($foto) ?>"
                    >
                      Más información
                    </button>

                    <?php if (!$adoptado): ?>
                      <a href="/BACKEND/Privada/panel_usuario.php?id_animal=<?= (int)$a['id_animal'] ?>"
                         class="btn btn-success btn-sm">
                        Quiero adoptar
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </section>
</main>

<!-- MODAL DETALLE ANIMAL -->
<div class="modal fade" id="modalAnimal" tabindex="-1" aria-labelledby="modalAnimalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAnimalLabel">Detalle de la mascota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-5">
            <img id="modalAnimalImg"
                 src=""
                 alt="Foto animal"
                 class="img-fluid rounded-3"
                 style="max-height:300px;object-fit:cover;">
          </div>
          <div class="col-md-7">
            <h4 id="modalAnimalNombre" class="mb-2"></h4>
            <p class="mb-1"><strong>Tipo:</strong> <span id="modalAnimalTipo"></span></p>
            <p class="mb-1"><strong>Tamaño:</strong> <span id="modalAnimalTamano"></span></p>
            <p class="mb-1"><strong>Edad:</strong> <span id="modalAnimalEdad"></span> años</p>
            <p class="mb-1"><strong>Sexo:</strong> <span id="modalAnimalSexo"></span></p>
            <p class="mb-1"><strong>Estado de salud:</strong> <span id="modalAnimalSalud"></span></p>
            <hr>
            <p class="mb-0" id="modalAnimalDescripcion"></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- Nota: el botón de adoptar solo sirve como recordatorio visual; el flujo principal es la card -->
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<footer class="text-white text-center py-3 mt-auto site-footer">
  <div class="container">
    <small>&copy; <?= date('Y') ?> AdoptaConAmor · Proyecto final</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// JS para llenar el modal con la info del animal
document.addEventListener('click', function (event) {
  const btn = event.target.closest('.btn-mas-info');
  if (!btn) return;

  const nombre = btn.dataset.nombre || '';
  const tipo   = btn.dataset.tipo || '';
  const tamano = btn.dataset.tamano || '';
  const edad   = btn.dataset.edad || '';
  const sexo   = btn.dataset.sexo || '';
  const salud  = btn.dataset.salud || '';
  const desc   = btn.dataset.descripcion || '';
  const foto   = btn.dataset.foto || '';

  document.getElementById('modalAnimalNombre').textContent = nombre;
  document.getElementById('modalAnimalTipo').textContent = tipo;
  document.getElementById('modalAnimalTamano').textContent = tamano;
  document.getElementById('modalAnimalEdad').textContent = edad;
  document.getElementById('modalAnimalSexo').textContent = sexo;
  document.getElementById('modalAnimalSalud').textContent = salud;
  document.getElementById('modalAnimalDescripcion').textContent =
      desc !== '' ? desc : 'Sin descripción adicional.';

  const img = document.getElementById('modalAnimalImg');
  img.src = foto;
  img.alt = 'Foto de ' + nombre;
});
</script>

</body>
</html>
