<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['rol_usuario'] !== 'profesor' || !is_int($_SESSION['id_usuario'])) {
    http_response_code(403);
    exit('Acceso restringido al profesorado.');
}

header('Cache-Control: no-store, private');

require_once __DIR__ . '/../conexion/conexion.php';

function observacionesValidasProfesor(mixed $observaciones): bool
{
    if (!is_string($observaciones)) {
        return false;
    }

    $longitud = preg_match_all('/./us', $observaciones, $coincidencias);
    return $longitud !== false && $longitud <= 255;
}

function redirigirFichaProfesor(int $idAlumno, string $estado): never
{
    header('Location: ../vistas/profesores/alumno.php?id=' . $idAlumno . '&estado=' . rawurlencode($estado));
    exit;
}
