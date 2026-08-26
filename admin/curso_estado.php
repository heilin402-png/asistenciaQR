<?php

session_start();

require_once("../config/conexion.php");


if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/login.php");
    exit();

}


if ($_SESSION["id_rol"] != 1) {

    header("Location: ../docente/dashboard.php");
    exit();

}


if (!isset($_GET["id"])) {

    header("Location: cursos.php");
    exit();

}


$id = intval($_GET["id"]);


$sql = "SELECT estado
        FROM cursos
        WHERE id_curso = $id";


$resultado = mysqli_query($conexion, $sql);


if (!$resultado || mysqli_num_rows($resultado) == 0) {

    header("Location: cursos.php?error=1");
    exit();

}


$curso = mysqli_fetch_assoc($resultado);


if ($curso["estado"] == "ACTIVO") {

    $nuevo_estado = "INACTIVO";

} else {

    $nuevo_estado = "ACTIVO";

}


$sql_update = "UPDATE cursos
               SET estado = '$nuevo_estado'
               WHERE id_curso = $id";


if (mysqli_query($conexion, $sql_update)) {

    header("Location: cursos.php?mensaje=estado");
    exit();

}


header("Location: cursos.php?error=1");
exit();

?>