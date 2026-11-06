<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$idNota = filter_input(INPUT_POST, 'id_nota', FILTER_VALIDATE_INT);
$idAlumno = filter_input(INPUT_POST, 'id_alumno', FILTER_VALIDATE_INT);
$nota = $_POST['nota'] ?? null;
$observaciones = $_POST['observaciones'] ?? '';

if (
    !is_int($idNota) || $idNota < 1
    || !is_int($idAlumno) || $idAlumno < 1
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

$actualizar = $conexion->prepare(
    'UPDATE tb_notas AS n
     INNER JOIN tb_alumnos AS a ON a.id_alumno = n.id_alumno
     INNER JOIN tb_curso_asignatura AS ca
        ON ca.id_curso_asignatura = n.id_curso_asignatura
        AND ca.id_curso = a.id_curso
     SET n.nota = :nota, n.observaciones = :observaciones
     WHERE n.id_nota = :id_nota
        AND n.id_alumno = :id_alumno'
);
$actualizar->execute([
    'nota' => $nota,
    'observaciones' => $observaciones === '' ? null : $observaciones,
    'id_nota' => $idNota,
    'id_alumno' => $idAlumno,
]);

if ($actualizar->rowCount() === 0) {
    $verificar = $conexion->prepare(
        'SELECT n.id_nota
         FROM tb_notas AS n
         INNER JOIN tb_curso_asignatura AS ca
            ON ca.id_curso_asignatura = n.id_curso_asignatura
         WHERE n.id_nota = :id_nota
            AND n.id_alumno = :id_alumno'
    );
    $verificar->execute([
        'id_nota' => $idNota,
        'id_alumno' => $idAlumno,
    ]);
    if (!$verificar->fetch()) {
        http_response_code(403);
        exit('No se encontró la nota que quieres modificar.');
    }
}

redirigirFichaProfesor($idAlumno, 'actualizada');
