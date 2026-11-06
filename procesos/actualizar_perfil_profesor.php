<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

$nombre = trim(is_string($_POST['nombre'] ?? null) ? $_POST['nombre'] : '');
$apellidos = trim(is_string($_POST['apellidos'] ?? null) ? $_POST['apellidos'] : '');
$email = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
$contrasenaActual = $_POST['contrasena_actual'] ?? '';
$nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
$confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

$longitudNombre = preg_match_all('/./us', $nombre, $coincidenciasNombre);
$longitudApellidos = preg_match_all('/./us', $apellidos, $coincidenciasApellidos);
$cambiarContrasena = $nuevaContrasena !== '' || $confirmarContrasena !== '' || $contrasenaActual !== '';

if (
    $nombre === ''
    || $apellidos === ''
    || $longitudNombre === false || $longitudNombre > 60
    || $longitudApellidos === false || $longitudApellidos > 100
    || strlen($email) > 120
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !is_string($contrasenaActual)
    || !is_string($nuevaContrasena)
    || !is_string($confirmarContrasena)
    || ($cambiarContrasena && (strlen($nuevaContrasena) < 8 || strlen($nuevaContrasena) > 72))
) {
    header('Location: ../vistas/profesores/editar_perfil.php?estado=datos_invalidos');
    exit;
}

if ($cambiarContrasena && !hash_equals($nuevaContrasena, $confirmarContrasena)) {
    header('Location: ../vistas/profesores/editar_perfil.php?estado=contrasenas_no_coinciden');
    exit;
}

$comprobarEmail = $conexion->prepare(
    'SELECT id_usuario
     FROM tb_usuarios
     WHERE email = :email AND id_usuario <> :id_usuario
     LIMIT 1'
);
$comprobarEmail->execute([
    'email' => $email,
    'id_usuario' => $_SESSION['id_usuario'],
]);
if ($comprobarEmail->fetch()) {
    header('Location: ../vistas/profesores/editar_perfil.php?estado=email_existente');
    exit;
}

try {
    if ($cambiarContrasena) {
        $consultaContrasena = $conexion->prepare(
            'SELECT password FROM tb_usuarios WHERE id_usuario = :id_usuario LIMIT 1'
        );
        $consultaContrasena->execute(['id_usuario' => $_SESSION['id_usuario']]);
        $hashActual = $consultaContrasena->fetchColumn();

        if (!is_string($hashActual) || !password_verify($contrasenaActual, $hashActual)) {
            header('Location: ../vistas/profesores/editar_perfil.php?estado=contrasena_actual_incorrecta');
            exit;
        }

        $actualizar = $conexion->prepare(
            'UPDATE tb_usuarios
             SET nombre = :nombre,
                 apellidos = :apellidos,
                 email = :email,
                 password = :password
             WHERE id_usuario = :id_usuario'
        );
        $actualizar->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'password' => password_hash($nuevaContrasena, PASSWORD_DEFAULT),
            'id_usuario' => $_SESSION['id_usuario'],
        ]);
    } else {
        $actualizar = $conexion->prepare(
            'UPDATE tb_usuarios
             SET nombre = :nombre,
                 apellidos = :apellidos,
                 email = :email
             WHERE id_usuario = :id_usuario'
        );
        $actualizar->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'id_usuario' => $_SESSION['id_usuario'],
        ]);
    }
} catch (PDOException $error) {
    if (($error->errorInfo[1] ?? null) === 1062) {
        header('Location: ../vistas/profesores/editar_perfil.php?estado=email_existente');
        exit;
    }
    throw $error;
}

header('Location: ../vistas/profesores/editar_perfil.php?estado=actualizado');
exit;
