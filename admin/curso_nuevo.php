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


$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre_curso = trim($_POST["nombre_curso"]);


    if ($nombre_curso == "") {

        $error = "Debes ingresar el nombre del curso.";

    } else {


        $nombre_curso = mysqli_real_escape_string(
            $conexion,
            $nombre_curso
        );


        $sql = "INSERT INTO cursos
                (nombre_curso, estado)
                VALUES
                ('$nombre_curso', 'ACTIVO')";


        if (mysqli_query($conexion, $sql)) {

            header("Location: cursos.php?mensaje=creado");
            exit();

        } else {

            $error = "Error al crear el curso: "
                   . mysqli_error($conexion);

        }

    }

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Nuevo curso - Asistencia QR</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid">

        <a
            href="dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-qr-code-scan"></i>

            Sistema de Asistencia QR

        </a>

    </div>

</nav>


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <h3 class="mb-4">

                        <i class="bi bi-book"></i>

                        Nuevo curso

                    </h3>


                    <?php if ($error != ""): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-triangle"></i>

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST">


                        <div class="mb-3">

                            <label class="form-label">

                                Nombre del curso

                            </label>

                            <input
                                type="text"
                                name="nombre_curso"
                                class="form-control"
                                placeholder="Ejemplo: 701"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="d-flex gap-2">

                            <a
                                href="cursos.php"
                                class="btn btn-secondary"
                            >

                                <i class="bi bi-arrow-left"></i>

                                Cancelar

                            </a>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-check-circle"></i>

                                Guardar curso

                            </button>

                        </div>


                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>