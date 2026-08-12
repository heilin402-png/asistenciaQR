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

    header("Location: estudiantes.php");
    exit();

}

$id_estudiante = intval($_GET["id"]);


/* ==========================
   CONSULTAR ESTUDIANTE
   ========================== */

$sql = "SELECT
            e.id_estudiante,
            e.documento,
            e.nombres,
            e.apellidos,
            c.nombre_curso
        FROM estudiantes e
        INNER JOIN cursos c
            ON e.id_curso = c.id_curso
        WHERE e.id_estudiante = ?";

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
   DATOS DEL QR
   ========================== */

$documento = $estudiante["documento"];

$nombre_completo =
    $estudiante["nombres"] . " " .
    $estudiante["apellidos"];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>QR - <?php echo htmlspecialchars($nombre_completo); ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/estilos.css"
    >

</head>


<body class="bg-light">


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow border-0">

                <div class="card-body text-center p-5">


                    <h3 class="mb-3">

                        <i class="bi bi-qr-code-scan"></i>

                        Código QR

                    </h3>


                    <p class="text-muted">

                        Código QR del estudiante

                    </p>


                    <!-- ==========================
                         QR
                         ========================== -->

                    <div class="my-4">

                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo urlencode($documento); ?>"
                            alt="Código QR"
                            width="250"
                            height="250"
                            class="img-fluid border rounded"
                        >

                    </div>


                    <!-- INFORMACIÓN -->

                    <h4>

                        <?php

                        echo htmlspecialchars(
                            $nombre_completo
                        );

                        ?>

                    </h4>


                    <p class="mb-1">

                        <strong>Documento:</strong>

                        <?php

                        echo htmlspecialchars(
                            $documento
                        );

                        ?>

                    </p>


                    <p>

                        <strong>Curso:</strong>

                        <?php

                        echo htmlspecialchars(
                            $estudiante["nombre_curso"]
                        );

                        ?>

                    </p>


                    <hr>


                    <div class="d-flex justify-content-center gap-2">

                        <a
                            href="estudiantes.php"
                            class="btn btn-secondary"
                        >

                            <i class="bi bi-arrow-left"></i>

                            Volver

                        </a>


                        <button
                            onclick="window.print()"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-printer"></i>

                            Imprimir

                        </button>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>