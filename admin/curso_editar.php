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


$sql = "SELECT
            id_curso,
            nombre_curso
        FROM cursos
        WHERE id_curso = $id";


$resultado = mysqli_query($conexion, $sql);


if (!$resultado || mysqli_num_rows($resultado) == 0) {

    header("Location: cursos.php?error=1");
    exit();

}


$curso = mysqli_fetch_assoc($resultado);


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


        $sql_update = "UPDATE cursos
                       SET nombre_curso = '$nombre_curso'
                       WHERE id_curso = $id";


        if (mysqli_query($conexion, $sql_update)) {

            header("Location: cursos.php?mensaje=editado");
            exit();

        } else {

            $error = "Error al actualizar el curso: "
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

    <title>Editar curso - Asistencia QR</title>

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

                        <i class="bi bi-pencil"></i>

                        Editar curso

                    </h3>


                    <?php if ($error != ""): ?>

                        <div class="alert alert-danger">

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
                                maxlength="100"
                                value="<?php echo htmlspecialchars($curso["nombre_curso"]); ?>"
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

                                Guardar cambios

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