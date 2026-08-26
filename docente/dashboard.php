<?php
require_once("../config/auth.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Panel Docente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="alert alert-primary">

        <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?></h2>

        <hr>

        <p>Rol: Docente</p>

    </div>

    <a href="../auth/logout.php" class="btn btn-danger">

        Cerrar sesión

    </a>

</div>

</body>

</html>