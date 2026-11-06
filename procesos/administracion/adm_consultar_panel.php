<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

$adm_conteos = [];
foreach ([
    'usuarios' => 'tb_usuarios',
    'alumnos' => 'tb_alumnos',
    'cursos' => 'tb_curso',
    'asignaturas' => 'tb_asignatura',
    'notas' => 'tb_notas',
] as $adm_nombre => $adm_tabla) {
    $adm_consulta = $conexion->query('SELECT COUNT(*) FROM ' . $adm_tabla);
    $adm_conteos[$adm_nombre] = (int) $adm_consulta->fetchColumn();
}

$adm_nombre_consulta = $conexion->prepare(
    'SELECT nombre, apellidos
     FROM tb_usuarios
     WHERE id_usuario = :id_usuario'
);
$adm_nombre_consulta->execute(['id_usuario' => $_SESSION['adm_id_usuario']]);
$adm_identidad = $adm_nombre_consulta->fetch();
if (!$adm_identidad) {
    http_response_code(404);
    exit('No se encontró la cuenta administrativa.');
}
