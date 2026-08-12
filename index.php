<?php

session_start();

if (isset($_SESSION["id_usuario"])) {

    if ($_SESSION["id_rol"] == 1) {

        header("Location: /asistencia_qr/admin/dashboard.php");
        exit();

    } elseif ($_SESSION["id_rol"] == 2) {

        header("Location: /asistencia_qr/docente/dashboard.php");
        exit();

    }

}

/* Usuario no autenticado */
header("Location: /asistencia_qr/auth/login.php");
exit();

?>