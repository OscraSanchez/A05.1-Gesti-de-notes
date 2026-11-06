<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/administracion/adm_consultar_notas.php';

$adm_estado = $_GET['estado'] ?? '';
$adm_mensajes = [
    'creado' => 'La nota se ha creado.',
    'actualizado' => 'La nota se ha actualizado.',
    'eliminado' => 'La nota se ha eliminado.',
    'datos_invalidos' => 'Revisa alumno, asignatura, evaluación, nota y observaciones.',
    'referencia_invalida' => 'El alumno, asignatura o nota seleccionada no existe.',
    'nota_existente' => 'Ya existe una nota para ese alumno, asignatura y evaluación.',
];
if (!is_string($adm_estado) || !isset($adm_mensajes[$adm_estado])) {
    $adm_estado = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Notas | Administración Gnotas</title><link rel="stylesheet" href="../css/adm_administracion.css"></head>
<body>
<main class="adm-contenedor">
    <nav class="adm-barra">
        <a href="<?= $adm_alumno_filtrado !== null ? 'adm_alumnos.php#adm-alumno-' . (int) $adm_alumno_filtrado['id_alumno'] : 'adm_panel.php' ?>"><?= $adm_alumno_filtrado !== null ? '← Ficha del alumno' : '← Panel' ?></a>
        <form action="../../procesos/cerrar_sesion.php" method="post"><button class="adm-boton adm-boton-secundario">Cerrar sesión</button></form>
    </nav>
    <h1><?= $adm_alumno_filtrado !== null ? 'Notas de ' . adm_escapar($adm_alumno_filtrado['nombre'] . ' ' . $adm_alumno_filtrado['apellidos']) : 'Gestión de notas' ?></h1>
    <?php if ($adm_estado !== ''): ?><p class="adm-mensaje <?= in_array($adm_estado, ['datos_invalidos', 'referencia_invalida', 'nota_existente'], true) ? 'adm-mensaje-error' : 'adm-mensaje-exito' ?>" role="status"><?= adm_escapar($adm_mensajes[$adm_estado]) ?></p><?php endif; ?>
    <section class="adm-panel">
        <h2>Añadir nota</h2>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_nota.php" method="post">
            <input type="hidden" name="adm_id_nota" value="0">
            <label>Alumno<select name="adm_id_alumno" required><?php foreach ($adm_alumnos as $adm_alumno): ?><option value="<?= (int) $adm_alumno['id_alumno'] ?>" <?= (int) $adm_alumno['id_alumno'] === $adm_id_alumno_filtro ? 'selected' : '' ?>><?= adm_escapar($adm_alumno['apellidos'] . ', ' . $adm_alumno['nombre'] . ' · ' . $adm_alumno['grado'] . ' ' . $adm_alumno['curso']) ?></option><?php endforeach; ?></select></label>
            <label>Asignatura del curso<select name="adm_id_curso_asignatura" required><?php foreach ($adm_curso_asignaturas as $adm_relacion): ?><option value="<?= (int) $adm_relacion['id_curso_asignatura'] ?>"><?= adm_escapar($adm_relacion['grado'] . ' · ' . $adm_relacion['curso'] . ' · ' . $adm_relacion['codigo'] . ' ' . $adm_relacion['asignatura']) ?></option><?php endforeach; ?></select></label>
            <label>Evaluación<select name="adm_evaluacion" required><option value="1">1</option><option value="2">2</option><option value="3">3</option></select></label>
            <label>Nota<input type="number" name="adm_nota" min="0" max="10" step="0.01" required></label>
            <label class="adm-campo-completo">Observaciones<textarea name="adm_observaciones" maxlength="255" rows="2"></textarea></label>
            <button class="adm-boton" type="submit">Crear nota</button>
        </form>
    </section>
    <section class="adm-panel">
        <h2><?= $adm_alumno_filtrado !== null ? 'Notas del alumno' : 'Todas las notas' ?> (<?= $adm_total_notas ?>)</h2>
        <form class="adm-busqueda-form" method="get" role="search">
            <?php if ($adm_id_alumno_filtro !== null): ?><input type="hidden" name="adm_id_alumno" value="<?= $adm_id_alumno_filtro ?>"><?php endif; ?>
            <label class="adm-busqueda" for="adm-busqueda-notas">Buscar por alumno, asignatura, curso, evaluación o nota</label>
            <div class="adm-busqueda-controles">
                <input id="adm-busqueda-notas" type="search" name="adm_busqueda" value="<?= adm_escapar($adm_busqueda) ?>" maxlength="100" placeholder="Escribe para filtrar..." autocomplete="off">
                <button class="adm-boton" type="submit">Buscar</button>
                <?php if ($adm_busqueda !== ''): ?><a class="adm-boton adm-boton-secundario" href="?<?= $adm_id_alumno_filtro !== null ? 'adm_id_alumno=' . $adm_id_alumno_filtro : '' ?>">Limpiar</a><?php endif; ?>
            </div>
        </form>
        <p class="adm-subtitulo">Página <?= $adm_pagina ?> de <?= $adm_numero_paginas ?> · mostrando hasta <?= $adm_por_pagina ?> notas por página.</p>
        <div class="adm-registros">
        <?php foreach ($adm_notas as $adm_nota_fila): ?>
            <div class="adm-registro adm-nota-registro">
                <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_nota.php" method="post">
                    <input type="hidden" name="adm_id_nota" value="<?= (int) $adm_nota_fila['id_nota'] ?>">
                    <input type="hidden" name="adm_id_alumno" value="<?= (int) $adm_nota_fila['id_alumno'] ?>">
                    <input type="hidden" name="adm_id_curso_asignatura" value="<?= (int) $adm_nota_fila['id_curso_asignatura'] ?>">
                    <p class="adm-dato-nota"><strong>Alumno</strong><?= adm_escapar($adm_nota_fila['alumno_apellidos'] . ', ' . $adm_nota_fila['alumno_nombre'] . ' · ' . $adm_nota_fila['grado_nombre'] . ' ' . $adm_nota_fila['curso_nombre']) ?></p>
                    <p class="adm-dato-nota"><strong>Asignatura</strong><?= adm_escapar($adm_nota_fila['asignatura_codigo'] . ' · ' . $adm_nota_fila['asignatura_nombre']) ?></p>
                    <label>Evaluación<select name="adm_evaluacion"><?php for ($adm_eval = 1; $adm_eval <= 3; $adm_eval++): ?><option value="<?= $adm_eval ?>" <?= $adm_eval === (int) $adm_nota_fila['evaluacion'] ? 'selected' : '' ?>><?= $adm_eval ?></option><?php endfor; ?></select></label>
                    <label>Nota<input type="number" name="adm_nota" value="<?= adm_escapar($adm_nota_fila['nota']) ?>" min="0" max="10" step="0.01" required></label>
                    <label class="adm-campo-completo">Observaciones<textarea name="adm_observaciones" maxlength="255" rows="2"><?= adm_escapar($adm_nota_fila['observaciones'] ?? '') ?></textarea></label>
                    <button class="adm-boton" type="submit">Guardar cambios</button>
                </form>
                <form action="../../procesos/administracion/adm_eliminar_nota.php" method="post" onsubmit="return confirm('¿Eliminar esta nota definitivamente?');"><input type="hidden" name="adm_id_nota" value="<?= (int) $adm_nota_fila['id_nota'] ?>"><button class="adm-boton adm-boton-peligro" type="submit">Eliminar nota</button></form>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if ($adm_notas === []): ?><p class="adm-busqueda-vacio">No se encontraron notas con esos criterios.</p><?php endif; ?>
        <?php if ($adm_numero_paginas > 1): ?>
            <nav class="adm-paginacion" aria-label="Paginación de notas">
                <?php if ($adm_pagina > 1): ?><a href="?<?= $adm_id_alumno_filtro !== null ? 'adm_id_alumno=' . $adm_id_alumno_filtro . '&amp;' : '' ?><?= $adm_busqueda !== '' ? 'adm_busqueda=' . rawurlencode($adm_busqueda) . '&amp;' : '' ?>adm_pagina=<?= $adm_pagina - 1 ?>">← Anterior</a><?php endif; ?>
                <span>Página <?= $adm_pagina ?> / <?= $adm_numero_paginas ?></span>
                <?php if ($adm_pagina < $adm_numero_paginas): ?><a href="?<?= $adm_id_alumno_filtro !== null ? 'adm_id_alumno=' . $adm_id_alumno_filtro . '&amp;' : '' ?><?= $adm_busqueda !== '' ? 'adm_busqueda=' . rawurlencode($adm_busqueda) . '&amp;' : '' ?>adm_pagina=<?= $adm_pagina + 1 ?>">Siguiente →</a><?php endif; ?>
            </nav>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
