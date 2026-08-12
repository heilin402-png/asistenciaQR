<?php

session_start();

require_once("../config/conexion.php");


/* ==========================
   VERIFICAR SESIÓN
   ========================== */

if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/login.php");
    exit();

}


/* ==========================
   VERIFICAR ADMINISTRADOR
   ========================== */

if ($_SESSION["id_rol"] != 1) {

    header("Location: ../docente/dashboard.php");
    exit();

}


/* ==========================
   VERIFICAR ID
   ========================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: estudiantes.php?error=1");
    exit();

}

$id_estudiante = intval($_GET["id"]);


/* ==========================
   CONSULTAR ESTUDIANTE
   ========================== */

$sql = "SELECT estado
        FROM estudiantes
        WHERE id_estudiante = ?";

$stmt = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_estudiante
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($resultado) != 1) {

    header("Location: estudiantes.php?error=1");
    exit();

}

$estudiante = mysqli_fetch_assoc($resultado);


/* ==========================
   CAMBIAR ESTADO
   ========================== */

if ($estudiante["estado"] == "ACTIVO") {

    $nuevo_estado = "INACTIVO";

} else {

    $nuevo_estado = "ACTIVO";

}


$sql = "UPDATE estudiantes
        SET estado = ?
        WHERE id_estudiante = ?";

$stmt = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $nuevo_estado,
    $id_estudiante
);


if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: estudiantes.php?mensaje=estado"
    );

    exit();

}


header("Location: estudiantes.php?error=1");

exit();

?>