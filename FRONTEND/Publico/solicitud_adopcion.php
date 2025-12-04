<?php
// FRONTEND/Publico/solicitud_adopcion.php (PARTE PÚBLICA)
session_start();

require_once __DIR__ . '/../../BACKEND/DATABASE/conexion.php';

$mensajeOk    = isset($_GET['ok']);
$mensajeError = isset($_GET['error']);

// Datos de sesión (por si el usuario está logueado)
$nombreSesion = $_SESSION['nombre'] ?? '';
$emailSesion  = $_SESSION['email']  ?? '';

// Si viene id_animal por GET (desde botón "Adoptar" de la ficha)
$idAnimalSeleccionado = filter_input(INPUT_GET, 'id_animal', FILTER_VALIDATE_INT);

// Obtener animales disponibles para el select y el catálogo
$animales = [];
try {
    $sql = "SELECT 
                a.id_animal,
                a.nombre,
                a.id_tipo,
                a.edad_anios,
                a.sexo,
                a.descripcion,
                f.url AS foto_url
            FROM animales a
            LEFT JOIN fotos_animal f
                ON f.id_animal = a.id_animal
               AND f.es_principal = 1
            WHERE a.visible = 1
              AND a.adoptado = 0
            ORDER BY a.nombre";
    $stmt = $pdo->query($sql);
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Podrías mostrar un mensaje si quieres
    // $error = 'Error al obtener animales: ' . $e->getMessage();
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
    <title>Solicitud de adopción - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/index.php">
      <span class="logo-pill">AC</span> AdoptaConAmor
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a href="/index.html" class="nav-link">Inicio</a></li>
        <li class="nav-item"><a href="/FRONTEND/Publico/animales.php" class="nav-link">Animales</a></li>
        <li class="nav-item"><a href="/FRONTEND/Publico/como-adoptar.php" class="nav-link">Cómo adoptar</a></li>
        <li class="nav-item"><a href="/FRONTEND/Publico/solicitud_adopcion.php" class="nav-link active">Solicitud de adopción</a></li>
        <li class="nav-item"><a href="/FRONTEND/Publico/contacto.php" class="nav-link">Contacto</a></li>
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
<div class="container py-4">
    <h1 class="mb-3">Solicitud de adopción</h1>
    <p class="text-muted">
        Completa el siguiente formulario para solicitar la adopción de una mascota.
        Un administrador revisará tu solicitud y se pondrá en contacto contigo.
    </p>

    <?php if ($mensajeOk): ?>
        <div class="alert alert-success">
            ¡Gracias! Tu solicitud se ha enviado correctamente. Pronto nos pondremos en contacto contigo.
        </div>
    <?php elseif ($mensajeError): ?>
        <div class="alert alert-danger">
            Ocurrió un error al enviar tu solicitud. Inténtalo más tarde.
        </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <form method="post" action="/BACKEND/Privada/solicitud_guardar.php" class="mt-3">
        <!-- Para que solicitud_guardar.php regrese a esta misma página -->
        <input type="hidden" name="redirect" value="/FRONTEND/Publico/solicitud_adopcion.php">

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre_completo" class="form-label">Nombre completo *</label>
                <input type="text" class="form-control" id="nombre_completo"
                       name="nombre_completo" required
                       value="<?= htmlspecialchars($nombreSesion) ?>">
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label">Correo electrónico *</label>
                <input type="email" class="form-control" id="correo"
                       name="correo" required
                       value="<?= htmlspecialchars($emailSesion) ?>">
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono *</label>
                <input type="tel" class="form-control" id="telefono"
                       name="telefono" required>
            </div>

            <div class="col-md-6">
                <label for="id_animal" class="form-label">Mascota que deseas adoptar *</label>
                <select class="form-select" id="id_animal" name="id_animal" required>
                    <option value="">-- Selecciona una mascota --</option>
                    <?php foreach ($animales as $animal): ?>
                        <option value="<?= (int)$animal['id_animal'] ?>"
                            <?= ($idAnimalSeleccionado === (int)$animal['id_animal']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($animal['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">
                    También puedes elegir la mascota desde el catálogo de abajo.
                </div>
            </div>

            <div class="col-12">
                <label for="mensaje" class="form-label">
                    Cuéntanos por qué quieres adoptar y cómo cuidarás a la mascota *
                </label>
                <textarea class="form-control" id="mensaje" name="mensaje"
                          rows="4" required></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            Enviar solicitud
        </button>
    </form>

    <!-- CATÁLOGO DE ANIMALES (MISMA BD) -->
    <hr class="my-5">

    <h2 class="h5 mb-3">Catálogo de animales disponibles</h2>
    <p class="text-muted">
        Da clic en <strong>Elegir esta mascota</strong> para rellenar el formulario con ese animal.
    </p>

    <?php if (empty($animales)): ?>
        <div class="alert alert-info">
            Por el momento no hay animales disponibles para adopción.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($animales as $a): ?>
                <?php
                    $foto = $a['foto_url'] ?: '/FRONTEND/IMG/sin-foto.png';
                ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($foto) ?>"
                             class="card-img-top"
                             alt="Foto de <?= htmlspecialchars($a['nombre']) ?>"
                             style="height:220px;object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">
                                <?= htmlspecialchars($a['nombre']) ?>
                            </h5>
                            <p class="card-text small text-muted mb-2">
                                <?= htmlspecialchars(tipoTexto($a['id_tipo'])) ?> ·
                                <?= (int)$a['edad_anios'] ?> años ·
                                <?= htmlspecialchars($a['sexo']) ?>
                            </p>
                            <p class="card-text small flex-grow-1">
                                <?= htmlspecialchars($a['descripcion'] ?: 'Sin descripción adicional.') ?>
                            </p>
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm mt-2 elegir-animal"
                                data-id="<?= (int)$a['id_animal'] ?>"
                            >
                                Elegir esta mascota
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</main>

<footer class="bg-dark text-light py-3 mt-auto">
  <div class="container text-center small">
    &copy; <?= date('Y') ?> AdoptaConAmor - Todos los derechos reservados.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Cuando el usuario da clic en "Elegir esta mascota", ponemos ese id en el select
document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('.elegir-animal');
    if (!btn) return;

    const id = btn.dataset.id;
    const select = document.getElementById('id_animal');
    if (!select) return;

    select.value = id;

    // Hacemos un pequeño scroll hacia el formulario para que se note
    select.scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>

</body>
</html>
