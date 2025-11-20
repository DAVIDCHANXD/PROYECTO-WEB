<?php
// BACKEND/Privada/get_animales.php

require_once __DIR__ . '/../DATABASE/conexion.php';

/**
 * Devuelve la lista de animales en un array PHP.
 */
function obtener_animales(): array
{
    global $pdo;

    if (!isset($pdo)) {
        throw new Exception('No hay conexión PDO. Revisa BACKEND/DATABASE/conexion.php');
    }

    // Consulta sencilla: usa solo columnas que probablemente tienes.
    // Ajusta nombres si tu tabla es diferente.
    $sql = "
        SELECT
            id_animal,
            nombre,
            id_tipo,
            id_tamano,
            -- si no tienes edad_anios, puedes quitar esta línea o cambiarla al nombre real
            edad_anios AS edad_mostrar,
            descripcion,
            -- estos son campos de relleno para que el JS no truene aunque no existan en la BD
            ''   AS raza,
            ''   AS estado_salud,
            NULL AS ruta_foto
        FROM animales
        ORDER BY id_animal DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * MODO JSON:
 * http://tu-servidor/BACKEND/Privada/get_animales.php?modo=json
 */
if (php_sapi_name() !== 'cli' && isset($_GET['modo']) && $_GET['modo'] === 'json') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $animales = obtener_animales();
        echo json_encode([
            'ok'       => true,
            'animales' => $animales,
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'ok'    => false,
            'error' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}
