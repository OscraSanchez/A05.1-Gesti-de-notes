<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

$busqueda = trim(is_string($_GET['busqueda'] ?? null) ? $_GET['busqueda'] : '');
if (preg_match('/^.{0,100}/us', $busqueda, $coincidenciasBusqueda) === 1) {
    $busqueda = $coincidenciasBusqueda[0];
} else {
    $busqueda = '';
}
$filtroSql = '';
$parametros = [];
if ($busqueda !== '') {
    $filtroSql = ' WHERE CONCAT_WS(\' \', u.nombre, u.apellidos) LIKE :nombre_completo
        OR u.nombre LIKE :nombre OR u.apellidos LIKE :apellidos
        OR c.nombre LIKE :curso OR g.nombre LIKE :grado OR s.nombre LIKE :asignatura';
    foreach (['nombre_completo', 'nombre', 'apellidos', 'curso', 'grado', 'asignatura'] as $campo) {
        $parametros[$campo] = '%' . $busqueda . '%';
    }
}
$consulta = $conexion->prepare(
    'SELECT
        a.id_alumno,
        u.nombre,
        u.apellidos,
        c.nombre AS curso,
        g.nombre AS grado_academico,
        GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre SEPARATOR \', \') AS asignaturas
     FROM tb_curso_asignatura AS ca
     INNER JOIN tb_curso AS c ON c.id_curso = ca.id_curso
     INNER JOIN tb_grado_academico AS g ON g.id_grado = c.id_grado
     INNER JOIN tb_alumnos AS a ON a.id_curso = c.id_curso
     INNER JOIN tb_usuarios AS u ON u.id_usuario = a.id_usuario
     INNER JOIN tb_asignatura AS s ON s.id_asignatura = ca.id_asignatura
     ' . $filtroSql . '
     GROUP BY a.id_alumno, u.nombre, u.apellidos, c.nombre, g.nombre
     ORDER BY c.nombre, u.apellidos, u.nombre'
);
$consulta->execute($parametros);
$alumnos = $consulta->fetchAll();

function escaparListadoProfesor(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
