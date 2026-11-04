<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['adm_id_usuario'], $_SESSION['adm_rol_usuario'])
    || !is_int($_SESSION['adm_id_usuario'])
    || $_SESSION['adm_rol_usuario'] !== 'administrador'
) {
    http_response_code(403);
    exit('Acceso restringido a administración.');
}

header('Cache-Control: no-store, private');
require_once __DIR__ . '/../../conexion/conexion.php';

$adm_verificar = $conexion->prepare(
    'SELECT r.nombre
     FROM tb_usuarios AS u
     INNER JOIN tb_roles AS r ON r.id_rol = u.id_rol
     WHERE u.id_usuario = :id_usuario
     LIMIT 1'
);
$adm_verificar->execute(['id_usuario' => $_SESSION['adm_id_usuario']]);
if ($adm_verificar->fetchColumn() !== 'administrador') {
    $_SESSION = [];
    session_destroy();
    http_response_code(403);
    exit('El acceso administrativo ya no está autorizado.');
}

function adm_escapar(mixed $adm_valor): string
{
    return htmlspecialchars((string) $adm_valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function adm_fecha_valida(mixed $adm_fecha): bool
{
    if (!is_string($adm_fecha)) {
        return false;
    }

    $adm_fecha_parseada = DateTimeImmutable::createFromFormat('!Y-m-d', $adm_fecha);
    $adm_errores = DateTimeImmutable::getLastErrors();
    return $adm_fecha_parseada !== false
        && $adm_fecha_parseada->format('Y-m-d') === $adm_fecha
        && ($adm_errores === false || ($adm_errores['warning_count'] === 0 && $adm_errores['error_count'] === 0));
}

function adm_redirigir(string $adm_vista, string $adm_estado): never
{
    $adm_vistas = [
        'panel' => 'adm_panel.php',
        'usuarios' => 'adm_usuarios.php',
        'alumnos' => 'adm_alumnos.php',
        'estructura' => 'adm_estructura.php',
        'notas' => 'adm_notas.php',
    ];
    if (!isset($adm_vistas[$adm_vista])) {
        throw new LogicException('Vista administrativa desconocida.');
    }

    header(
        'Location: ../../vistas/administracion/' . $adm_vistas[$adm_vista]
        . '?estado=' . rawurlencode($adm_estado)
    );
    exit;
}
