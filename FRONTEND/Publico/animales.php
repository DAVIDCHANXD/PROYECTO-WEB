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
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
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

      <!-- Filtros (tú luego los conectas con JS o PHP) -->
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

      <!-- Aquí tu JS / PHP puede pintar las cards dinámicamente -->
      <div id="lista-animales" class="row g-4">
        <!-- Ejemplos estáticos, puedes borrarlos cuando conectes la BD -->
        <div class="col-md-4" data-especie="perro">
          <div class="card h-100 shadow-sm border-0 animal-card">
            <img src="https://images.pexels.com/photos/59523/pexels-photo-59523.jpeg"
                 class="card-img-top" alt="Perro">
            <div class="card-body">
              <h5 class="card-title mb-1">Rocky</h5>
              <p class="text-muted small mb-2">Perro · Mediano · 2 años</p>
              <p class="card-text small">
                Juguetón y sociable, ideal para familias con niños.
              </p>
              <button class="btn btn-outline-primary btn-sm">
                Más información
              </button>
            </div>
          </div>
        </div>

        <div class="col-md-4" data-especie="gato">
          <div class="card h-100 shadow-sm border-0 animal-card">
            <img src="https://images.pexels.com/photos/20787/pexels-photo.jpg"
                 class="card-img-top" alt="Gato">
            <div class="card-body">
              <h5 class="card-title mb-1">Luna</h5>
              <p class="text-muted small mb-2">Gato · Pequeño · 1 año</p>
              <p class="card-text small">
                Tranquila, cariñosa y se adapta bien a espacios pequeños.
              </p>
              <button class="btn btn-outline-primary btn-sm">
                Más información
              </button>
            </div>
          </div>
        </div>

        <div class="col-md-4" data-especie="otros">
          <div class="card h-100 shadow-sm border-0 animal-card">
            <img src="https://images.pexels.com/photos/52500/horse-herd-fog-nature-52500.jpeg"
                 class="card-img-top" alt="Otro">
            <div class="card-body">
              <h5 class="card-title mb-1">Max</h5>
              <p class="text-muted small mb-2">Otro · Grande · 3 años</p>
              <p class="card-text small">
                Animal de granja rescatado, requiere espacio amplio.
              </p>
              <button class="btn btn-outline-primary btn-sm">
                Más información
              </button>
            </div>
          </div>
        </div>

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
<script src="/FRONTEND/JS/app.js"></script>
</body>
</html>
