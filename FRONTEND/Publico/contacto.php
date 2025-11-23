<?php
// FRONTEND/Publico/contacto.php

// 1) Conexión a la BD
require_once __DIR__ . '/../../BACKEND/DATABASE/conexion.php';

$mensajeExito = '';
$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2) Recibir datos del formulario
    $nombre  = trim($_POST['nombre']  ?? '');
    $email   = trim($_POST['email']   ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // 3) Validaciones básicas
    if ($nombre === '' || $email === '' || $mensaje === '') {
        $mensajeError = 'Por favor llena todos los campos.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo no tiene un formato válido.';
    } else {
        try {
            // 4) Insertar en la base de datos
            // OJO: Ajusta el nombre de la tabla y columnas si las tuyas son diferentes
            $sql = "INSERT INTO mensajes_contacto (nombre, email, mensaje, fecha_envio)
                    VALUES (:nombre, :email, :mensaje, NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre'  => $nombre,
                ':email'   => $email,
                ':mensaje' => $mensaje
            ]);

            $mensajeExito = 'Tu mensaje se envió correctamente. ¡Gracias por contactarnos!';

            // Opcional: limpiar campos después de guardar
            $nombre = '';
            $email  = '';
            $mensaje = '';

        } catch (PDOException $e) {
            $mensajeError = 'Error al guardar el mensaje. Intenta más tarde.';
            // Puedes descomentar esto mientras pruebas:
            // $mensajeError .= ' Detalle: ' . $e->getMessage();
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
        <li class="nav-item"><a class="nav-link" href="../../index.html">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="animales.php">Animales</a></li>
        <li class="nav-item"><a class="nav-link" href="como-adoptar.php">Cómo adoptar</a></li>
        <li class="nav-item"><a class="nav-link active" href="contacto.php">Contacto</a></li>
        <li class="nav-item ms-lg-3">
          <a class="btn btn-outline-light btn-sm" href="/BACKEND/Privada/login.php">
            Iniciar sesión
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

          <div class="card border-0 shadow-sm h-100">
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

              <!-- 5) Formulario que envía por POST a esta misma página -->
              <form method="post" action="contacto.php">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input
                      type="text"
                      name="nombre"
                      class="form-control"
                      placeholder="Tu nombre"
                      value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>"
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
                      value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
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
                    ><?php echo isset($mensaje) ? htmlspecialchars($mensaje) : ''; ?></textarea>
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
