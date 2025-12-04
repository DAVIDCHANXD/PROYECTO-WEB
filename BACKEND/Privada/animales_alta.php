<?php
// BACKEND/Privada/animales_alta.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../DATABASE/conexion.php';
$errores = [];
$nombre = '';
$id_tipo = '';
$id_tamano = '';
$edad_anios = '';
$sexo = '';
$id_estado_salud = '';
$descripcion = '';
$url_imagen = '';
$adoptado = 0;
$visible = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre          = trim($_POST['nombre'] ?? '');
    $id_tipo         = trim($_POST['id_tipo'] ?? '');
    $id_tamano       = trim($_POST['id_tamano'] ?? '');
    $edad_anios      = trim($_POST['edad_anios'] ?? '');
    $sexo            = trim($_POST['sexo'] ?? '');
    $id_estado_salud = trim($_POST['id_estado_salud'] ?? '');
    $descripcion     = trim($_POST['descripcion'] ?? '');
    $url_imagen      = trim($_POST['url_imagen'] ?? '');
    $adoptado        = isset($_POST['adoptado']) ? 1 : 0;
    $visible         = isset($_POST['visible']) ? 1 : 0;

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if ($id_tipo === '' || !ctype_digit($id_tipo)) {
        $errores[] = 'Selecciona un tipo de animal válido.';
    }
    if ($id_tamano === '' || !ctype_digit($id_tamano)) {
        $errores[] = 'Selecciona un tamaño válido.';
    }
    if ($edad_anios === '' || !ctype_digit($edad_anios)) {
        $errores[] = 'La edad en años debe ser un número entero.';
    }
    if ($sexo !== 'Macho' && $sexo !== 'Hembra') {
        $errores[] = 'Selecciona un sexo válido.';
    }
    if ($id_estado_salud === '' || !ctype_digit($id_estado_salud)) {
        $errores[] = 'Selecciona un estado de salud válido.';
    }

    if (empty($errores)) {
        try {
            $pdo->beginTransaction();
            $sqlAnimal = "INSERT INTO animales 
                            (nombre, id_tipo, id_tamano, edad_anios, sexo, id_estado_salud, descripcion, adoptado, visible)
                          VALUES
                            (:nombre, :id_tipo, :id_tamano, :edad_anios, :sexo, :id_estado_salud, :descripcion, :adoptado, :visible)";
            $stmt = $pdo->prepare($sqlAnimal);
            $stmt->execute([
                ':nombre'          => $nombre,
                ':id_tipo'         => $id_tipo,
                ':id_tamano'       => $id_tamano,
                ':edad_anios'      => $edad_anios,
                ':sexo'            => $sexo,
                ':id_estado_salud' => $id_estado_salud,
                ':descripcion'     => $descripcion,
                ':adoptado'        => $adoptado,
                ':visible'         => $visible,
            ]);
            $idAnimal = (int)$pdo->lastInsertId();
            if ($url_imagen !== '') {
                $sqlFoto = "INSERT INTO fotos_animal (id_animal, url, es_principal)
                            VALUES (:id_animal, :url, 1)";
                $stmtFoto = $pdo->prepare($sqlFoto);
                $stmtFoto->execute([
                    ':id_animal' => $idAnimal,
                    ':url'       => $url_imagen,
                ]);
            }
            $pdo->commit();
            header('Location: animales_listar.php?msg=alta_ok');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errores[] = 'Error al guardar en la base de datos: ' . $e->getMessage();
        }
    }
}

function selected($a, $b) {
    return (string)$a === (string)$b ? 'selected' : '';
}
function checked($valor) {
    return (int)$valor === 1 ? 'checked' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo animal - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Registrar nuevo animal</h1>
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text"
                   class="form-control"
                   id="nombre"
                   name="nombre"
                   value="<?= htmlspecialchars($nombre) ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="id_tipo" class="form-label">Tipo de animal *</label>
            <select class="form-select" id="id_tipo" name="id_tipo" required>
                <option value="">-- Selecciona --</option>
                <option value="1" <?= selected($id_tipo, 1) ?>>Perro</option>
                <option value="2" <?= selected($id_tipo, 2) ?>>Gato</option>
                <option value="3" <?= selected($id_tipo, 3) ?>>Conejo / Pequeños mamíferos</option>
                <option value="4" <?= selected($id_tipo, 4) ?>>Ave</option>
                <option value="7" <?= selected($id_tipo, 7) ?>>Pez</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_tamano" class="form-label">Tamaño *</label>
            <select class="form-select" id="id_tamano" name="id_tamano" required>
                <option value="">-- Selecciona --</option>
                <option value="1" <?= selected($id_tamano, 1) ?>>Muy pequeño</option>
                <option value="2" <?= selected($id_tamano, 2) ?>>Pequeño</option>
                <option value="3" <?= selected($id_tamano, 3) ?>>Mediano</option>
                <option value="4" <?= selected($id_tamano, 4) ?>>Grande</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="edad_anios" class="form-label">Edad (años) *</label>
            <input type="number"
                   min="0"
                   class="form-control"
                   id="edad_anios"
                   name="edad_anios"
                   value="<?= htmlspecialchars($edad_anios) ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="sexo" class="form-label">Sexo *</label>
            <select class="form-select" id="sexo" name="sexo" required>
                <option value="">-- Selecciona --</option>
                <option value="Macho"  <?= selected($sexo, 'Macho') ?>>Macho</option>
                <option value="Hembra" <?= selected($sexo, 'Hembra') ?>>Hembra</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_estado_salud" class="form-label">Estado de salud *</label>
            <select class="form-select" id="id_estado_salud" name="id_estado_salud" required>
                <option value="">-- Selecciona --</option>
                <option value="1" <?= selected($id_estado_salud, 1) ?>>Saludable</option>
                <option value="2" <?= selected($id_estado_salud, 2) ?>>Vacunas en proceso</option>
                <option value="3" <?= selected($id_estado_salud, 3) ?>>En recuperación</option>
                <option value="4" <?= selected($id_estado_salud, 4) ?>>Tratamiento especial</option>
                <option value="5" <?= selected($id_estado_salud, 5) ?>>Condición crónica</option>
                <option value="6" <?= selected($id_estado_salud, 6) ?>>Observación</option>
                <option value="7" <?= selected($id_estado_salud, 7) ?>>Otro / Revisar</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control"
                      id="descripcion"
                      name="descripcion"
                      rows="3"><?= htmlspecialchars($descripcion) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="url_imagen" class="form-label">URL de imagen (opcional)</label>
            <input type="url"
                   class="form-control"
                   id="url_imagen"
                   name="url_imagen"
                   placeholder="https://ejemplo.com/mi-mascota.jpg"
                   value="<?= htmlspecialchars($url_imagen) ?>">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox"
                   class="form-check-input"
                   id="adoptado"
                   name="adoptado"
                   value="1" <?= checked($adoptado) ?>>
            <label class="form-check-label" for="adoptado">Marcar como ya adoptado</label>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox"
                   class="form-check-input"
                   id="visible"
                   name="visible"
                   value="1" <?= checked($visible) ?>>
            <label class="form-check-label" for="visible">Visible en la parte pública</label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="animales_listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
