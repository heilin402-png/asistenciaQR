<?php

session_start();

session_unset();
session_destroy();

header("Location: /asistencia_qr/auth/login.php");
exit();

?>