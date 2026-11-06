<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_id_usuario = filter_input(INPUT_POST, 'adm_id_usuario', FILTER_VALIDATE_INT);
$adm_id_rol = filter_input(INPUT_POST, 'adm_id_rol', FILTER_VALIDATE_INT);
$adm_nombre = trim(is_string($_POST['adm_nombre'] ?? null) ? $_POST['adm_nombre'] : '');
$adm_apellidos = trim(is_string($_POST['adm_apellidos'] ?? null) ? $_POST['adm_apellidos'] : '');
$adm_email = trim(is_string($_POST['adm_email'] ?? null) ? $_POST['adm_email'] : '');
$adm_password = $_POST['adm_password'] ?? null;
$adm_nuevo = $adm_id_usuario === null || $adm_id_usuario === false || $adm_id_usuario === 0;

if (
    ($adm_id_usuario !== false && $adm_id_usuario !== null && $adm_id_usuario < 0)
    || !is_int($adm_id_rol) || $adm_id_rol < 1
    || $adm_nombre === '' || strlen($adm_nombre) > 60
    || $adm_apellidos === '' || strlen($adm_apellidos) > 100
    || strlen($adm_email) > 120 || !filter_var($adm_email, FILTER_VALIDATE_EMAIL)
    || !is_string($adm_password)
    || ($adm_nuevo && (strlen($adm_password) < 8 || strlen($adm_password) > 72))
    || (!$adm_nuevo && $adm_password !== '' && (strlen($adm_password) < 8 || strlen($adm_password) > 72))
) {
    adm_redirigir('usuarios', 'datos_invalidos');
}

$adm_rol_existe = $conexion->prepare('SELECT COUNT(*) FROM tb_roles WHERE id_rol = :id_rol');
$adm_rol_existe->execute(['id_rol' => $adm_id_rol]);
if ((int) $adm_rol_existe->fetchColumn() !== 1) {
    adm_redirigir('usuarios', 'datos_invalidos');
}

if (!$adm_nuevo) {
    $adm_usuario_actual = $conexion->prepare(
        'SELECT u.id_rol, r.nombre AS rol
         FROM tb_usuarios AS u
         INNER JOIN tb_roles AS r ON r.id_rol = u.id_rol
         WHERE u.id_usuario = :id_usuario'
    );
    $adm_usuario_actual->execute(['id_usuario' => $adm_id_usuario]);
    $adm_usuario_actual_fila = $adm_usuario_actual->fetch();
    if (!$adm_usuario_actual_fila) {
        adm_redirigir('usuarios', 'datos_invalidos');
    }

    if ((int) $adm_usuario_actual_fila['id_rol'] !== $adm_id_rol) {
        if (
            $adm_id_usuario === $_SESSION['adm_id_usuario']
            && $adm_id_rol !== (int) $adm_usuario_actual_fila['id_rol']
        ) {
            adm_redirigir('usuarios', 'autoproteccion');
        }

        $adm_rol_nombre_consulta = $conexion->prepare('SELECT nombre FROM tb_roles WHERE id_rol = :id_rol');
        $adm_rol_nombre_consulta->execute(['id_rol' => $adm_id_rol]);
        $adm_nuevo_rol = $adm_rol_nombre_consulta->fetchColumn();
        if ($adm_usuario_actual_fila['rol'] === 'administrador' && $adm_nuevo_rol !== 'administrador') {
            $adm_total_admins = (int) $conexion->query(
                "SELECT COUNT(*) FROM tb_usuarios u INNER JOIN tb_roles r ON r.id_rol=u.id_rol WHERE r.nombre='administrador'"
            )->fetchColumn();
            if ($adm_total_admins <= 1) {
                adm_redirigir('usuarios', 'ultimo_admin');
            }
        }

        $adm_dependencia_rol = $conexion->prepare(
            'SELECT
                EXISTS(SELECT 1 FROM tb_alumnos WHERE id_usuario = :id_alumno)
                OR EXISTS(SELECT 1 FROM tb_curso_asignatura WHERE id_profesor = :id_profesor)'
        );
        $adm_dependencia_rol->execute([
            'id_alumno' => $adm_id_usuario,
            'id_profesor' => $adm_id_usuario,
        ]);
        if ((int) $adm_dependencia_rol->fetchColumn() === 1) {
            $adm_nuevo_rol_consulta = $conexion->prepare('SELECT nombre FROM tb_roles WHERE id_rol = :id_rol');
            $adm_nuevo_rol_consulta->execute(['id_rol' => $adm_id_rol]);
            $adm_nuevo_rol = $adm_nuevo_rol_consulta->fetchColumn();
            if (
                ($adm_usuario_actual_fila['rol'] === 'alumno' && $adm_nuevo_rol !== 'alumno')
                || ($adm_usuario_actual_fila['rol'] === 'profesor' && $adm_nuevo_rol !== 'profesor')
            ) {
                adm_redirigir('usuarios', 'rol_conflictivo');
            }
        }
    }
}

try {
    if ($adm_nuevo) {
        $adm_guardar = $conexion->prepare(
            'INSERT INTO tb_usuarios (id_rol, nombre, apellidos, email, password)
             VALUES (:id_rol, :nombre, :apellidos, :email, :password)'
        );
        $adm_guardar->execute([
            'id_rol' => $adm_id_rol,
            'nombre' => $adm_nombre,
            'apellidos' => $adm_apellidos,
            'email' => $adm_email,
            'password' => password_hash($adm_password, PASSWORD_DEFAULT),
        ]);
    } elseif ($adm_password !== '') {
        $adm_guardar = $conexion->prepare(
            'UPDATE tb_usuarios
             SET id_rol=:id_rol, nombre=:nombre, apellidos=:apellidos,
                 email=:email, password=:password
             WHERE id_usuario=:id_usuario'
        );
        $adm_guardar->execute([
            'id_rol' => $adm_id_rol,
            'nombre' => $adm_nombre,
            'apellidos' => $adm_apellidos,
            'email' => $adm_email,
            'password' => password_hash($adm_password, PASSWORD_DEFAULT),
            'id_usuario' => $adm_id_usuario,
        ]);
    } else {
        $adm_guardar = $conexion->prepare(
            'UPDATE tb_usuarios
             SET id_rol=:id_rol, nombre=:nombre, apellidos=:apellidos, email=:email
             WHERE id_usuario=:id_usuario'
        );
        $adm_guardar->execute([
            'id_rol' => $adm_id_rol,
            'nombre' => $adm_nombre,
            'apellidos' => $adm_apellidos,
            'email' => $adm_email,
            'id_usuario' => $adm_id_usuario,
        ]);
    }
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1062) {
        adm_redirigir('usuarios', 'email_existente');
    }
    if (($adm_error->errorInfo[1] ?? null) === 1644) {
        adm_redirigir('usuarios', 'rol_conflictivo');
    }
    throw $adm_error;
}

adm_redirigir('usuarios', $adm_nuevo ? 'creado' : 'actualizado');
