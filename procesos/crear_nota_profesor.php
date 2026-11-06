<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$idAlumno = filter_input(INPUT_POST, 'id_alumno', FILTER_VALIDATE_INT);
$idCursoAsignatura = filter_input(INPUT_POST, 'id_curso_asignatura', FILTER_VALIDATE_INT);
$evaluacion = filter_input(INPUT_POST, 'evaluacion', FILTER_VALIDATE_INT);
$nota = $_POST['nota'] ?? null;
$observaciones = $_POST['observaciones'] ?? '';

if (
    !is_int($idAlumno) || $idAlumno < 1
    || !is_int($idCursoAsignatura) || $idCursoAsignatura < 1
    || !is_int($evaluacion) || $evaluacion < 1 || $evaluacion > 3
    || !is_string($nota)
    || !preg_match('/^(?:10(?:\.0{1,2})?|[0-9](?:\.[0-9]{1,2})?)$/', $nota)
    || !observacionesValidasProfesor($observaciones)
) {
    if (is_int($idAlumno) && $idAlumno > 0) {
        redirigirFichaProfesor($idAlumno, 'datos_invalidos');
    }
    http_response_code(400);
    exit('Datos de nota no válidos.');
}

$permiso = $conexion->prepare(
    'SELECT a.id_alumno
     FROM tb_alumnos AS a
     INNER JOIN tb_curso_asignatura AS ca ON ca.id_curso = a.id_curso
     WHERE a.id_alumno = :id_alumno
        AND ca.id_curso_asignatura = :id_curso_asignatura'
);
$permiso->execute([
    'id_alumno' => $idAlumno,
    'id_curso_asignatura' => $idCursoAsignatura,
]);
if (!$permiso->fetch()) {
    http_response_code(404);
    exit('El alumno o la asignatura no existe en el mismo curso.');
}

$insertar = $conexion->prepare(
    'INSERT INTO tb_notas (id_alumno, id_curso_asignatura, id_curso, evaluacion, nota, observaciones)
     SELECT a.id_alumno, ca.id_curso_asignatura, a.id_curso, :evaluacion, :nota, :observaciones
     FROM tb_alumnos AS a
     INNER JOIN tb_curso_asignatura AS ca ON ca.id_curso = a.id_curso
     WHERE a.id_alumno = :id_alumno
        AND ca.id_curso_asignatura = :id_curso_asignatura'
);
try {
    $insertar->execute([
        'evaluacion' => $evaluacion,
        'nota' => $nota,
        'observaciones' => $observaciones === '' ? null : $observaciones,
        'id_alumno' => $idAlumno,
        'id_curso_asignatura' => $idCursoAsignatura,
    ]);
} catch (PDOException $error) {
    if (($error->errorInfo[1] ?? null) === 1062) {
        redirigirFichaProfesor($idAlumno, 'nota_existente');
    }
    throw $error;
}

redirigirFichaProfesor($idAlumno, 'creada');
