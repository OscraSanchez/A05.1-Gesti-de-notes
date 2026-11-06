<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_tipo = $_POST['adm_tipo'] ?? '';
$adm_id = filter_input(INPUT_POST, 'adm_id', FILTER_VALIDATE_INT);
$adm_id = is_int($adm_id) && $adm_id > 0 ? $adm_id : null;
$adm_nuevo = $adm_id === null;

try {
    if ($adm_tipo === 'grado') {
        $adm_nombre = trim(is_string($_POST['adm_nombre'] ?? null) ? $_POST['adm_nombre'] : '');
        $adm_fecha_inicio = $_POST['adm_fecha_inicio'] ?? '';
        $adm_fecha_fin = $_POST['adm_fecha_fin'] ?? '';
        $adm_fecha_inicio_valida = adm_fecha_valida($adm_fecha_inicio);
        $adm_fecha_fin_valida = adm_fecha_valida($adm_fecha_fin);
        if (
            !preg_match('/^\d{4}-\d{4}$/', $adm_nombre)
            || strlen($adm_nombre) > 9
            || !$adm_fecha_inicio_valida || !$adm_fecha_fin_valida
            || $adm_fecha_inicio > $adm_fecha_fin
        ) {
            adm_redirigir('estructura', 'datos_invalidos');
        }
        $adm_sql = $adm_nuevo
            ? 'INSERT INTO tb_grado_academico (nombre,fecha_inicio,fecha_fin) VALUES (:nombre,:inicio,:fin)'
            : 'UPDATE tb_grado_academico SET nombre=:nombre,fecha_inicio=:inicio,fecha_fin=:fin WHERE id_grado=:id';
        $adm_guardar = $conexion->prepare($adm_sql);
        $adm_parametros = ['nombre' => $adm_nombre, 'inicio' => $adm_fecha_inicio, 'fin' => $adm_fecha_fin];
        if (!$adm_nuevo) {
            $adm_parametros['id'] = $adm_id;
        }
        $adm_guardar->execute($adm_parametros);
    } elseif ($adm_tipo === 'curso') {
        $adm_id_grado = filter_input(INPUT_POST, 'adm_id_grado', FILTER_VALIDATE_INT);
        $adm_nombre = trim(is_string($_POST['adm_nombre'] ?? null) ? $_POST['adm_nombre'] : '');
        $adm_etapa = trim(is_string($_POST['adm_etapa'] ?? null) ? $_POST['adm_etapa'] : '');
        $adm_nivel = filter_input(INPUT_POST, 'adm_nivel', FILTER_VALIDATE_INT);
        $adm_grupo = strtoupper(trim(is_string($_POST['adm_grupo'] ?? null) ? $_POST['adm_grupo'] : ''));
        if (
            !is_int($adm_id_grado) || $adm_id_grado < 1
            || $adm_nombre === '' || strlen($adm_nombre) > 60
            || $adm_etapa === '' || strlen($adm_etapa) > 40
            || !is_int($adm_nivel) || $adm_nivel < 0 || $adm_nivel > 255
            || !preg_match('/^[A-Z]$/', $adm_grupo)
        ) {
            adm_redirigir('estructura', 'datos_invalidos');
        }
        $adm_sql = $adm_nuevo
            ? 'INSERT INTO tb_curso (id_grado,nombre,etapa,nivel,grupo) VALUES (:grado,:nombre,:etapa,:nivel,:grupo)'
            : 'UPDATE tb_curso SET id_grado=:grado,nombre=:nombre,etapa=:etapa,nivel=:nivel,grupo=:grupo WHERE id_curso=:id';
        $adm_guardar = $conexion->prepare($adm_sql);
        $adm_parametros = ['grado' => $adm_id_grado, 'nombre' => $adm_nombre, 'etapa' => $adm_etapa, 'nivel' => $adm_nivel, 'grupo' => $adm_grupo];
        if (!$adm_nuevo) {
            $adm_parametros['id'] = $adm_id;
        }
        $adm_guardar->execute($adm_parametros);
    } elseif ($adm_tipo === 'asignatura') {
        $adm_codigo = strtoupper(trim(is_string($_POST['adm_codigo'] ?? null) ? $_POST['adm_codigo'] : ''));
        $adm_nombre = trim(is_string($_POST['adm_nombre'] ?? null) ? $_POST['adm_nombre'] : '');
        if (!preg_match('/^[A-Z0-9_-]{1,10}$/', $adm_codigo) || $adm_nombre === '' || strlen($adm_nombre) > 80) {
            adm_redirigir('estructura', 'datos_invalidos');
        }
        $adm_sql = $adm_nuevo
            ? 'INSERT INTO tb_asignatura (codigo,nombre) VALUES (:codigo,:nombre)'
            : 'UPDATE tb_asignatura SET codigo=:codigo,nombre=:nombre WHERE id_asignatura=:id';
        $adm_guardar = $conexion->prepare($adm_sql);
        $adm_parametros = ['codigo' => $adm_codigo, 'nombre' => $adm_nombre];
        if (!$adm_nuevo) {
            $adm_parametros['id'] = $adm_id;
        }
        $adm_guardar->execute($adm_parametros);
    } elseif ($adm_tipo === 'curso_asignatura') {
        $adm_id_curso = filter_input(INPUT_POST, 'adm_id_curso', FILTER_VALIDATE_INT);
        $adm_id_asignatura = filter_input(INPUT_POST, 'adm_id_asignatura', FILTER_VALIDATE_INT);
        $adm_id_profesor = filter_input(INPUT_POST, 'adm_id_profesor', FILTER_VALIDATE_INT);
        if (
            !is_int($adm_id_curso) || $adm_id_curso < 1
            || !is_int($adm_id_asignatura) || $adm_id_asignatura < 1
            || !is_int($adm_id_profesor) || $adm_id_profesor < 1
        ) {
            adm_redirigir('estructura', 'datos_invalidos');
        }
        $adm_sql = $adm_nuevo
            ? 'INSERT INTO tb_curso_asignatura (id_curso,id_asignatura,id_profesor) VALUES (:curso,:asignatura,:profesor)'
            : 'UPDATE tb_curso_asignatura SET id_curso=:curso,id_asignatura=:asignatura,id_profesor=:profesor WHERE id_curso_asignatura=:id';
        $adm_guardar = $conexion->prepare($adm_sql);
        $adm_parametros = ['curso' => $adm_id_curso, 'asignatura' => $adm_id_asignatura, 'profesor' => $adm_id_profesor];
        if (!$adm_nuevo) {
            $adm_parametros['id'] = $adm_id;
        }
        $adm_guardar->execute($adm_parametros);
    } else {
        adm_redirigir('estructura', 'datos_invalidos');
    }
} catch (PDOException $adm_error) {
    if (($adm_error->errorInfo[1] ?? null) === 1062) {
        adm_redirigir('estructura', 'duplicado');
    }
    if (($adm_error->errorInfo[1] ?? null) === 1452) {
        adm_redirigir('estructura', 'referencia_invalida');
    }
    if (($adm_error->errorInfo[1] ?? null) === 1644) {
        adm_redirigir('estructura', 'profesor_invalido');
    }
    throw $adm_error;
}

adm_redirigir('estructura', $adm_nuevo ? 'creado' : 'actualizado');
