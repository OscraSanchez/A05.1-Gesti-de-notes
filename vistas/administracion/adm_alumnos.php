<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/administracion/adm_consultar_alumnos.php';

$adm_estado = $_GET['estado'] ?? '';
$adm_mensajes = [
    'creado' => 'La ficha se ha creado.',
    'actualizado' => 'La ficha se ha actualizado.',
    'eliminado' => 'La ficha se ha eliminado.',
    'datos_invalidos' => 'Revisa los datos de la ficha.',
    'usuario_no_alumno' => 'Solo se pueden crear fichas para cuentas con rol alumno.',
    'dni_existente' => 'Ese DNI ya está registrado.',
    'ficha_existente' => 'Ese usuario ya tiene ficha académica.',
    'referencia_invalida' => 'El usuario o curso seleccionado ya no existe.',
    'dependencias' => 'No se puede eliminar la ficha porque ya tiene notas. Elimina primero las notas relacionadas.',
    'curso_con_notas' => 'No se puede cambiar de curso a este alumno porque tiene notas asociadas. Elimina primero sus notas y vuelve a intentarlo.',
];
if (!is_string($adm_estado) || !isset($adm_mensajes[$adm_estado])) {
    $adm_estado = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Alumnos | Administración Gnotas</title><link rel="stylesheet" href="../css/adm_administracion.css"></head>
<body>
<main class="adm-contenedor">
    <nav class="adm-barra"><a href="adm_panel.php">← Panel</a><form action="../../procesos/cerrar_sesion.php" method="post"><button class="adm-boton adm-boton-secundario">Cerrar sesión</button></form></nav>
    <h1>Fichas de alumnos</h1>
    <?php if ($adm_estado !== ''): ?><p class="adm-mensaje <?= in_array($adm_estado, ['datos_invalidos', 'usuario_no_alumno', 'dni_existente', 'ficha_existente', 'referencia_invalida', 'dependencias', 'curso_con_notas'], true) ? 'adm-mensaje-error' : 'adm-mensaje-exito' ?>" role="status"><?= adm_escapar($adm_mensajes[$adm_estado]) ?></p><?php endif; ?>
    <section class="adm-panel">
        <h2>Crear ficha académica</h2>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_alumno.php" method="post">
            <input type="hidden" name="adm_id_alumno" value="0">
            <label>Cuenta de alumno<select name="adm_id_usuario" required><?php foreach ($adm_usuarios_alumno as $adm_usuario): ?><option value="<?= (int) $adm_usuario['id_usuario'] ?>"><?= adm_escapar($adm_usuario['apellidos'] . ', ' . $adm_usuario['nombre'] . ' · ' . $adm_usuario['email']) ?></option><?php endforeach; ?></select></label>
            <label>Curso<select name="adm_id_curso" required><?php foreach ($adm_cursos as $adm_curso): ?><option value="<?= (int) $adm_curso['id_curso'] ?>"><?= adm_escapar($adm_curso['grado_nombre'] . ' · ' . $adm_curso['nombre']) ?></option><?php endforeach; ?></select></label>
            <label>DNI<input name="adm_dni" pattern="[0-9]{8}[A-Za-z]" maxlength="9" required></label>
            <label>Fecha de nacimiento<input type="date" name="adm_fecha_nacimiento" required></label>
            <button class="adm-boton" type="submit">Crear ficha</button>
        </form>
    </section>
    <section class="adm-panel" data-busqueda-panel>
        <h2>Alumnado registrado</h2>
        <label class="adm-busqueda">Buscar por alumno, correo, DNI, curso o año
            <input type="search" data-busqueda-entrada placeholder="Escribe para filtrar..." autocomplete="off">
        </label>
        <div class="adm-registros">
        <?php foreach ($adm_alumnos as $adm_alumno): ?>
            <div class="adm-item-buscable" data-busqueda-item data-busqueda-texto="<?= adm_escapar($adm_alumno['nombre'] . ' ' . $adm_alumno['apellidos'] . ' ' . $adm_alumno['email'] . ' ' . $adm_alumno['dni'] . ' ' . $adm_alumno['curso_nombre'] . ' ' . $adm_alumno['grado_nombre']) ?>">
            <form id="adm-alumno-<?= (int) $adm_alumno['id_alumno'] ?>" class="adm-registro adm-formulario" action="../../procesos/administracion/adm_guardar_alumno.php" method="post">
                <input type="hidden" name="adm_id_alumno" value="<?= (int) $adm_alumno['id_alumno'] ?>">
                <label>Alumno<input value="<?= adm_escapar($adm_alumno['apellidos'] . ', ' . $adm_alumno['nombre'] . ' · ' . $adm_alumno['email']) ?>" disabled><input type="hidden" name="adm_id_usuario" value="<?= (int) $adm_alumno['id_usuario'] ?>"></label>
                <label>Curso<select name="adm_id_curso"><?php foreach ($adm_cursos as $adm_curso): ?><option value="<?= (int) $adm_curso['id_curso'] ?>" <?= (int) $adm_curso['id_curso'] === (int) $adm_alumno['id_curso'] ? 'selected' : '' ?>><?= adm_escapar($adm_curso['grado_nombre'] . ' · ' . $adm_curso['nombre']) ?></option><?php endforeach; ?></select></label>
                <label>DNI<input name="adm_dni" value="<?= adm_escapar($adm_alumno['dni']) ?>" maxlength="9" pattern="[0-9]{8}[A-Za-z]" required></label>
                <label>Nacimiento<input type="date" name="adm_fecha_nacimiento" value="<?= adm_escapar($adm_alumno['fecha_nacimiento']) ?>" required></label>
                <button class="adm-boton" type="submit">Guardar ficha</button>
            </form>
            <div class="adm-fila-acciones">
                <a class="adm-boton adm-boton-secundario" href="adm_notas.php?adm_id_alumno=<?= (int) $adm_alumno['id_alumno'] ?>">Ver sus notas</a>
                <form action="../../procesos/administracion/adm_eliminar_alumno.php" method="post" onsubmit="return confirm('¿Eliminar esta ficha académica?');"><input type="hidden" name="adm_id_alumno" value="<?= (int) $adm_alumno['id_alumno'] ?>"><button class="adm-boton adm-boton-peligro" type="submit">Eliminar ficha</button></form>
            </div>
            </div>
        <?php endforeach; ?>
        </div>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay alumnos que coincidan con la búsqueda.</p>
    </section>
</main>
<script src="../js/busqueda_paneles.js" defer></script>
</body>
</html>
