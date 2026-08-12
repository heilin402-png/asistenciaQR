<?php

session_start();

if (isset($_SESSION["id_usuario"])) {

    if ($_SESSION["id_rol"] == 1) {

        header("Location: /asistenciaQR/admin/dashboard.php");
        exit();

    } elseif ($_SESSION["id_rol"] == 2) {

        header("Location: /asistenciaQR/docente/dashboard.php");
        exit();

    }

}

/* Usuario no autenticado */

header("Location: /asistenciaQR/auth/login.php");
exit();

?>