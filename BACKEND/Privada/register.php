<?php
// BACKEND/Privada/register.php
session_start();
require_once __DIR__ . '/../DATABASE/conexion.php';

$errores = '';
$exito   = '';

$nombre   = '';
$apellido = '';
$email    = '';
$telefono = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $apellido  = trim($_POST['apellido'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nombre === '' || $email === '' || $password === '' || $password2 === '') {
        $errores = 'Por favor llena todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores = 'El correo no es válido.';
    } elseif ($password !== $password2) {
        $errores = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $errores = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            // Verificar que el correo no exista
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email');
            $stmt->execute([':email' => $email]);

            if ($stmt->fetchColumn() > 0) {
                $errores = 'Ya existe una cuenta con ese correo.';
            } else {
                // Buscar el rol de "adoptante" en la tabla roles (columna nombre)
                $stmtRol = $pdo->prepare('SELECT id_rol FROM roles WHERE nombre = :rol');
                $stmtRol->execute([':rol' => 'adoptante']);
                $idRol = $stmtRol->fetchColumn();

                // Si no se encuentra, por seguridad definimos un rol por defecto (3, como comentaste)
                if (!$idRol) {
                    $idRol = 3;
                }

                // Hashear la contraseña
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Insertar usuario nuevo
                $stmtIns = $pdo->prepare('
                    INSERT INTO usuarios (id_rol, nombre, apellidos, email, password_hash, telefono)
                    VALUES (:id_rol, :nombre, :apellidos, :email, :password_hash, :telefono)
                ');

                $stmtIns->execute([
                    ':id_rol'        => $idRol,
                    ':nombre'        => $nombre,
                    ':apellidos'     => $apellido,   // columna apellidos en la BD
                    ':email'         => $email,
                    ':password_hash' => $hash,
                    ':telefono'      => $telefono,
                ]);

                $exito = 'Tu cuenta se creó correctamente. Ahora puedes iniciar sesión.';
                // Limpiar campos del formulario
                $nombre = $apellido = $email = $telefono = '';
            }
        } catch (PDOException $e) {
            $errores = 'Error al registrar usuario: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../FRONTEND/CSS/styles.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3 text-center">Crear cuenta</h1>
                    <p class="text-center text-muted mb-4">
                        Regístrate para poder enviar solicitudes de adopción.
                    </p>

                    <?php if ($errores): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errores); ?></div>
                    <?php endif; ?>

                    <?php if ($exito): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($exito); ?>
                            <a href="login.php" class="alert-link"> Iniciar sesión</a>
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control"
                                       required value="<?php echo htmlspecialchars($nombre); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control"
                                       value="<?php echo htmlspecialchars($apellido); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Correo electrónico *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                       value="<?php echo htmlspecialchars($telefono); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Repetir contraseña *</label>
                                <input type="password" name="password2" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-4">
                            Registrarme
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        ¿Ya tienes cuenta?
                        <a href="login.php">Iniciar sesión</a>
                    </p>
                    <p class="text-center mt-2 mb-0">
                        <a href="../../index.html">← Volver al sitio público</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
