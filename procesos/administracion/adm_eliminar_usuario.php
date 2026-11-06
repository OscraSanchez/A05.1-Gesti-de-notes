<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_id_usuario = filter_input(INPUT_POST, 'adm_id_usuario', FILTER_VALIDATE_INT);
if (!is_int($adm_id_usuario) || $adm_id_usuario < 1) {
    adm_redirigir('usuarios', 'datos_invalidos');
}
if ($adm_id_usuario === $_SESSION['adm_id_usuario']) {
    adm_redirigir('usuarios', 'autoproteccion');
}

$adm_destino = $conexion->prepare(
    'SELECT r.nombre
     FROM tb_usuarios u
     INNER JOIN tb_roles r ON r.id_rol=u.id_rol
     WHERE u.id_usuario=:id_usuario'
);
$adm_destino->execute(['id_usuario' => $adm_id_usuario]);
$adm_rol_destino = $adm_destino->fetchColumn();
if (!$adm_rol_destino) {
    adm_redirigir('usuarios', 'datos_invalidos');
}
if ($adm_rol_destino === 'administrador') {
    $adm_total_admins = (int) $conexion->query(
        "SELECT COUNT(*) FROM tb_usuarios u INNER JOIN tb_roles r ON r.id_rol=u.id_rol WHERE r.nombre='administrador'"
    )->fetchColumn();
    if ($adm_total_admins <= 1) {
        adm_redirigir('usuarios', 'ultimo_admin');
    }
}

try {
    $adm_eliminar = $conexion->prepare('DELETE FROM tb_usuarios WHERE id_usuario = :id_usuario');
    $adm_eliminar->execute(['id_usuario' => $adm_id_usuario]);
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1451) {
        adm_redirigir('usuarios', 'dependencias');
    }
    throw $adm_error;
}

adm_redirigir('usuarios', 'eliminado');
