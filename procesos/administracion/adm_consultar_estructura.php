<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

$adm_grados = $conexion->query(
    'SELECT id_grado, nombre, fecha_inicio, fecha_fin
     FROM tb_grado_academico ORDER BY fecha_inicio DESC'
)->fetchAll();
$adm_cursos = $conexion->query(
    'SELECT c.id_curso, c.id_grado, c.nombre, c.etapa, c.nivel, c.grupo,
            g.nombre AS grado_nombre
     FROM tb_curso c INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     ORDER BY g.fecha_inicio DESC, c.nombre'
)->fetchAll();
$adm_asignaturas = $conexion->query(
    'SELECT id_asignatura, codigo, nombre FROM tb_asignatura ORDER BY nombre'
)->fetchAll();
$adm_relaciones = $conexion->query(
    'SELECT ca.id_curso_asignatura, ca.id_curso, ca.id_asignatura, ca.id_profesor,
            c.nombre AS curso_nombre, g.nombre AS grado_nombre,
            s.nombre AS asignatura_nombre, s.codigo,
            u.nombre AS profesor_nombre, u.apellidos AS profesor_apellidos
     FROM tb_curso_asignatura ca
     INNER JOIN tb_curso c ON c.id_curso=ca.id_curso
     INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     INNER JOIN tb_asignatura s ON s.id_asignatura=ca.id_asignatura
     INNER JOIN tb_usuarios u ON u.id_usuario=ca.id_profesor
     ORDER BY g.fecha_inicio DESC, c.nombre, s.nombre'
)->fetchAll();
$adm_profesores = $conexion->query(
    "SELECT u.id_usuario, u.nombre, u.apellidos FROM tb_usuarios u
     INNER JOIN tb_roles r ON r.id_rol=u.id_rol
     WHERE r.nombre='profesor' ORDER BY u.apellidos, u.nombre"
)->fetchAll();
