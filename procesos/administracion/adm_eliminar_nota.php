<?php
declare(strict_types=1);

require_once __DIR__ . '/adm_acceso.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}
$adm_id_nota = filter_input(INPUT_POST, 'adm_id_nota', FILTER_VALIDATE_INT);
if (!is_int($adm_id_nota) || $adm_id_nota < 1) {
    adm_redirigir('notas', 'datos_invalidos');
}
$adm_eliminar = $conexion->prepare('DELETE FROM tb_notas WHERE id_nota=:id_nota');
$adm_eliminar->execute(['id_nota' => $adm_id_nota]);
if ($adm_eliminar->rowCount() !== 1) {
    adm_redirigir('notas', 'referencia_invalida');
}

adm_redirigir('notas', 'eliminado');
