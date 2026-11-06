<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

// ---- Filtro de texto----
$busqueda = trim(is_string($_GET['busqueda'] ?? null) ? $_GET['busqueda'] : '');
if (preg_match('/^.{0,100}/us', $busqueda, $coincidenciasBusqueda) === 1) {
    $busqueda = $coincidenciasBusqueda[0];
} else {
    $busqueda = '';
}

// ---- Filtros de los selects ----
$cursoSel = filter_var($_GET['curso'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$cursoSel = $cursoSel === false ? 0 : $cursoSel;

$anioSel = filter_var($_GET['anio'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$anioSel = $anioSel === false ? 0 : $anioSel;

// ---- Construcción del WHERE (los filtros se suman con AND) ----
$condiciones = [];
$parametros = [];

if ($busqueda !== '') {
    // Paréntesis: así los OR de la búsqueda no rompen los AND de los selects
    $condiciones[] = '(CONCAT_WS(\' \', u.nombre, u.apellidos) LIKE :nombre_completo
        OR u.nombre LIKE :nombre OR u.apellidos LIKE :apellidos
        OR c.nombre LIKE :curso_txt OR g.nombre LIKE :grado OR s.nombre LIKE :asignatura)';
    foreach (['nombre_completo', 'nombre', 'apellidos', 'curso_txt', 'grado', 'asignatura'] as $campo) {
        $parametros[$campo] = '%' . $busqueda . '%';
    }
}
if ($cursoSel > 0) {
    $condiciones[] = 'c.id_curso = :curso_sel';
    $parametros['curso_sel'] = $cursoSel;
}
if ($anioSel > 0) {
    $condiciones[] = 'g.id_grado = :anio_sel';
    $parametros['anio_sel'] = $anioSel;
}

$filtroSql = $condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '';

// ---- Listado de alumnos ----
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
$alumnos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// ---- Datos para rellenar los selects ----
$cursos = $conexion->query(
    'SELECT c.id_curso, c.nombre, g.nombre AS anio
     FROM tb_curso AS c
     INNER JOIN tb_grado_academico AS g ON g.id_grado = c.id_grado
     ORDER BY g.fecha_inicio DESC, c.nivel, c.grupo'
)->fetchAll(PDO::FETCH_ASSOC);

$anios = $conexion->query(
    'SELECT id_grado, nombre
     FROM tb_grado_academico
     ORDER BY fecha_inicio DESC'
)->fetchAll(PDO::FETCH_ASSOC);

function escaparListadoProfesor(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}