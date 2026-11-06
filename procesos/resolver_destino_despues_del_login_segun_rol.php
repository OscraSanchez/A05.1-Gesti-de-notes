<?php
declare(strict_types=1);

session_start();

// Este es el punto único que decide a qué panel llega cada usuario tras autenticarse.
if (
    isset($_SESSION['adm_id_usuario'], $_SESSION['adm_rol_usuario'])
    && is_int($_SESSION['adm_id_usuario'])
    && $_SESSION['adm_rol_usuario'] === 'administrador'
) {
    header('Location: ../vistas/administracion/adm_panel.php');
    exit;
}

if (
    isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])
    && is_int($_SESSION['id_usuario'])
) {
    if ($_SESSION['rol_usuario'] === 'alumno') {
        header('Location: ../vistas/alumnos/index.php');
        exit;
    }

    if ($_SESSION['rol_usuario'] === 'profesor') {
        header('Location: ../vistas/profesores/listado_alumnos.php');
        exit;
    }
}

http_response_code(403);
header('Location: ../vistas/login.php');
exit;
