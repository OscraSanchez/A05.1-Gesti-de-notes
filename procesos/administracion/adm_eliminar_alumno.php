<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_id_alumno = filter_input(INPUT_POST, 'adm_id_alumno', FILTER_VALIDATE_INT);
if (!is_int($adm_id_alumno) || $adm_id_alumno < 1) {
    adm_redirigir('alumnos', 'datos_invalidos');
}

try {
    $adm_eliminar = $conexion->prepare('DELETE FROM tb_alumnos WHERE id_alumno=:id_alumno');
    $adm_eliminar->execute(['id_alumno' => $adm_id_alumno]);
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1451) {
        adm_redirigir('alumnos', 'dependencias');
    }
    throw $adm_error;
}

adm_redirigir('alumnos', 'eliminado');
