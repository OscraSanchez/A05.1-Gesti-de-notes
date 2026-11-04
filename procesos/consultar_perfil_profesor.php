<?php
declare(strict_types=1);

require_once __DIR__ . '/acceso_profesor.php';

$consulta = $conexion->prepare(
    'SELECT nombre, apellidos, email
     FROM tb_usuarios
     WHERE id_usuario = :id_usuario
     LIMIT 1'
);
$consulta->execute(['id_usuario' => $_SESSION['id_usuario']]);
$perfil = $consulta->fetch();

if (!$perfil) {
    http_response_code(404);
    exit('No se encontró el perfil del profesor.');
}

$estado = $_GET['estado'] ?? '';
$estadosPermitidos = [
    'actualizado',
    'datos_invalidos',
    'email_existente',
    'contrasena_actual_incorrecta',
    'contrasenas_no_coinciden',
];
if (!is_string($estado) || !in_array($estado, $estadosPermitidos, true)) {
    $estado = '';
}

function escaparPerfilProfesor(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
