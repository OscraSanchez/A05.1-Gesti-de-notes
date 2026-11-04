<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/administracion/adm_consultar_usuarios.php';

$adm_estado = $_GET['estado'] ?? '';
$adm_mensajes = [
    'creado' => 'La cuenta se ha creado.',
    'actualizado' => 'La cuenta se ha actualizado.',
    'eliminado' => 'La cuenta se ha eliminado.',
    'datos_invalidos' => 'Revisa los datos del formulario.',
    'email_existente' => 'Ese correo ya está en uso.',
    'dependencias' => 'No se puede eliminar el usuario porque tiene datos relacionados. Elimina o reasigna esos datos primero.',
    'autoproteccion' => 'No puedes eliminar o cambiar el rol de tu propia cuenta administrativa.',
    'ultimo_admin' => 'No se puede quitar ni borrar al último administrador.',
    'rol_conflictivo' => 'No se puede cambiar el rol: el usuario tiene una ficha o asignaciones que requieren conservarlo.',
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
    <title>Usuarios | Administración Gnotas</title>
    <link rel="stylesheet" href="../css/adm_administracion.css">
</head>
<body>
<main class="adm-contenedor">
    <nav class="adm-barra">
        <a href="adm_panel.php">← Panel</a>
        <form action="../../procesos/cerrar_sesion.php" method="post"><button class="adm-boton adm-boton-secundario">Cerrar sesión</button></form>
    </nav>
    <h1>Usuarios</h1>
    <?php if ($adm_estado !== ''): ?>
        <p class="adm-mensaje <?= in_array($adm_estado, ['datos_invalidos', 'email_existente', 'dependencias', 'autoproteccion', 'ultimo_admin', 'rol_conflictivo'], true) ? 'adm-mensaje-error' : 'adm-mensaje-exito' ?>" role="status"><?= adm_escapar($adm_mensajes[$adm_estado]) ?></p>
    <?php endif; ?>
    <section class="adm-panel">
        <h2>Crear cuenta</h2>
        <form class="adm-formulario adm-grid" action="../../procesos/administracion/adm_guardar_usuario.php" method="post">
            <input type="hidden" name="adm_id_usuario" value="0">
            <label>Nombre<input name="adm_nombre" maxlength="60" required></label>
            <label>Apellidos<input name="adm_apellidos" maxlength="100" required></label>
            <label>Correo<input type="email" name="adm_email" maxlength="120" required></label>
            <label>Rol<select name="adm_id_rol" required><?php foreach ($adm_roles as $adm_rol): ?><option value="<?= (int) $adm_rol['id_rol'] ?>"><?= adm_escapar($adm_rol['nombre']) ?></option><?php endforeach; ?></select></label>
            <label>Contraseña inicial<input type="password" name="adm_password" minlength="8" maxlength="72" required></label>
            <button class="adm-boton" type="submit">Crear usuario</button>
        </form>
    </section>
    <section class="adm-panel" data-busqueda-panel>
        <h2>Cuentas existentes</h2>
        <label class="adm-busqueda">Buscar por nombre, correo o rol
            <input type="search" data-busqueda-entrada placeholder="Escribe para filtrar..." autocomplete="off">
        </label>
        <div class="adm-tabla-wrap">
            <table>
                <thead><tr><th>Cuenta</th><th>Rol</th><th>Contraseña nueva (opcional)</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($adm_usuarios as $adm_usuario): ?>
                    <tr data-busqueda-item data-busqueda-texto="<?= adm_escapar($adm_usuario['nombre'] . ' ' . $adm_usuario['apellidos'] . ' ' . $adm_usuario['email'] . ' ' . $adm_usuario['nombre_rol']) ?>">
                        <td colspan="3">
                            <form id="adm-usuario-<?= (int) $adm_usuario['id_usuario'] ?>" class="adm-formulario adm-grid adm-fila-usuario" action="../../procesos/administracion/adm_guardar_usuario.php" method="post">
                                <input type="hidden" name="adm_id_usuario" value="<?= (int) $adm_usuario['id_usuario'] ?>">
                                <label>Nombre<input name="adm_nombre" value="<?= adm_escapar($adm_usuario['nombre']) ?>" maxlength="60" required></label>
                                <label>Apellidos<input name="adm_apellidos" value="<?= adm_escapar($adm_usuario['apellidos']) ?>" maxlength="100" required></label>
                                <label>Correo<input type="email" name="adm_email" value="<?= adm_escapar($adm_usuario['email']) ?>" maxlength="120" required></label>
                                <label>Rol<select name="adm_id_rol"><?php foreach ($adm_roles as $adm_rol): ?><option value="<?= (int) $adm_rol['id_rol'] ?>" <?= (int) $adm_rol['id_rol'] === (int) $adm_usuario['id_rol'] ? 'selected' : '' ?>><?= adm_escapar($adm_rol['nombre']) ?></option><?php endforeach; ?></select></label>
                                <label>Nueva contraseña<input type="password" name="adm_password" minlength="8" maxlength="72" autocomplete="new-password"></label>
                            </form>
                        </td>
                        <td class="adm-acciones">
                            <button class="adm-boton" type="submit" form="adm-usuario-<?= (int) $adm_usuario['id_usuario'] ?>">Guardar</button>
                            <form action="../../procesos/administracion/adm_eliminar_usuario.php" method="post" onsubmit="return confirm('¿Eliminar esta cuenta? Si tiene datos relacionados, la base lo impedirá.');">
                                <input type="hidden" name="adm_id_usuario" value="<?= (int) $adm_usuario['id_usuario'] ?>">
                                <button class="adm-boton adm-boton-peligro" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="adm-busqueda-vacio" data-busqueda-vacio hidden>No hay cuentas que coincidan con la búsqueda.</p>
    </section>
</main>
<script src="../js/busqueda_paneles.js" defer></script>
</body>
</html>
