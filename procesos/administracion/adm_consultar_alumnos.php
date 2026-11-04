<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

$adm_alumnos = $conexion->query(
    "SELECT a.id_alumno, a.id_usuario, a.id_curso, a.dni, a.fecha_nacimiento,
            u.nombre, u.apellidos, u.email, c.nombre AS curso_nombre,
            g.nombre AS grado_nombre
     FROM tb_alumnos a
     INNER JOIN tb_usuarios u ON u.id_usuario=a.id_usuario
     INNER JOIN tb_curso c ON c.id_curso=a.id_curso
     INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     ORDER BY g.fecha_inicio DESC, c.nombre, u.apellidos"
)->fetchAll();
$adm_usuarios_alumno = $conexion->query(
    "SELECT u.id_usuario,u.nombre,u.apellidos,u.email
     FROM tb_usuarios u INNER JOIN tb_roles r ON r.id_rol=u.id_rol
     LEFT JOIN tb_alumnos a ON a.id_usuario=u.id_usuario
     WHERE r.nombre='alumno' AND a.id_alumno IS NULL
     ORDER BY u.apellidos,u.nombre"
)->fetchAll();
$adm_cursos = $conexion->query(
    'SELECT c.id_curso,c.nombre,g.nombre AS grado_nombre
     FROM tb_curso c INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     ORDER BY g.fecha_inicio DESC,c.nombre'
)->fetchAll();
