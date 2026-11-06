<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $parametrosCookie = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametrosCookie['path'],
        $parametrosCookie['domain'],
        $parametrosCookie['secure'],
        $parametrosCookie['httponly']
    );
}

session_destroy();

header('Location: ../vistas/login.php');
exit;
