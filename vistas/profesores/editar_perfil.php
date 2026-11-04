<?php
declare(strict_types=1);

require_once __DIR__ . '/../../procesos/consultar_perfil_profesor.php';

$mensajes = [
    'actualizado' => 'Tu perfil se ha actualizado.',
    'datos_invalidos' => 'Revisa los datos. El nombre, los apellidos y el correo son obligatorios.',
    'email_existente' => 'Ese correo ya está asociado a otra cuenta.',
    'contrasena_actual_incorrecta' => 'La contraseña actual no es correcta.',
    'contrasenas_no_coinciden' => 'La nueva contraseña y su confirmación no coinciden.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar perfil | Gnotas</title>
    <link rel="stylesheet" href="../css/profesores.css">
</head>
<body>
<main>
    <nav class="barra-superior" aria-label="Navegación docente">
        <a class="volver" href="listado_alumnos.php">← Volver a mis alumnos</a>
        <form action="../../procesos/cerrar_sesion.php" method="post">
            <button class="boton-secundario" type="submit">Cerrar sesión</button>
        </form>
    </nav>
    <header class="cabecera cabecera-ficha">
        <div>
            <p class="marca">Gnotas · Área docente</p>
            <h1>Editar perfil</h1>
            <p class="subtitulo">Actualiza tus datos personales y, si lo necesitas, tu contraseña.</p>
        </div>
    </header>

    <?php if ($estado !== ''): ?>
        <p
            class="mensaje <?= $estado === 'actualizado' ? 'mensaje-exito' : 'mensaje-error' ?>"
            role="status"
        >
            <?= escaparPerfilProfesor($mensajes[$estado]) ?>
        </p>
    <?php endif; ?>

    <section class="perfil-formulario" aria-label="Datos del perfil">
        <form action="../../procesos/actualizar_perfil_profesor.php" method="post">
            <div class="perfil-campos">
                <div class="perfil-campo">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?= escaparPerfilProfesor($perfil['nombre']) ?>"
                        maxlength="60"
                        autocomplete="given-name"
                        required
                    >
                </div>

                <div class="perfil-campo">
                    <label for="apellidos">Apellidos</label>
                    <input
                        type="text"
                        id="apellidos"
                        name="apellidos"
                        value="<?= escaparPerfilProfesor($perfil['apellidos']) ?>"
                        maxlength="100"
                        autocomplete="family-name"
                        required
                    >
                </div>

                <div class="perfil-campo perfil-campo-completo">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= escaparPerfilProfesor($perfil['email']) ?>"
                        maxlength="120"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <fieldset>
                <legend>Cambiar contraseña (opcional)</legend>
                <p class="ayuda">Deja estos campos vacíos si no quieres cambiarla.</p>
                <div class="perfil-campos">
                    <div class="perfil-campo perfil-campo-completo">
                        <label for="contrasena_actual">Contraseña actual</label>
                        <input
                            type="password"
                            id="contrasena_actual"
                            name="contrasena_actual"
                            autocomplete="current-password"
                        >
                    </div>

                    <div class="perfil-campo">
                        <label for="nueva_contrasena">Nueva contraseña</label>
                        <input
                            type="password"
                            id="nueva_contrasena"
                            name="nueva_contrasena"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="perfil-campo">
                        <label for="confirmar_contrasena">Repetir nueva contraseña</label>
                        <input
                            type="password"
                            id="confirmar_contrasena"
                            name="confirmar_contrasena"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                        >
                    </div>
                </div>
            </fieldset>

            <div class="acciones-formulario">
                <button type="submit">Guardar perfil</button>
                <a class="boton-cancelar" href="listado_alumnos.php">Cancelar</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
