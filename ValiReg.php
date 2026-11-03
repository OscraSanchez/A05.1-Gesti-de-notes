<?php
session_start();
session_unset();
include_once './includes/conexion.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./login.php');
    exit();
}

$errores = [];
$caracteres = '/^[A-Za-z0-9_.,\-#\x{00B7}@]+$/u';
$soloLetras = '/^[\p{L} ]+$/u';

$nombre = $_POST['nombre'] ?? '';
$Apellido = $_POST['apellido'] ?? '' ;
$email = $_POST['email'] ?? '';
$con = $_POST['con'] ?? '';

function formatearApellidos($texto) { //"miguel angel"  ->  "MiguelAngel"
    $palabras  = explode(" ", $texto);   // array: ["miguel", "angel"]
    $resultado = "";

    foreach ($palabras as $palabra) {
        if ($palabra != "") {
            // MB_CASE_TITLE = primera letra en mayúscula y el resto en minúscula
            $resultado .= mb_convert_case($palabra, MB_CASE_TITLE, "UTF-8");
        }
    }
    return $resultado;
}

if ($nombre == "") {
    $errores['nombre'] = "El nombre no puede estar vacío.";
} elseif (mb_strlen($nombre) > 100) {
    $errores['nombre'] = "El nombre no puede tener más de 100 caracteres.";
} elseif (!preg_match($soloLetras, $nombre)) {
    $errores['nombre'] = "El nombre solo puede tener letras y espacios.";
}

if ($Apellido == "") {
    $errores['apellido'] = "El apellido no puede estar vacío.";
} elseif (mb_strlen($Apellido) > 100) {
    $errores['apellido'] = "El apellido no puede tener más de 100 caracteres.";
} elseif (!preg_match($soloLetras, $Apellido)) {
    $errores['apellido'] = "El apellido solo puede tener letras y espacios.";
}

if ($email == "") {
    $errores['email'] = "El email no puede estar vacío.";
} elseif (mb_strlen($email) > 150) {
    $errores['email'] = "El email no puede tener más de 150 caracteres.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // filter_var es una función de PHP que ya sabe cómo es un email válido
    $errores['email'] = "El email no tiene un formato válido.";
} elseif ($conn != null) {
    // En la BBDD el email también es UNIQUE, lo comprobamos igual que el username
    try {
        $consulta = $conn->prepare("SELECT email FROM tb_usuarios WHERE Email = :email");
        $consulta->execute([':email' => $email]);
        $existe = $consulta->fetch();

        if ($existe != false) {
            $errores['email'] = "El email introducido ya está registrado.";
        }
    } catch (PDOException $e) {
        error_log("Error al comprobar email: " . $e->getMessage());
        $errores['general'] = "Ha habido un problema con la base de datos. Inténtalo de nuevo más tarde.";
    }
}

if ($con == "") {
    $errores['passwd'] = "La contraseña no puede estar vacía.";
} elseif (mb_strlen($con) < 8) {
    $errores['passwd'] = "La contraseña debe tener al menos 8 caracteres.";
} elseif (mb_strlen($con) > 30) {
    $errores['passwd'] = "La contraseña no puede tener más de 30 caracteres.";
} elseif (!preg_match($caracteres, $con)) {
    $errores['passwd'] = "La contraseña solo puede tener letras, números y los símbolos _ . , - # · @";
}



if (count($errores) == 0) {
    $apellidoFinal = formatearApellidos($Apellido);
    $hash          = password_hash($con, PASSWORD_DEFAULT);   // nunca guardes la contraseña en claro

    try {
        $insert = $conn->prepare(
            "INSERT INTO tb_usuarios (id_rol, nombre, apellidos, password, email)
             VALUES (2, :nombre, :apellido, :hash, :email)"
        );
        $insert->execute([
            ':nombre'   => $nombre,
            ':apellido' => $apellidoFinal,
            ':hash'     => $hash,
            ':email'    => $email
        ]);

        header('Location: ./login.php');
        exit;

    } catch (PDOException $e) {
        // Por ejemplo: tabla que no existe, o dos personas con el mismo username a la vez
        error_log("Error al crear usuario: " . $e->getMessage());
        $errores['general'] = "No se ha podido crear tu cuenta por un problema con la base de datos. Inténtalo de nuevo más tarde.";
    }
}
$_SESSION['errores'] = $errores;
header('Location: ./reg.php?err=err');