<?php
// BACKEND/Privada/animales_listar.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../DATABASE/conexion.php';

$mensaje = $_GET['msg'] ?? null;

// Obtenemos todos los animales con su foto principal (si tiene)
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
                a.adoptado,
                a.visible,
                f.url AS foto_url
            FROM animales a
            LEFT JOIN fotos_animal f
                ON f.id_animal = a.id_animal
               AND f.es_principal = 1
            ORDER BY a.id_animal DESC";
    $stmt = $pdo->query($sql);
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener animales: " . $e->getMessage();
}

// === Funciones de texto para mostrar más bonito ===
function tipoTexto($idTipo)
{
    switch ((int)$idTipo) {
        case 1: return 'Perro';
        case 2: return 'Gato';
        case 3: return 'Conejo / Pequeños mamíferos';
        case 4: return 'Ave';
        case 7: return 'Pez';
        default: return 'Otros';
    }
}

function tamanoTexto($idTamano)
{
    switch ((int)$idTamano) {
        case 1: return 'Muy pequeño';
        case 2: return 'Pequeño';
        case 3: return 'Mediano';
        case 4: return 'Grande';
        default: return 'Otro';
    }
}

function estadoSaludTexto($idEstado)
{
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

function siNoTexto($valor)
{
    return ((int)$valor === 1) ? 'Sí' : 'No';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de animales - AdoptaConAmor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4">Gestión de animales</h1>

    <?php if ($mensaje === 'alta_ok'): ?>
        <div class="alert alert-success">Animal registrado correctamente.</div>
    <?php elseif ($mensaje === 'editar_ok'): ?>
        <div class="alert alert-success">Animal actualizado correctamente.</div>
    <?php elseif ($mensaje === 'eliminar_ok'): ?>
        <div class="alert alert-success">Animal eliminado correctamente.</div>
    <?php elseif ($mensaje === 'error'): ?>
        <div class="alert alert-danger">Ocurrió un error al procesar la operación.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="animales_alta.php" class="btn btn-primary">
            + Nuevo animal
        </a>
        <a href="dashboard.php" class="btn btn-secondary">
            Volver al panel
        </a>
    </div>

    <?php if (empty($animales)): ?>
        <div class="alert alert-info">No hay animales registrados todavía.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Tamaño</th>
                    <th>Edad (años)</th>
                    <th>Sexo</th>
                    <th>Estado de salud</th>
                    <th>Adoptado</th>
                    <th>Visible</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($animales as $a): ?>
                    <tr>
                        <td><?= (int)$a['id_animal'] ?></td>
                        <td>
                            <?php if (!empty($a['foto_url'])): ?>
                                <img src="<?= htmlspecialchars($a['foto_url']) ?>"
                                     alt="Foto de <?= htmlspecialchars($a['nombre']) ?>"
                                     style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                <span class="text-muted">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars(tipoTexto($a['id_tipo'])) ?></td>
                        <td><?= htmlspecialchars(tamanoTexto($a['id_tamano'])) ?></td>
                        <td><?= htmlspecialchars($a['edad_anios']) ?></td>
                        <td><?= htmlspecialchars($a['sexo']) ?></td>
                        <td><?= htmlspecialchars(estadoSaludTexto($a['id_estado_salud'])) ?></td>
                        <td><?= htmlspecialchars(siNoTexto($a['adoptado'])) ?></td>
                        <td><?= htmlspecialchars(siNoTexto($a['visible'])) ?></td>
                        <td>
                            <a href="animales_editar.php?id=<?= (int)$a['id_animal'] ?>"
                               class="btn btn-sm btn-warning">
                                Editar
                            </a>
                            <a href="animales_eliminar.php?id=<?= (int)$a['id_animal'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Seguro que deseas eliminar este animal?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
