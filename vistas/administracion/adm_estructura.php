<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/administracion/adm_consultar_estructura.php';

$adm_estado = $_GET['estado'] ?? '';
$adm_mensajes = [
    'creado' => 'El registro se ha creado.',
    'actualizado' => 'Los cambios se han guardado.',
    'eliminado' => 'El registro se ha eliminado.',
    'datos_invalidos' => 'Revisa los datos del formulario.',
    'duplicado' => 'Ya existe un registro con esos datos.',
    'referencia_invalida' => 'Uno de los elementos seleccionados ya no existe.',
    'profesor_invalido' => 'La persona seleccionada no tiene el rol de profesor.',
    'dependencias' => 'No se puede borrar porque hay datos relacionados. Elimina o reasigna primero las dependencias.',
];
if (!is_string($adm_estado) || !isset($adm_mensajes[$adm_estado])) {
    $adm_estado = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estructura académica | Gnotas</title>
    <link rel="stylesheet" href="../css/adm_administracion.css">
</head>
<body>
<main class="adm-contenedor">
    <nav class="adm-barra"><a href="adm_panel.php">← Panel</a><form action="../../procesos/cerrar_sesion.php" method="post"><button class="adm-boton adm-boton-secundario">Cerrar sesión</button></form></nav>
    <h1>Estructura académica</h1>
    <?php if ($adm_estado !== ''): ?><p class="adm-mensaje <?= in_array($adm_estado, ['datos_invalidos', 'duplicado', 'referencia_invalida', 'profesor_invalido', 'dependencias'], true) ? 'adm-mensaje-error' : 'adm-mensaje-exito' ?>" role="status"><?= adm_escapar($adm_mensajes[$adm_estado]) ?></p><?php endif; ?>

    <section class="adm-panel" data-busqueda-panel>
        <h2>Años académicos</h2>
        <label class="adm-busqueda">Buscar año académico
            <input type="search" data-busqueda-entrada placeholder="Ej. 2025-2026" autocomplete="off">
        </label>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_estructura.php" method="post">
            <input type="hidden" name="adm_tipo" value="grado">
            <label>Año (AAAA-AAAA)<input name="adm_nombre" placeholder="2026-2027" pattern="\d{4}-\d{4}" maxlength="9" required></label>
            <label>Fecha de inicio<input type="date" name="adm_fecha_inicio" required></label>
            <label>Fecha de fin<input type="date" name="adm_fecha_fin" required></label>
            <button class="adm-boton" type="submit">Crear año</button>
        </form>
        <div class="adm-registros">
            <?php foreach ($adm_grados as $adm_grado): ?>
                <div class="adm-item-buscable" data-busqueda-item data-busqueda-texto="<?= adm_escapar($adm_grado['nombre'] . ' ' . $adm_grado['fecha_inicio'] . ' ' . $adm_grado['fecha_fin']) ?>">
                <form class="adm-registro adm-formulario" action="../../procesos/administracion/adm_guardar_estructura.php" method="post">
                    <input type="hidden" name="adm_tipo" value="grado"><input type="hidden" name="adm_id" value="<?= (int) $adm_grado['id_grado'] ?>">
                    <label>Año<input name="adm_nombre" value="<?= adm_escapar($adm_grado['nombre']) ?>" maxlength="9" required></label>
                    <label>Inicio<input type="date" name="adm_fecha_inicio" value="<?= adm_escapar($adm_grado['fecha_inicio']) ?>" required></label>
                    <label>Fin<input type="date" name="adm_fecha_fin" value="<?= adm_escapar($adm_grado['fecha_fin']) ?>" required></label>
                    <button class="adm-boton" type="submit">Guardar</button>
                </form>
                <form action="../../procesos/administracion/adm_eliminar_estructura.php" method="post" onsubmit="return confirm('¿Eliminar este año académico?');">
                    <input type="hidden" name="adm_tipo" value="grado"><input type="hidden" name="adm_id" value="<?= (int) $adm_grado['id_grado'] ?>">
                    <button class="adm-boton adm-boton-peligro" type="submit">Eliminar año</button>
                </form>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay años que coincidan con la búsqueda.</p>
    </section>

    <section class="adm-panel" data-busqueda-panel>
        <h2>Cursos</h2>
        <label class="adm-busqueda">Buscar curso, etapa o año académico
            <input type="search" data-busqueda-entrada placeholder="Escribe para filtrar..." autocomplete="off">
        </label>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_estructura.php" method="post">
            <input type="hidden" name="adm_tipo" value="curso">
            <label>Año<select name="adm_id_grado" required><?php foreach ($adm_grados as $adm_grado): ?><option value="<?= (int) $adm_grado['id_grado'] ?>"><?= adm_escapar($adm_grado['nombre']) ?></option><?php endforeach; ?></select></label>
            <label>Nombre<input name="adm_nombre" maxlength="60" required></label><label>Etapa<input name="adm_etapa" maxlength="40" required></label>
            <label>Nivel<input type="number" name="adm_nivel" min="0" max="255" required></label><label>Grupo<input name="adm_grupo" maxlength="1" pattern="[A-Za-z]" value="A" required></label>
            <button class="adm-boton" type="submit">Crear curso</button>
        </form>
        <div class="adm-registros">
            <?php foreach ($adm_cursos as $adm_curso): ?>
                <div class="adm-item-buscable" data-busqueda-item data-busqueda-texto="<?= adm_escapar($adm_curso['grado_nombre'] . ' ' . $adm_curso['nombre'] . ' ' . $adm_curso['etapa'] . ' ' . $adm_curso['nivel'] . ' ' . $adm_curso['grupo']) ?>">
                <form class="adm-registro adm-formulario" action="../../procesos/administracion/adm_guardar_estructura.php" method="post">
                    <input type="hidden" name="adm_tipo" value="curso"><input type="hidden" name="adm_id" value="<?= (int) $adm_curso['id_curso'] ?>">
                    <label>Año<select name="adm_id_grado"><?php foreach ($adm_grados as $adm_grado): ?><option value="<?= (int) $adm_grado['id_grado'] ?>" <?= (int) $adm_grado['id_grado'] === (int) $adm_curso['id_grado'] ? 'selected' : '' ?>><?= adm_escapar($adm_grado['nombre']) ?></option><?php endforeach; ?></select></label>
                    <label>Curso<input name="adm_nombre" value="<?= adm_escapar($adm_curso['nombre']) ?>" maxlength="60" required></label>
                    <label>Etapa<input name="adm_etapa" value="<?= adm_escapar($adm_curso['etapa']) ?>" maxlength="40" required></label>
                    <label>Nivel<input type="number" name="adm_nivel" value="<?= (int) $adm_curso['nivel'] ?>" min="0" max="255" required></label>
                    <label>Grupo<input name="adm_grupo" value="<?= adm_escapar($adm_curso['grupo']) ?>" maxlength="1" pattern="[A-Za-z]" required></label>
                    <button class="adm-boton" type="submit">Guardar</button>
                </form>
                <form action="../../procesos/administracion/adm_eliminar_estructura.php" method="post" onsubmit="return confirm('¿Eliminar este curso?');"><input type="hidden" name="adm_tipo" value="curso"><input type="hidden" name="adm_id" value="<?= (int) $adm_curso['id_curso'] ?>"><button class="adm-boton adm-boton-peligro" type="submit">Eliminar curso</button></form>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay cursos que coincidan con la búsqueda.</p>
    </section>

    <section class="adm-panel" data-busqueda-panel>
        <h2>Asignaturas</h2>
        <label class="adm-busqueda">Buscar por código o nombre
            <input type="search" data-busqueda-entrada placeholder="Escribe para filtrar..." autocomplete="off">
        </label>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_estructura.php" method="post"><input type="hidden" name="adm_tipo" value="asignatura"><label>Código<input name="adm_codigo" maxlength="10" required></label><label>Nombre<input name="adm_nombre" maxlength="80" required></label><button class="adm-boton" type="submit">Crear asignatura</button></form>
        <div class="adm-registros">
            <?php foreach ($adm_asignaturas as $adm_asignatura): ?>
                <div class="adm-item-buscable" data-busqueda-item data-busqueda-texto="<?= adm_escapar($adm_asignatura['codigo'] . ' ' . $adm_asignatura['nombre']) ?>">
                <form class="adm-registro adm-formulario" action="../../procesos/administracion/adm_guardar_estructura.php" method="post"><input type="hidden" name="adm_tipo" value="asignatura"><input type="hidden" name="adm_id" value="<?= (int) $adm_asignatura['id_asignatura'] ?>"><label>Código<input name="adm_codigo" value="<?= adm_escapar($adm_asignatura['codigo']) ?>" maxlength="10" required></label><label>Nombre<input name="adm_nombre" value="<?= adm_escapar($adm_asignatura['nombre']) ?>" maxlength="80" required></label><button class="adm-boton" type="submit">Guardar</button></form>
                <form action="../../procesos/administracion/adm_eliminar_estructura.php" method="post" onsubmit="return confirm('¿Eliminar esta asignatura?');"><input type="hidden" name="adm_tipo" value="asignatura"><input type="hidden" name="adm_id" value="<?= (int) $adm_asignatura['id_asignatura'] ?>"><button class="adm-boton adm-boton-peligro" type="submit">Eliminar asignatura</button></form>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay asignaturas que coincidan con la búsqueda.</p>
    </section>

    <section class="adm-panel" data-busqueda-panel>
        <h2>Asignaturas de cada curso</h2>
        <label class="adm-busqueda">Buscar por curso, asignatura o profesor
            <input type="search" data-busqueda-entrada placeholder="Escribe para filtrar..." autocomplete="off">
        </label>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_estructura.php" method="post"><input type="hidden" name="adm_tipo" value="curso_asignatura">
            <label>Curso<select name="adm_id_curso" required><?php foreach ($adm_cursos as $adm_curso): ?><option value="<?= (int) $adm_curso['id_curso'] ?>"><?= adm_escapar($adm_curso['grado_nombre'] . ' · ' . $adm_curso['nombre']) ?></option><?php endforeach; ?></select></label>
            <label>Asignatura<select name="adm_id_asignatura" required><?php foreach ($adm_asignaturas as $adm_asignatura): ?><option value="<?= (int) $adm_asignatura['id_asignatura'] ?>"><?= adm_escapar($adm_asignatura['codigo'] . ' · ' . $adm_asignatura['nombre']) ?></option><?php endforeach; ?></select></label>
            <label>Profesor responsable<select name="adm_id_profesor" required><?php foreach ($adm_profesores as $adm_profesor): ?><option value="<?= (int) $adm_profesor['id_usuario'] ?>"><?= adm_escapar($adm_profesor['apellidos'] . ', ' . $adm_profesor['nombre']) ?></option><?php endforeach; ?></select></label>
            <button class="adm-boton" type="submit">Asignar materia</button>
        </form>
        <div class="adm-registros">
            <?php foreach ($adm_relaciones as $adm_relacion): ?>
                <div class="adm-item-buscable" data-busqueda-item data-busqueda-texto="<?= adm_escapar($adm_relacion['grado_nombre'] . ' ' . $adm_relacion['curso_nombre'] . ' ' . $adm_relacion['codigo'] . ' ' . $adm_relacion['asignatura_nombre'] . ' ' . $adm_relacion['profesor_apellidos'] . ' ' . $adm_relacion['profesor_nombre']) ?>">
                <form class="adm-registro adm-formulario" action="../../procesos/administracion/adm_guardar_estructura.php" method="post"><input type="hidden" name="adm_tipo" value="curso_asignatura"><input type="hidden" name="adm_id" value="<?= (int) $adm_relacion['id_curso_asignatura'] ?>">
                    <label>Curso<select name="adm_id_curso"><?php foreach ($adm_cursos as $adm_curso): ?><option value="<?= (int) $adm_curso['id_curso'] ?>" <?= (int) $adm_curso['id_curso'] === (int) $adm_relacion['id_curso'] ? 'selected' : '' ?>><?= adm_escapar($adm_curso['grado_nombre'] . ' · ' . $adm_curso['nombre']) ?></option><?php endforeach; ?></select></label>
                    <label>Asignatura<select name="adm_id_asignatura"><?php foreach ($adm_asignaturas as $adm_asignatura): ?><option value="<?= (int) $adm_asignatura['id_asignatura'] ?>" <?= (int) $adm_asignatura['id_asignatura'] === (int) $adm_relacion['id_asignatura'] ? 'selected' : '' ?>><?= adm_escapar($adm_asignatura['codigo'] . ' · ' . $adm_asignatura['nombre']) ?></option><?php endforeach; ?></select></label>
                    <label>Responsable<select name="adm_id_profesor"><?php foreach ($adm_profesores as $adm_profesor): ?><option value="<?= (int) $adm_profesor['id_usuario'] ?>" <?= (int) $adm_profesor['id_usuario'] === (int) $adm_relacion['id_profesor'] ? 'selected' : '' ?>><?= adm_escapar($adm_profesor['apellidos'] . ', ' . $adm_profesor['nombre']) ?></option><?php endforeach; ?></select></label>
                    <button class="adm-boton" type="submit">Guardar</button>
                </form>
                <form action="../../procesos/administracion/adm_eliminar_estructura.php" method="post" onsubmit="return confirm('¿Quitar esta asignatura del curso? Las notas existentes impedirán el borrado.');"><input type="hidden" name="adm_tipo" value="curso_asignatura"><input type="hidden" name="adm_id" value="<?= (int) $adm_relacion['id_curso_asignatura'] ?>"><button class="adm-boton adm-boton-peligro" type="submit">Quitar del curso</button></form>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay asignaciones que coincidan con la búsqueda.</p>
    </section>
</main>
<script src="../js/busqueda_paneles.js" defer></script>
</body>
</html>
