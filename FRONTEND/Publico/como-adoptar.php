<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Cómo adoptar - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/FRONTEND/CSS/index.css" />
</head>
<body class="bg-light d-flex flex-column min-vh-100">

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
        <li class="nav-item"><a class="nav-link" href="animales.php">Animales</a></li>
        <li class="nav-item"><a class="nav-link active" href="como-adoptar.php">Cómo adoptar</a></li>
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
      <div class="row g-5">
        <div class="col-lg-7">
          <h1 class="h2 fw-bold mb-3">Proceso de adopción</h1>
          <p class="text-muted mb-4">
            El objetivo es asegurar que cada animal llegue a un hogar responsable.
            Este flujo es un ejemplo para el proyecto, puedes adaptarlo a las reglas del refugio real.
          </p>

          <ol class="list-group list-group-numbered shadow-sm">
            <li class="list-group-item border-0 border-bottom">
              <div class="fw-semibold">Paso 1 · Elige a tu nuevo amigo</div>
              <p class="small text-muted mb-0">
                Explora la sección de animales, revisa su edad, tamaño, comportamiento
                y necesidades especiales.
              </p>
            </li>
            <li class="list-group-item border-0 border-bottom">
              <div class="fw-semibold">Paso 2 · Llena tu solicitud</div>
              <p class="small text-muted mb-0">
                Completa un formulario con tus datos de contacto, tipo de vivienda y
                experiencia previa con animales.
              </p>
            </li>
            <li class="list-group-item border-0 border-bottom">
              <div class="fw-semibold">Paso 3 · Entrevista y visita</div>
              <p class="small text-muted mb-0">
                El refugio revisa tu información y agenda una visita para conocer al
                animal y resolver dudas.
              </p>
            </li>
            <li class="list-group-item border-0">
              <div class="fw-semibold">Paso 4 · Firma y seguimiento</div>
              <p class="small text-muted mb-0">
                Se firma un convenio de adopción responsable y se realizan visitas o
                contactos de seguimiento.
              </p>
            </li>
          </ol>
        </div>

        <div class="col-lg-5">
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
              <h5 class="card-title mb-3">Requisitos básicos (ejemplo)</h5>
              <ul class="small text-muted mb-0">
                <li>Ser mayor de 18 años.</li>
                <li>Identificación oficial vigente.</li>
                <li>Comprobante de domicilio reciente.</li>
                <li>Compromiso de esterilización si aún no se ha realizado.</li>
                <li>Disposición para visitas de seguimiento.</li>
              </ul>
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title mb-3">Preguntas frecuentes</h5>

              <div class="accordion accordion-flush" id="faqAdopcion">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq1c">
                      ¿Tiene costo la adopción?
                    </button>
                  </h2>
                  <div id="faq1c" class="accordion-collapse collapse"
                       data-bs-parent="#faqAdopcion">
                    <div class="accordion-body small text-muted">
                      Normalmente se solicita una cuota de recuperación para cubrir
                      vacunas y esterilización. El monto puede variar según el refugio.
                    </div>
                  </div>
                </div>

                <div class="accordion-item">
                  <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq2c">
                      ¿Puedo devolver al animal si no se adapta?
                    </button>
                  </h2>
                  <div id="faq2c" class="accordion-collapse collapse"
                       data-bs-parent="#faqAdopcion">
                    <div class="accordion-body small text-muted">
                      En caso de problemas graves, el refugio debe ser el primer
                      contacto. Se buscará otra familia adecuada para el animal.
                    </div>
                  </div>
                </div>

                <div class="accordion-item">
                  <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq3c">
                      ¿Cuánto tarda el proceso?
                    </button>
                  </h2>
                  <div id="faq3c" class="accordion-collapse collapse"
                       data-bs-parent="#faqAdopcion">
                    <div class="accordion-body small text-muted">
                      Depende de la revisión de la solicitud y disponibilidad del
                      refugio, pero normalmente puede tomar de unos días a un par de semanas.
                    </div>
                  </div>
                </div>

              </div>

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
