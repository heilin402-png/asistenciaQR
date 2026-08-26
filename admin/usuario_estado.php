<?php

session_start();

require_once("../config/conexion.php");

/* Verificar sesión */
if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/login.php");
    exit();

}

/* Verificar administrador */
if ($_SESSION["id_rol"] != 1) {

    header("Location: ../docente/dashboard.php");
    exit();

}


/* Obtener ID del usuario */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: usuarios.php");
    exit();

}

$id_usuario = intval($_GET["id"]);


/* No permitir que el administrador se desactive a sí mismo */

if ($id_usuario == $_SESSION["id_usuario"]) {

    header("Location: usuarios.php?error=propio");
    exit();

}


/* Buscar estado actual */

$sql = "SELECT estado
        FROM usuarios
        WHERE id_usuario = ?";

$stmt = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_usuario
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($resultado) != 1) {

    header("Location: usuarios.php?error=noexiste");
    exit();

}


$usuario = mysqli_fetch_assoc($resultado);


/* Cambiar estado */

if ($usuario["estado"] == "ACTIVO") {

    $nuevo_estado = "INACTIVO";

} else {

    $nuevo_estado = "ACTIVO";

}


/* Actualizar */

$sql = "UPDATE usuarios
        SET estado = ?
        WHERE id_usuario = ?";

$stmt = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $nuevo_estado,
    $id_usuario
);


if (mysqli_stmt_execute($stmt)) {

    header("Location: usuarios.php?mensaje=estado");

    exit();

} else {

    header("Location: usuarios.php?error=estado");

    exit();

}

?>