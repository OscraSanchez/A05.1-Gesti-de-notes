<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="contenedor">
        <div class="panel-logo">
            <img src="img/logo.png" alt="Jesuïtes Bellvitge - Centre d'Estudis Joan XXIII">
        </div>
        <div class="panel-form">
            <h1>Registro</h1>
            <form class="form-registro" action="./ValiReg.php" method="POST">
                <div>
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Introduce tu usuario" >
                    <p id="errorUsuario" class="errores"></p>
                </div>
                <div>
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Introduce tu nombre" >
                    <p id="errorNombre" class="errores"></p>
                </div>
                <div class="completo"   >
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Introduce tu apellido" >
                    <p id="errorApellido" class="errores"></p>
                </div>
                <div class="completo">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Introduce tu email" >
                    <p id="errorEmail" class="errores"></p>
                </div>
                <div>
                    <label for="con">Contraseña</label>
                    <input type="password" id="con" name="con" placeholder="Introduce la contraseña" >
                    <p id="errorCon" class="errores"></p>
                </div>
                <div>
                    <label for="con2">Confirmación</label>
                    <input type="password" id="con2" name="con2" placeholder="Confirma tu contraseña" >
                    <p id="errorCon2" class="errores"></p>
                </div>
                <div class="btn-fila">
                    <button type="submit" class="btn" id="botonRegistrar" disabled>Registrar</button>
                </div>
            </form>

            <?php if (!empty($_SESSION['errores'])): ?>
                <div class="errores">
                    <ul>
                        <?php foreach ((array) $_SESSION['errores'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errores']); ?>
            <?php endif; ?>

            <p class="enlace-extra">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>.</p>
        </div>
    </div>
    <script src="ValiReg.js"></script>
</body>
</html>