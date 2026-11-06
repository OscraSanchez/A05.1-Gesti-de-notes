<?php

$servername = 'localhost';
$port = '3306';
$dbusername = 'root';
$dbpassword = '';
$dbname = 'gestio_notes';

try {
    $conexion = new PDO(
        "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4",
        $dbusername,
        $dbpassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Comprueba la configuración del servidor.');
}