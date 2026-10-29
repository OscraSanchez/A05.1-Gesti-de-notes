<?php
session_start();
include_once './includes/conexion.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./login.php');
    exit();
}


$user = trim($_POST['user']) ?? '';
$con  = $_POST['con'] ?? '';

if ($user === '' || $con === '') {
    header('Location: ./login.php?err=vacio');
    exit();
}

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT *
            FROM tb_usuarios
            WHERE email = :email";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':email' => $user
    ]);

    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u && password_verify($con, $u['password'])) {

        $_SESSION['IdUser']     = $u['id_usuario'];
        $_SESSION['IdRol']   = $u['id_rol'];
        $_SESSION['Nombre']     = $u['nombre'];
        $_SESSION['Apellido']   = $u['apellidos'];
        $_SESSION['Email']      = $u['email'];

        header('Location: ../view/home.php');
        exit();
    }

    header('Location: ./login.php?err=credenciales');
    exit();

} catch (PDOException $e) {
    error_log('Error login: ' . $e->getMessage());
    header('Location: ./login.php?err=servidor');
    exit();
}