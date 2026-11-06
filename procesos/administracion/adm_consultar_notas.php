<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

$adm_pagina = filter_input(INPUT_GET, 'adm_pagina', FILTER_VALIDATE_INT);
if (!is_int($adm_pagina) || $adm_pagina < 1) {
    $adm_pagina = 1;
}
$adm_id_alumno_filtro = filter_input(INPUT_GET, 'adm_id_alumno', FILTER_VALIDATE_INT);
if (!is_int($adm_id_alumno_filtro) || $adm_id_alumno_filtro < 1) {
    $adm_id_alumno_filtro = null;
}
$adm_busqueda = trim(is_string($_GET['adm_busqueda'] ?? null) ? $_GET['adm_busqueda'] : '');
if (preg_match('/^.{0,100}/us', $adm_busqueda, $adm_coincidencias_busqueda) === 1) {
    $adm_busqueda = $adm_coincidencias_busqueda[0];
} else {
    $adm_busqueda = '';
}
$adm_condiciones = [];
$adm_parametros = [];
if ($adm_id_alumno_filtro !== null) {
    $adm_condiciones[] = 'n.id_alumno = :id_alumno';
    $adm_parametros['id_alumno'] = $adm_id_alumno_filtro;
}
if ($adm_busqueda !== '') {
    $adm_condiciones[] = '(CONCAT_WS(\' \', u.nombre, u.apellidos) LIKE :nombre_completo
        OR u.nombre LIKE :nombre OR u.apellidos LIKE :apellidos
        OR u.email LIKE :email OR c.nombre LIKE :curso OR g.nombre LIKE :grado
        OR s.nombre LIKE :asignatura OR s.codigo LIKE :codigo
        OR CAST(n.evaluacion AS CHAR) LIKE :evaluacion
        OR CAST(n.nota AS CHAR) LIKE :nota OR n.observaciones LIKE :observaciones)';
    foreach (['nombre_completo', 'nombre', 'apellidos', 'email', 'curso', 'grado', 'asignatura', 'codigo', 'evaluacion', 'nota', 'observaciones'] as $adm_campo) {
        $adm_parametros[$adm_campo] = '%' . $adm_busqueda . '%';
    }
}
$adm_where = $adm_condiciones === [] ? '' : ' WHERE ' . implode(' AND ', $adm_condiciones);
$adm_por_pagina = 25;
$adm_alumnos = $conexion->query(
    'SELECT a.id_alumno,u.nombre,u.apellidos,c.nombre AS curso,g.nombre AS grado
     FROM tb_alumnos a
     INNER JOIN tb_usuarios u ON u.id_usuario=a.id_usuario
     INNER JOIN tb_curso c ON c.id_curso=a.id_curso
     INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     ORDER BY g.fecha_inicio DESC,c.nombre,u.apellidos'
)->fetchAll();
$adm_alumno_filtrado = null;
foreach ($adm_alumnos as $adm_alumno) {
    if ((int) $adm_alumno['id_alumno'] === $adm_id_alumno_filtro) {
        $adm_alumno_filtrado = $adm_alumno;
        break;
    }
}
$adm_total_consulta = $conexion->prepare(
    'SELECT COUNT(*)
     FROM tb_notas n
     INNER JOIN tb_alumnos a ON a.id_alumno=n.id_alumno
     INNER JOIN tb_usuarios u ON u.id_usuario=a.id_usuario
     INNER JOIN tb_curso c ON c.id_curso=a.id_curso
     INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     INNER JOIN tb_curso_asignatura ca ON ca.id_curso_asignatura=n.id_curso_asignatura
     INNER JOIN tb_asignatura s ON s.id_asignatura=ca.id_asignatura'
     . $adm_where
);
$adm_total_consulta->execute($adm_parametros);
$adm_total_notas = (int) $adm_total_consulta->fetchColumn();
$adm_numero_paginas = max(1, (int) ceil($adm_total_notas / $adm_por_pagina));
$adm_pagina = min($adm_pagina, $adm_numero_paginas);
$adm_offset = ($adm_pagina - 1) * $adm_por_pagina;
$adm_notas_consulta = $conexion->prepare(
    'SELECT n.id_nota,n.id_alumno,n.id_curso_asignatura,n.evaluacion,n.nota,n.observaciones,
            u.nombre AS alumno_nombre,u.apellidos AS alumno_apellidos,
            c.nombre AS curso_nombre,g.nombre AS grado_nombre,
            s.codigo AS asignatura_codigo,s.nombre AS asignatura_nombre
     FROM tb_notas n
     INNER JOIN tb_alumnos a ON a.id_alumno=n.id_alumno
     INNER JOIN tb_usuarios u ON u.id_usuario=a.id_usuario
     INNER JOIN tb_curso c ON c.id_curso=a.id_curso
     INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     INNER JOIN tb_curso_asignatura ca ON ca.id_curso_asignatura=n.id_curso_asignatura
     INNER JOIN tb_asignatura s ON s.id_asignatura=ca.id_asignatura
     ' . $adm_where . '
     ORDER BY g.fecha_inicio DESC,c.nombre,u.apellidos,s.nombre,n.evaluacion'
     . ' LIMIT :limite OFFSET :desplazamiento'
);
foreach ($adm_parametros as $adm_clave => $adm_valor) {
    $adm_notas_consulta->bindValue(':' . $adm_clave, $adm_valor, is_int($adm_valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$adm_notas_consulta->bindValue(':limite', $adm_por_pagina, PDO::PARAM_INT);
$adm_notas_consulta->bindValue(':desplazamiento', $adm_offset, PDO::PARAM_INT);
$adm_notas_consulta->execute();
$adm_notas = $adm_notas_consulta->fetchAll();
$adm_curso_asignaturas = $conexion->query(
    'SELECT ca.id_curso_asignatura,c.nombre AS curso,g.nombre AS grado,
            s.codigo,s.nombre AS asignatura
     FROM tb_curso_asignatura ca
     INNER JOIN tb_curso c ON c.id_curso=ca.id_curso
     INNER JOIN tb_grado_academico g ON g.id_grado=c.id_grado
     INNER JOIN tb_asignatura s ON s.id_asignatura=ca.id_asignatura
     ORDER BY g.fecha_inicio DESC,c.nombre,s.nombre'
)->fetchAll();
