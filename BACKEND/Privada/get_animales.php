<?php
// BACKEND/Privada/get_animales.php

require_once __DIR__ . '/../DATABASE/conexion.php';

/**
 * Devuelve la lista de animales en un array PHP.
 * Lanza Exception si algo falla.
 */
function obtener_animales(): array
{
    // Usamos la $pdo que viene de conexion.php
    global $pdo;

    if (!isset($pdo)) {
        throw new Exception('No hay conexión PDO. Revisa BACKEND/DATABASE/conexion.php');
    }

    // Ajusta los nombres de columnas a lo que tenga tu tabla "animales"
    $sql = "
        SELECT 
            id_animal,
            nombre,
            id_tipo,
            id_tamano,
            COALESCE(edad_anios, edad) AS edad_mostrar,
            descripcion
        FROM animales
        WHERE visible = 1
          AND adoptado = 0
        ORDER BY fecha_registro DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * MODO API (opcional):
 * Si entras a este archivo por URL con ?modo=json
 * te responde un JSON con los animales o con el error.
 *
 * Ejemplo: http://tu-servidor/BACKEND/Privada/get_animales.php?modo=json
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
