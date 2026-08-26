<?php

session_start();

require_once("../config/conexion.php");

$correo = trim($_POST["correo"]);
$password = trim($_POST["password"]);

$sql = "SELECT * FROM usuarios
WHERE correo=?
AND estado='ACTIVO'";

$stmt = mysqli_prepare($conexion,$sql);

mysqli_stmt_bind_param($stmt,"s",$correo);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($resultado)==1){

    $usuario = mysqli_fetch_assoc($resultado);

    if(password_verify($password, $usuario["password"])){

        $_SESSION["id_usuario"] = $usuario["id_usuario"];

        $_SESSION["nombre"] = $usuario["nombres"];

        $_SESSION["id_rol"] = $usuario["id_rol"];

        if($usuario["id_rol"] == 1){

            header("Location: ../admin/dashboard.php");

        }else{

            header("Location: ../docente/dashboard.php");

        }

        exit();

    }

}

header("Location: login.php?error=1");

exit();

?>