<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_id_nota = filter_input(INPUT_POST, 'adm_id_nota', FILTER_VALIDATE_INT);
$adm_id_alumno = filter_input(INPUT_POST, 'adm_id_alumno', FILTER_VALIDATE_INT);
$adm_id_curso_asignatura = filter_input(INPUT_POST, 'adm_id_curso_asignatura', FILTER_VALIDATE_INT);
$adm_evaluacion = filter_input(INPUT_POST, 'adm_evaluacion', FILTER_VALIDATE_INT);
$adm_nota = $_POST['adm_nota'] ?? null;
$adm_observaciones = $_POST['adm_observaciones'] ?? '';
$adm_nuevo = $adm_id_nota === null || $adm_id_nota === false || $adm_id_nota === 0;
$adm_longitud_observaciones = is_string($adm_observaciones)
    ? preg_match_all('/./us', $adm_observaciones, $adm_coincidencias)
    : false;

if (
    !is_int($adm_id_alumno) || $adm_id_alumno < 1
    || !is_int($adm_id_curso_asignatura) || $adm_id_curso_asignatura < 1
    || !is_int($adm_evaluacion) || $adm_evaluacion < 1 || $adm_evaluacion > 3
    || !is_string($adm_nota)
    || !preg_match('/^(?:10(?:\.0{1,2})?|[0-9](?:\.[0-9]{1,2})?)$/', $adm_nota)
    || !is_string($adm_observaciones)
    || $adm_longitud_observaciones === false || $adm_longitud_observaciones > 255
) {
    adm_redirigir('notas', 'datos_invalidos');
}
if (!$adm_nuevo && (!is_int($adm_id_nota) || $adm_id_nota < 1)) {
    adm_redirigir('notas', 'datos_invalidos');
}

$adm_relacion = $conexion->prepare(
    'SELECT a.id_curso
     FROM tb_alumnos a
     INNER JOIN tb_curso_asignatura ca ON ca.id_curso=a.id_curso
     WHERE a.id_alumno=:id_alumno AND ca.id_curso_asignatura=:id_curso_asignatura'
);
$adm_relacion->execute([
    'id_alumno' => $adm_id_alumno,
    'id_curso_asignatura' => $adm_id_curso_asignatura,
]);
$adm_id_curso = $adm_relacion->fetchColumn();
if ($adm_id_curso === false) {
    adm_redirigir('notas', 'referencia_invalida');
}

try {
    if ($adm_nuevo) {
        $adm_guardar = $conexion->prepare(
            'INSERT INTO tb_notas
                (id_alumno,id_curso_asignatura,id_curso,evaluacion,nota,observaciones)
             VALUES
                (:alumno,:curso_asignatura,:curso,:evaluacion,:nota,:observaciones)'
        );
        $adm_guardar->execute([
            'alumno' => $adm_id_alumno,
            'curso_asignatura' => $adm_id_curso_asignatura,
            'curso' => $adm_id_curso,
            'evaluacion' => $adm_evaluacion,
            'nota' => $adm_nota,
            'observaciones' => $adm_observaciones === '' ? null : $adm_observaciones,
        ]);
    } else {
        $adm_guardar = $conexion->prepare(
            'UPDATE tb_notas
             SET id_alumno=:alumno,id_curso_asignatura=:curso_asignatura,id_curso=:curso,
                 evaluacion=:evaluacion,nota=:nota,observaciones=:observaciones
             WHERE id_nota=:id_nota'
        );
        $adm_guardar->execute([
            'alumno' => $adm_id_alumno,
            'curso_asignatura' => $adm_id_curso_asignatura,
            'curso' => $adm_id_curso,
            'evaluacion' => $adm_evaluacion,
            'nota' => $adm_nota,
            'observaciones' => $adm_observaciones === '' ? null : $adm_observaciones,
            'id_nota' => $adm_id_nota,
        ]);
        if ($adm_guardar->rowCount() === 0) {
            $adm_existe = $conexion->prepare('SELECT COUNT(*) FROM tb_notas WHERE id_nota=:id_nota');
            $adm_existe->execute(['id_nota' => $adm_id_nota]);
            if ((int) $adm_existe->fetchColumn() !== 1) {
                adm_redirigir('notas', 'referencia_invalida');
            }
        }
    }
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1062) {
        adm_redirigir('notas', 'nota_existente');
    }
    throw $adm_error;
}

adm_redirigir('notas', $adm_nuevo ? 'creado' : 'actualizado');
