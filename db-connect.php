<?php
// db-connect.php
// Este archivo SÍ va a Git.

// 1. Incluimos las credenciales secretas que están en config.php
require_once 'config.php';

// 2. Crear la conexión usando las constantes
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 3. Verificar si la conexión falló (¡Vital!)
if ($conn->connect_error) {
    // 'die()' mata la ejecución del script y muestra el error.
    // En un proyecto real (producción), esto se manejaría con logs,
    // pero para la U, 'die()' es perfecto.
    die("Error de conexión: " . $conn->connect_error);
}

// 4. Opcional pero recomendado: Setear el charset a UTF-8
// Esto evita problemas con tildes o 'ñ' en los datos.
$conn->set_charset("utf8");