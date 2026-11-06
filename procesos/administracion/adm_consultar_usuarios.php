<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

$adm_usuarios_consulta = $conexion->query(
    'SELECT u.id_usuario, u.id_rol, r.nombre AS nombre_rol,
            u.nombre, u.apellidos, u.email
     FROM tb_usuarios AS u
     INNER JOIN tb_roles AS r ON r.id_rol = u.id_rol
     ORDER BY r.id_rol, u.apellidos, u.nombre'
);
$adm_usuarios = $adm_usuarios_consulta->fetchAll();
$adm_roles_consulta = $conexion->query('SELECT id_rol, nombre FROM tb_roles ORDER BY id_rol');
$adm_roles = $adm_roles_consulta->fetchAll();
