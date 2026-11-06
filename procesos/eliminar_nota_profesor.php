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
if (!is_int($idNota) || $idNota < 1 || !is_int($idAlumno) || $idAlumno < 1) {
    http_response_code(400);
    exit('Identificador de nota no válido.');
}

$eliminar = $conexion->prepare(
    'DELETE n
     FROM tb_notas AS n
     INNER JOIN tb_alumnos AS a ON a.id_alumno = n.id_alumno
     INNER JOIN tb_curso_asignatura AS ca
        ON ca.id_curso_asignatura = n.id_curso_asignatura
        AND ca.id_curso = a.id_curso
     WHERE n.id_nota = :id_nota
        AND n.id_alumno = :id_alumno'
);
$eliminar->execute([
    'id_nota' => $idNota,
    'id_alumno' => $idAlumno,
]);

if ($eliminar->rowCount() !== 1) {
    http_response_code(404);
    exit('No se encontró la nota que quieres borrar.');
}

redirigirFichaProfesor($idAlumno, 'eliminada');
