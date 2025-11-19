<?php
// FRONTEND/.../animales.php

require_once __DIR__ . '/../../BACKEND/DATABASE/conexion.php';

$animales     = [];
$errorAnimales = '';

try {
    // Traemos datos de animales + tipo + tamaño + foto principal
    $sql = "
        SELECT 
            a.id_animal,
            a.nombre,
            ta.nombre   AS especie,
            tm.nombre   AS tamanio,
            a.edad_anios,
            a.descripcion,
            f.url       AS foto_url
        FROM animales a
        JOIN tipos_animal ta ON a.id_tipo   = ta.id_tipo
        JOIN tamanos      tm ON a.id_tamano = tm.id_tamano
        LEFT JOIN fotos_animal f 
            ON f.id_animal = a.id_animal AND f.es_principal = 1
        WHERE a.visible = 1
          AND a.adoptado = 0
        ORDER BY a.fecha_registro DESC
    ";

    $stmt = $pdo->query($sql);
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorAnimales = 'Error al cargar los animales: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Animales en adopción - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css" />
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<?php /* NAVBAR REUTILIZADA */ ?>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <span class="logo-pill">AC</span> AdoptaConAmor
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="../../index.html">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="animales.php">Animales</a></li>
        <li class="nav-item"><a class="nav-link" href="como-adoptar.php">Cómo adoptar</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
        <li class="nav-item ms-lg-3">
          <a class="btn btn-outline-light btn-sm" href="/BACKEND/Privada/login.php">
            Iniciar sesión
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main class="flex-grow-1">

  <section class="py-5">
    <div class="container">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h2 fw-bold mb-1">Animales disponibles en adopción</h1>
          <p class="text-muted mb-0">
            Explora la lista de animales rescatados y elige a tu nuevo compañero.
          </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-outline-secondary btn-sm" onclick="cargarAnimales()">
            Actualizar lista
          </button>
          <a href="/BACKEND/Privada/login.php" class="btn btn-primary btn-sm">
            Panel del refugio
          </a>
        </div>
      </div>

      <!-- Filtros -->
      <ul class="nav nav-pills small mb-4" id="filtros-animales">
        <li class="nav-item">
          <button class="nav-link active" data-filter="todos" type="button">Todos</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-filter="perro" type="button">Perros</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-filter="gato" type="button">Gatos</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-filter="otros" type="button">Otros</button>
        </li>
      </ul>

      <!-- Lista dinámica de animales -->
      <div id="lista-animales" class="row g-4">

        <?php if ($errorAnimales): ?>
          <div class="col-12">
            <div class="alert alert-danger">
              <?php echo htmlspecialchars($errorAnimales); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!$errorAnimales && empty($animales)): ?>
          <div class="col-12">
            <div class="alert alert-info">
              Por ahora no hay animales disponibles en adopción.
            </div>
          </div>
        <?php endif; ?>

        <?php foreach ($animales as $animal): ?>
          <?php
            $nombre      = $animal['nombre']        ?? 'Sin nombre';
            $especieRaw  = $animal['especie']       ?? 'Otros';   // viene de tipos_animal.nombre
            $tamanio     = $animal['tamanio']       ?? '';        // viene de tamanos.nombre
            $edad        = $animal['edad_anios']    ?? '';
            $desc        = $animal['descripcion']   ?? '';
            $foto        = $animal['foto_url']      ?? '';

            if (empty($foto)) {
                $foto = 'https://via.placeholder.com/600x400?text=En+adopcion';
            }

            // Para los filtros: solo perro / gato / otros
            $especieLower = strtolower($especieRaw);
            if ($especieLower === 'perro' || $especieLower === 'perros') {
                $especieFiltro = 'perro';
            } elseif ($especieLower === 'gato' || $especieLower === 'gatos') {
                $especieFiltro = 'gato';
            } else {
                $especieFiltro = 'otros';
            }

            // Subtítulo tipo: "Perro · Mediano · 2 años"
            $partesSub = [];
            if (!empty($especieRaw)) $partesSub[] = $especieRaw;
            if (!empty($tamanio))    $partesSub[] = $tamanio;
            if ($edad !== '' && $edad !== null) $partesSub[] = $edad . ' años';
            $subtitulo = implode(' · ', $partesSub);
          ?>

          <div class="col-md-4" data-especie="<?php echo htmlspecialchars($especieFiltro); ?>">
            <div class="card h-100 shadow-sm border-0 animal-card">
              <img src="<?php echo htmlspecialchars($foto); ?>"
                   class="card-img-top"
                   alt="Foto de <?php echo htmlspecialchars($nombre); ?>">
              <div class="card-body">
                <h5 class="card-title mb-1">
                  <?php echo htmlspecialchars($nombre); ?>
                </h5>

                <?php if ($subtitulo): ?>
                  <p class="text-muted small mb-2">
                    <?php echo htmlspecialchars($subtitulo); ?>
                  </p>
                <?php endif; ?>

                <?php if (!empty($desc)): ?>
                  <p class="card-text small">
                    <?php echo nl2br(htmlspecialchars($desc)); ?>
                  </p>
                <?php endif; ?>

                <button class="btn btn-outline-primary btn-sm">
                  Más información
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </section>

</main>

<footer class="text-white text-center py-3 mt-auto site-footer">
  <div class="container">
    <small>&copy; <?php echo date('Y'); ?> AdoptaConAmor · Proyecto final</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Botón "Actualizar lista"
function cargarAnimales() {
  location.reload();
}

// Filtros simples por data-especie
document.addEventListener('DOMContentLoaded', () => {
  const botones = document.querySelectorAll('#filtros-animales .nav-link');
  const cards   = document.querySelectorAll('#lista-animales [data-especie]');

  botones.forEach(btn => {
    btn.addEventListener('click', () => {
      const filtro = btn.getAttribute('data-filter');

      botones.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      cards.forEach(card => {
        const especie = card.getAttribute('data-especie');

        if (filtro === 'todos' || especie === filtro) {
          card.classList.remove('d-none');
        } else {
          card.classList.add('d-none');
        }
      });
    });
  });
});
</script>
<script src="/FRONTEND/JS/app.js"></script>
</body>
</html>
