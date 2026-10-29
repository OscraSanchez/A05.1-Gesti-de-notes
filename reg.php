<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="./ValiReg.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre">
        <input type="text" name="apellido" placeholder="Apellido">
        <input type="text" name="email" placeholder="Email">
        <input type="text" name="con" placeholder="Contraseña">
        <input type="submit" value="Enviar">
    </form>
    <?php 
    session_start();
    if($_SESSION['errores']){
    var_dump($_SESSION['errores']);
    }
    ?>
</body>
</html>