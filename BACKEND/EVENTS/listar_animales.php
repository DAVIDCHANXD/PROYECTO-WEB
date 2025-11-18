<?php
// BACKEND/EVENTS/listar_animales.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../DATABASE/conexion.php';

try {
    $sql = "
        SELECT 
            a.id_animal,
            a.nombre,
            e.nombre  AS especie,
            IFNULL(r.nombre, 'Mestizo') AS raza,
            IFNULL(t.nombre, 'N/D') AS tamanio,
            a.sexo,
            a.edad_aproximada,
            a.descripcion,
            esal.nombre AS estado_salud,
            est.nombre  AS estatus,
            IFNULL(f.ruta_foto, '') AS ruta_foto
        FROM animales a
        JOIN especies e          ON a.id_especie = e.id_especie
        LEFT JOIN razas r        ON a.id_raza = r.id_raza
        LEFT JOIN tamanios t     ON a.id_tamanio = t.id_tamanio
        LEFT JOIN estados_salud esal ON a.id_estado_salud = esal.id_estado_salud
        JOIN estatus_animales est    ON a.id_estatus = est.id_estatus
        LEFT JOIN fotos_animales f   ON a.id_animal = f.id_animal AND f.es_principal = 1
        WHERE est.nombre = 'Disponible'
        ORDER BY a.fecha_publicacion DESC;
    ";

    $stmt = $pdo->query($sql);
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($animales);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => true,
        'mensaje' => 'Error al obtener animales: ' . $e->getMessage()
    ]);
}
