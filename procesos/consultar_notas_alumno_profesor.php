<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

$idAlumno = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!is_int($idAlumno) || $idAlumno < 1) {
    http_response_code(400);
    exit('Identificador de alumno no válido.');
}

$consulta = $conexion->prepare(
    'SELECT
        a.id_alumno,
        u.nombre,
        u.apellidos,
        c.nombre AS curso,
        g.nombre AS grado_academico,
        ca.id_curso_asignatura,
        s.codigo AS asignatura_codigo,
        s.nombre AS asignatura_nombre,
        n.id_nota,
        n.evaluacion,
        n.nota,
        n.observaciones
     FROM tb_alumnos AS a
     INNER JOIN tb_usuarios AS u ON u.id_usuario = a.id_usuario
     INNER JOIN tb_curso AS c ON c.id_curso = a.id_curso
     INNER JOIN tb_grado_academico AS g ON g.id_grado = c.id_grado
     INNER JOIN tb_curso_asignatura AS ca ON ca.id_curso = c.id_curso
     INNER JOIN tb_asignatura AS s ON s.id_asignatura = ca.id_asignatura
     LEFT JOIN tb_notas AS n
        ON n.id_alumno = a.id_alumno
        AND n.id_curso_asignatura = ca.id_curso_asignatura
     WHERE a.id_alumno = :id_alumno
     ORDER BY s.nombre, n.evaluacion'
);
$consulta->execute([
    'id_alumno' => $idAlumno,
]);
$filas = $consulta->fetchAll();

if ($filas === []) {
    http_response_code(404);
    exit('No se encontró el alumno o no tiene asignaturas asignadas a su curso.');
}

$alumno = $filas[0];
$asignaturas = [];
foreach ($filas as $fila) {
    $idAsignaturaCurso = (int) $fila['id_curso_asignatura'];
    if (!isset($asignaturas[$idAsignaturaCurso])) {
        $asignaturas[$idAsignaturaCurso] = [
            'id_curso_asignatura' => $idAsignaturaCurso,
            'codigo' => $fila['asignatura_codigo'],
            'nombre' => $fila['asignatura_nombre'],
            'notas' => [],
        ];
    }
    if ($fila['id_nota'] !== null) {
        $asignaturas[$idAsignaturaCurso]['notas'][(int) $fila['evaluacion']] = [
            'id_nota' => (int) $fila['id_nota'],
            'nota' => $fila['nota'],
            'observaciones' => $fila['observaciones'],
        ];
    }
}

$estado = $_GET['estado'] ?? '';
$estadosPermitidos = ['creada', 'actualizada', 'eliminada', 'datos_invalidos', 'nota_existente'];
if (!is_string($estado) || !in_array($estado, $estadosPermitidos, true)) {
    $estado = '';
}

function escaparFichaProfesor(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
