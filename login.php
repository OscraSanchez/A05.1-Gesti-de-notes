<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="contenedor">
        <div class="panel-logo">
            <img src="img/logo.png" alt="Jesuïtes Bellvitge - Centre d'Estudis Joan XXIII">
        </div>

        <div class="panel-form">
            <h1>Iniciar sesión</h1>

            <form class="form-login" action="./ValiLogin.php" method="POST">
                <div class="campo">
                    <label for="user">Nombre de usuario</label>
                    <input type="text" id="user" name="user" placeholder="Introduce el nombre de usuario">
                    <p id="errU"></p>
                </div>

                <div class="campo">
                    <label for="con">Contraseña</label>
                    <input type="password" id="con" name="con" placeholder="Introduce la contraseña">
                    <p id="errC"></p>
                </div>

                <button type="submit" class="btn">Iniciar sesión</button>
            </form>
            <?php
            if(isset($_GET['err'])){
                if($_GET['err'] == 'vacio'){
                    echo '
                        <div class="errores">
                            <p>No puedes dejar campos vacios.</p>
                        </div>';
                } elseif($_GET['err'] == 'credenciales'){
                    echo '
                        <div class="errores">
                            <p>Nombre o contraseña incorrecta.</p>
                        </div>';
                } 
            }
            ?>
            <p class="enlace-extra">¿No tienes cuenta? <a href="reg.php">Regístrate</a>.</p>
        </div>
    </div>
    <script src="./js.js"></script>
</body>
</html>