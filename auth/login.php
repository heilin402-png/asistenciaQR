<?php
session_start();

if(isset($_SESSION['id_usuario'])){

    if($_SESSION['id_rol']==1){

        header("Location: ../admin/dashboard.php");

    }else{

        header("Location: ../docente/dashboard.php");

    }

    exit();

}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema Asistencia QR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/estilos.css">

</head>

<body>

<div class="container">

<div class="row justify-content-center align-items-center vh-100">

<div class="col-md-5">

<div class="card shadow">

<div class="card-body p-5">

<div class="text-center">

<img src="../assets/img/logo.png" width="110" class="mb-3">

<h3>Sistema de Asistencia QR</h3>

<p class="text-muted">

Institución Educativa

</p>

</div>

<?php

if(isset($_GET['error'])){

echo '

<div class="alert alert-danger">

Correo o contraseña incorrectos.

</div>

';

}

?>

<form action="validar_login.php" method="POST">

<div class="mb-3">

<label class="form-label">

Correo

</label>

<input
type="email"
name="correo"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Contraseña

</label>

<div class="input-group">

<input
type="password"
name="password"
id="password"
class="form-control"
required>

<button
type="button"
class="btn btn-outline-secondary"
id="ver">

<i class="bi bi-eye"></i>

</button>

</div>

</div>

<div class="d-grid">

<button class="btn btn-primary btn-lg">

Ingresar

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

const boton=document.getElementById("ver");

const password=document.getElementById("password");

boton.onclick=function(){

if(password.type==="password"){

password.type="text";

boton.innerHTML='<i class="bi bi-eye-slash"></i>';

}else{

password.type="password";

boton.innerHTML='<i class="bi bi-eye"></i>';

}

}

</script>

</body>

</html>