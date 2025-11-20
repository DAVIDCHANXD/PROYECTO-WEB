<?php
// FRONTEND/Publico/animales.php

require_once __DIR__ . '/../../BACKEND/Privada/get_animales.php';

$animales      = [];
$errorAnimales = '';

try {
    $animales = obtener_animales();
} catch (Throwable $e) {
    // Aquí ya te debería decir EXACTAMENTE qué falla
    $errorAnimales = 'Error al cargar los animales: ' . $e->getMessage();
}

// Mapas simples para mostrar texto
$mapTipos = [
    1 => 'Perro',
    2 => 'Gato',
    3 => 'Otros',
];
$mapTamanos = [
    1 => 'Grande',
    2 => 'Mediano',
    3 => 'Pequeño',
];
?>
