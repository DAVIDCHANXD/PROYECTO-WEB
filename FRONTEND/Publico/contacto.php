<?php
// FRONTEND/Publico/contacto.php

require_once __DIR__ . '/../../BACKEND/DATABASE/conexion.php';

$mensajeExito = '';
$mensajeError = '';

// Variables para repoblar el formulario si hay error
$nombre   = '';
$email    = '';
$telefono = '';
$asunto   = '';
$mensaje  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Recibir datos
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $asunto   = trim($_POST['asunto']   ?? '');
    $mensaje  = trim($_POST['mensaje']  ?? '');

    // 2) Validaciones básicas
    if ($nombre === '' || $email === '' || $telefono === '' || $asunto === '' || $mensaje === '') {
        $mensajeError = 'Por favor llena todos los campos.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo no tiene un formato válido.';
    } else {
        // (Opcional) Validar teléfono muy simple
        // if (!preg_match('/^[0-9+\s\-]{7,20}$/', $telefono)) {
        //     $mensajeError = 'El teléfono no parece válido.';
        // }

        if ($mensajeError === '') {
            try {
                // 3) Insertar en la base de datos
                // OJO: ajusta el nombre de la tabla/columnas si tus nombres son diferentes
                $sql = "INSERT INTO mensajes_contacto 
                        (nombre, email, telefono, asunto, mensaje, fecha_envio)
                        VALUES 
                        (:nombre, :email, :telefono, :asunto, :mensaje, NOW())";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre'   => $nombre,
                    ':email'    => $email,
                    ':telefono' => $telefono,
                    ':asunto'   => $asunto,
                    ':mensaje'  => $mensaje
                ]);

                $mensajeExito = 'Tu mensaje se envió correctamente. ¡Gracias por contactarnos!';

                // Limpiar campos
                $nombre   = '';
                $email    = '';
                $telefono = '';
                $asunto   = '';
                $mensaje  = '';

            } catch (PDOException $e) {
                $mensajeError = 'Error al guardar el mensaje. Intenta más tarde.';
                // Para depurar mientras desarrollas:
                // $mensajeError .= ' Detalle: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Contacto - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css" />
    <link rel="stylesheet" href="/FRONTEND/CSS/contacto.css" />    
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm main-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/index.html">
      <span class="logo-pill">AC</span> AdoptaConAmor
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="/index.html">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="/FRONTEND/Publico/animales.php">Animales</a></li>
        <li class="nav-item"><a class="nav-link" href="/FRONTEND/Publico/como-adoptar.php">Cómo adoptar</a></li>
        <li class="nav-item"><a class="nav-link" href="/FRONTEND/Publico/solicitud_adopcion.php">Solicitud de adopción</a></li>
        <li class="nav-item"><a class="nav-link active" href="/FRONTEND/Publico/contacto.php">Contacto</a></li>
        <li class="nav-item ms-lg-3">
          <a class="btn btn-outline-light btn-sm" href="/BACKEND/Privada/login.php">
            Iniciar sesión
          </a>
        </li>
        <li class="nav-item ms-lg-3">
          <a class="btn btn-outline-light btn-sm" href="/BACKEND/Privada/register.php">
            Registrase
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main class="flex-grow-1 contacto-bg">
  <section class="py-5 contacto-hero">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-5">
          <h1 class="h3 fw-bold mb-3">Contacto</h1>
          <p class="text-muted">
            Esta información es de ejemplo para el proyecto. Puedes sustituirla
            posteriormente por los datos reales del refugio con el que trabajes.
          </p>

          <!-- 👇 quitamos h-100 -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Refugio Patitas Felices</h5>
              <p class="mb-1">TuCiudad, TuEstado</p>
              <p class="mb-1">Tel: 555-000-0000</p>
              <p class="mb-1">Email: info@patitas.test</p>
              <p class="text-muted small mt-3 mb-0">
                *Datos de ejemplo para el proyecto escolar.
              </p>
            </div>
          </div>
        </div>


        <div class="col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-3">Envíanos un mensaje</h5>

              <!-- Mensajes de éxito / error -->
              <?php if ($mensajeExito): ?>
                  <div class="alert alert-success py-2"><?php echo htmlspecialchars($mensajeExito); ?></div>
              <?php endif; ?>
              <?php if ($mensajeError): ?>
                  <div class="alert alert-danger py-2"><?php echo htmlspecialchars($mensajeError); ?></div>
              <?php endif; ?>

              <form method="post" action="contacto.php">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input
                      type="text"
                      name="nombre"
                      class="form-control"
                      placeholder="Tu nombre"
                      value="<?php echo htmlspecialchars($nombre); ?>"
                      required
                    >
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Correo</label>
                    <input
                      type="email"
                      name="email"
                      class="form-control"
                      placeholder="tu@correo.com"
                      value="<?php echo htmlspecialchars($email); ?>"
                      required
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input
                      type="text"
                      name="telefono"
                      class="form-control"
                      placeholder="Tu número"
                      value="<?php echo htmlspecialchars($telefono); ?>"
                      required
                    >
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Asunto</label>
                    <input
                      type="text"
                      name="asunto"
                      class="form-control"
                      placeholder="Motivo de tu mensaje"
                      value="<?php echo htmlspecialchars($asunto); ?>"
                      required
                    >
                  </div>

                  <div class="col-12">
                    <label class="form-label">Mensaje</label>
                    <textarea
                      name="mensaje"
                      class="form-control"
                      rows="4"
                      placeholder="Escribe tu mensaje..."
                      required
                    ><?php echo htmlspecialchars($mensaje); ?></textarea>
                  </div>
                  <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                      Enviar mensaje
                    </button>
                  </div>
                </div>
              </form>

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
