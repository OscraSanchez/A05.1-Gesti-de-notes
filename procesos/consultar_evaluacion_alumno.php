<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])) {
    header('Location: ../vistas/login.php');
    exit;
}

if ($_SESSION['rol_usuario'] !== 'alumno' || !is_int($_SESSION['id_usuario'])) {
    http_response_code(403);
    exit('Acceso restringido al alumnado.');
}

header('Cache-Control: no-store, private');

require_once __DIR__ . '/../conexion/conexion.php';

$consulta = $conexion->prepare(
    'SELECT
        u.id_usuario,
        u.nombre,
        u.apellidos,
        r.nombre AS rol_usuario,
        a.id_alumno,
        c.nombre AS curso_nombre,
        c.etapa,
        c.nivel,
        c.grupo,
        g.nombre AS grado_academico,
        s.id_asignatura,
        s.codigo AS asignatura_codigo,
        s.nombre AS asignatura_nombre,
        MAX(CASE WHEN n.evaluacion = 1 THEN n.nota END) AS evaluacion_1,
        MAX(CASE WHEN n.evaluacion = 2 THEN n.nota END) AS evaluacion_2,
        MAX(CASE WHEN n.evaluacion = 3 THEN n.nota END) AS evaluacion_3,
        AVG(n.nota) AS media_asignatura
     FROM tb_usuarios AS u
     INNER JOIN tb_roles AS r ON r.id_rol = u.id_rol
     LEFT JOIN tb_alumnos AS a ON a.id_usuario = u.id_usuario
     LEFT JOIN tb_curso AS c ON c.id_curso = a.id_curso
     LEFT JOIN tb_grado_academico AS g ON g.id_grado = c.id_grado
     LEFT JOIN tb_curso_asignatura AS ca ON ca.id_curso = c.id_curso
     LEFT JOIN tb_asignatura AS s ON s.id_asignatura = ca.id_asignatura
     LEFT JOIN tb_notas AS n
        ON n.id_alumno = a.id_alumno
        AND n.id_curso_asignatura = ca.id_curso_asignatura
     WHERE u.id_usuario = :id_usuario
        AND r.nombre = :rol_usuario
     GROUP BY
        u.id_usuario, u.nombre, u.apellidos, r.nombre,
        a.id_alumno, c.nombre, c.etapa, c.nivel, c.grupo, g.nombre,
        s.id_asignatura, s.codigo, s.nombre
     ORDER BY s.nombre'
);
$consulta->execute([
    'id_usuario' => $_SESSION['id_usuario'],
    'rol_usuario' => 'alumno',
]);
$filas = $consulta->fetchAll();

if ($filas === []) {
    http_response_code(403);
    exit('La cuenta no tiene acceso al área de alumnado.');
}

$alumno = $filas[0];
$asignaturas = [];
foreach ($filas as $fila) {
    if ($fila['id_asignatura'] !== null) {
        $asignaturas[] = $fila;
    }
}

$medias = array_filter(
    array_column($asignaturas, 'media_asignatura'),
    static fn (mixed $media): bool => $media !== null
);
$mediaGeneral = $medias === [] ? null : array_sum($medias) / count($medias);

function escapar(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function mostrarNota(mixed $nota): string
{
    return $nota === null ? '—' : number_format((float) $nota, 2, ',', '.');
}
