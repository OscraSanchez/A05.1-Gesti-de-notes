<?php
declare(strict_types=1);

session_start();

if (
    isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])
    && $_SESSION['rol_usuario'] === 'alumno'
) {
    header('Location: alumnos/index.php');
    exit;
}

if (
    isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])
    && $_SESSION['rol_usuario'] === 'profesor'
) {
    header('Location: profesores/listado_alumnos.php');
    exit;
}

if (
    isset($_SESSION['adm_id_usuario'], $_SESSION['adm_rol_usuario'])
    && $_SESSION['adm_rol_usuario'] === 'administrador'
) {
    header('Location: administracion/adm_panel.php');
    exit;
}

$sesionActiva = isset($_SESSION['id_usuario'], $_SESSION['rol_usuario']);
$rolUsuario = $sesionActiva
    ? htmlspecialchars((string) $_SESSION['rol_usuario'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
    : '';
$errorLogin = isset($_GET['error']) && $_GET['error'] === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Gnotas</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<main>
    <h1>Gnotas</h1>
    <?php if ($sesionActiva): ?>
        <p class="mensaje exito">Has iniciado sesión correctamente.</p>
        <p>Rol: <strong><?= $rolUsuario ?></strong></p>
    <?php else: ?>
        <p>Inicia sesión con tu cuenta.</p>
        <?php if ($errorLogin): ?>
            <p class="mensaje error" role="alert">Correo electrónico o contraseña incorrectos.</p>
        <?php endif; ?>
        <form action="../procesos/login.php" method="post">
            <label for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                autocomplete="username"
                maxlength="120"
                required
            >

            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Iniciar sesión</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
