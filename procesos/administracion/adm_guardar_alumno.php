<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_id_alumno = filter_input(INPUT_POST, 'adm_id_alumno', FILTER_VALIDATE_INT);
$adm_id_usuario = filter_input(INPUT_POST, 'adm_id_usuario', FILTER_VALIDATE_INT);
$adm_id_curso = filter_input(INPUT_POST, 'adm_id_curso', FILTER_VALIDATE_INT);
$adm_dni = strtoupper(trim(is_string($_POST['adm_dni'] ?? null) ? $_POST['adm_dni'] : ''));
$adm_fecha_nacimiento = $_POST['adm_fecha_nacimiento'] ?? '';
$adm_nuevo = $adm_id_alumno === null || $adm_id_alumno === false || $adm_id_alumno === 0;

if (
    !is_int($adm_id_usuario) || $adm_id_usuario < 1
    ||     !is_int($adm_id_curso) || $adm_id_curso < 1
    || !preg_match('/^[0-9]{8}[A-Z]$/', $adm_dni)
    || !adm_fecha_valida($adm_fecha_nacimiento)
) {
    adm_redirigir('alumnos', 'datos_invalidos');
}

$adm_usuario_valido = $conexion->prepare(
    "SELECT u.id_usuario
     FROM tb_usuarios u INNER JOIN tb_roles r ON r.id_rol=u.id_rol
     WHERE u.id_usuario=:id_usuario AND r.nombre='alumno'"
);
$adm_usuario_valido->execute(['id_usuario' => $adm_id_usuario]);
if (!$adm_usuario_valido->fetch()) {
    adm_redirigir('alumnos', 'usuario_no_alumno');
}

try {
    if ($adm_nuevo) {
        $adm_guardar = $conexion->prepare(
            'INSERT INTO tb_alumnos (id_usuario,id_curso,dni,fecha_nacimiento)
             VALUES (:usuario,:curso,:dni,:fecha)'
        );
    } else {
        $adm_guardar = $conexion->prepare(
            'UPDATE tb_alumnos
             SET id_usuario=:usuario,id_curso=:curso,dni=:dni,fecha_nacimiento=:fecha
             WHERE id_alumno=:alumno'
        );
    }
    $adm_parametros = [
        'usuario' => $adm_id_usuario,
        'curso' => $adm_id_curso,
        'dni' => $adm_dni,
        'fecha' => $adm_fecha_nacimiento,
    ];
    if (!$adm_nuevo) {
        if (!is_int($adm_id_alumno) || $adm_id_alumno < 1) {
            adm_redirigir('alumnos', 'datos_invalidos');
        }
        $adm_parametros['alumno'] = $adm_id_alumno;
    }
    $adm_guardar->execute($adm_parametros);
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1062) {
        $adm_detalle = (string) ($adm_error->errorInfo[2] ?? '');
        adm_redirigir('alumnos', str_contains($adm_detalle, 'dni') ? 'dni_existente' : 'ficha_existente');
    }
    if (($adm_error->errorInfo[1] ?? null) === 1452) {
        adm_redirigir('alumnos', 'referencia_invalida');
    }
    if (($adm_error->errorInfo[1] ?? null) === 1451) {
        adm_redirigir('alumnos', 'curso_con_notas');
    }
    throw $adm_error;
}

adm_redirigir('alumnos', $adm_nuevo ? 'creado' : 'actualizado');
