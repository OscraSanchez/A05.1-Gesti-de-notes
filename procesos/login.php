<?php
declare(strict_types=1);

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!is_string($email) || !is_string($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../vistas/login.php?error=1');
    exit;
}

require_once __DIR__ . '/../conexion/conexion.php';

$consulta = $conexion->prepare(
    'SELECT u.id_usuario, u.password, r.nombre AS rol_usuario
     FROM tb_usuarios AS u
     INNER JOIN tb_roles AS r ON r.id_rol = u.id_rol
     WHERE u.email = :email
     LIMIT 1'
);
$consulta->execute(['email' => $email]);
$usuario = $consulta->fetch();

if (!$usuario || !password_verify($password, $usuario['password'])) {
    header('Location: ../vistas/login.php?error=1');
    exit;
}

session_regenerate_id(true);
$_SESSION = [];

if ($usuario['rol_usuario'] === 'administrador') {
    $_SESSION['adm_id_usuario'] = (int) $usuario['id_usuario'];
    $_SESSION['adm_rol_usuario'] = $usuario['rol_usuario'];
} else {
    $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
    $_SESSION['rol_usuario'] = $usuario['rol_usuario'];
}

header('Location: resolver_destino_despues_del_login_segun_rol.php');
exit;
