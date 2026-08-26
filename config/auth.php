<?php
/*
|--------------------------------------------------------------------------
| SISTEMA DE ASISTENCIA QR ESCOLAR
|--------------------------------------------------------------------------
| Archivo: auth.php
| Descripción: Verifica que exista una sesión iniciada.
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no existe la sesión, regresar al login
if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit();

}
?>