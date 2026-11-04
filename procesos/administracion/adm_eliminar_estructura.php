<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_tablas = [
    'grado' => ['tabla' => 'tb_grado_academico', 'columna' => 'id_grado'],
    'curso' => ['tabla' => 'tb_curso', 'columna' => 'id_curso'],
    'asignatura' => ['tabla' => 'tb_asignatura', 'columna' => 'id_asignatura'],
    'curso_asignatura' => ['tabla' => 'tb_curso_asignatura', 'columna' => 'id_curso_asignatura'],
];
$adm_tipo = $_POST['adm_tipo'] ?? '';
$adm_id = filter_input(INPUT_POST, 'adm_id', FILTER_VALIDATE_INT);
if (!is_string($adm_tipo) || !isset($adm_tablas[$adm_tipo]) || !is_int($adm_id) || $adm_id < 1) {
    adm_redirigir('estructura', 'datos_invalidos');
}

$adm_tabla = $adm_tablas[$adm_tipo]['tabla'];
$adm_columna = $adm_tablas[$adm_tipo]['columna'];
try {
    $adm_eliminar = $conexion->prepare("DELETE FROM $adm_tabla WHERE $adm_columna = :id");
    $adm_eliminar->execute(['id' => $adm_id]);
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1451) {
        adm_redirigir('estructura', 'dependencias');
    }
    throw $adm_error;
}

adm_redirigir('estructura', 'eliminado');
