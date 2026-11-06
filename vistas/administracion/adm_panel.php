<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/administracion/adm_consultar_panel.php';

$adm_estado = $_GET['estado'] ?? '';
$adm_mensajes = [
    'creado' => 'El registro se ha creado.',
    'actualizado' => 'Los cambios se han guardado.',
    'eliminado' => 'El registro se ha eliminado.',
    'datos_invalidos' => 'Los datos enviados no son válidos.',
    'email_existente' => 'Ese correo ya está asociado a otra cuenta.',
    'dependencias' => 'No se puede borrar: existen datos relacionados. Elimina o reasigna primero las dependencias.',
    'autoproteccion' => 'No puedes eliminar o degradar tu propia cuenta administrativa.',
    'ultimo_admin' => 'No puedes quitar el rol del último administrador.',
    'rol_conflictivo' => 'No se puede cambiar el rol porque existen datos académicos o asignaciones que lo requieren.',
    'nota_existente' => 'Ya existe una nota para ese alumno, asignatura y evaluación.',
    'dni_existente' => 'Ese DNI ya está registrado.',
    'email_perfil_existente' => 'Ese correo ya está asociado a otra cuenta.',
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
    <title>Administración | Gnotas</title>
    <link rel="stylesheet" href="../css/adm_administracion.css">
</head>
<body>
<main class="adm-contenedor">
    <header class="adm-cabecera">
        <div>
            <p class="adm-marca">Gnotas · Administración</p>
            <h1>Panel de control</h1>
            <p class="adm-subtitulo">
                Hola, <?= adm_escapar($adm_identidad['nombre'] . ' ' . $adm_identidad['apellidos']) ?>.
                Desde aquí gestionas los datos del sistema.
            </p>
        </div>
        <form action="../../procesos/cerrar_sesion.php" method="post">
            <button class="adm-boton adm-boton-secundario" type="submit">Cerrar sesión</button>
        </form>
    </header>

    <?php if ($adm_estado !== ''): ?>
        <p class="adm-mensaje <?= in_array($adm_estado, ['datos_invalidos', 'email_existente', 'dependencias', 'autoproteccion', 'ultimo_admin', 'rol_conflictivo', 'nota_existente', 'dni_existente', 'email_perfil_existente'], true) ? 'adm-mensaje-error' : 'adm-mensaje-exito' ?>" role="status">
            <?= adm_escapar($adm_mensajes[$adm_estado]) ?>
        </p>
    <?php endif; ?>

    <section class="adm-tarjetas" aria-label="Resumen del sistema">
        <?php foreach ([
            'usuarios' => 'Usuarios',
            'alumnos' => 'Alumnos',
            'cursos' => 'Cursos',
            'asignaturas' => 'Asignaturas',
            'notas' => 'Notas',
        ] as $adm_clave => $adm_etiqueta): ?>
            <article class="adm-tarjeta">
                <span><?= adm_escapar($adm_etiqueta) ?></span>
                <strong><?= $adm_conteos[$adm_clave] ?></strong>
            </article>
        <?php endforeach; ?>
    </section>

    <section data-busqueda-panel>
        <label class="adm-busqueda">Buscar módulo administrativo
            <input type="search" data-busqueda-entrada placeholder="Usuarios, alumnos, notas..." autocomplete="off">
        </label>
        <nav class="adm-modulos" aria-label="Gestión administrativa">
            <a href="adm_usuarios.php" data-busqueda-item data-busqueda-texto="usuarios cuentas roles"><strong>Usuarios</strong><span>Crear cuentas, cambiar roles o eliminar usuarios.</span></a>
            <a href="adm_alumnos.php" data-busqueda-item data-busqueda-texto="alumnos fichas dni cursos"><strong>Alumnos</strong><span>Gestionar fichas, DNI, fecha de nacimiento y curso.</span></a>
            <a href="adm_estructura.php" data-busqueda-item data-busqueda-texto="estructura academica años cursos asignaturas"><strong>Estructura académica</strong><span>Gestionar años, cursos, asignaturas y asignaciones.</span></a>
            <a href="adm_notas.php" data-busqueda-item data-busqueda-texto="notas evaluaciones calificaciones"><strong>Notas</strong><span>Consultar, añadir, editar y borrar cualquier nota.</span></a>
        </nav>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay módulos que coincidan con la búsqueda.</p>
    </section>
</main>
<script src="../js/busqueda_paneles.js" defer></script>
</body>
</html>
