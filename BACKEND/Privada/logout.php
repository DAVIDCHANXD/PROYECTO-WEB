<?php
// BACKEND/Privada/logout.php
session_start();

// Vaciar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Opcional: borrar la cookie de sesión por si existe
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// A partir de aquí solo mostramos la vista de "sesión cerrada"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sesión cerrada - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container min-vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm border-0" style="max-width: 400px; width: 100%;">
        <div class="card-body text-center">
            <h4 class="card-title mb-3">Sesión cerrada</h4>
            <p class="card-text mb-4">
                Tu sesión se ha cerrado correctamente.
            </p>
            <!-- Botón que manda al index -->
            <a href="../../index.html" class="btn btn-primary">
                Aceptar
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
