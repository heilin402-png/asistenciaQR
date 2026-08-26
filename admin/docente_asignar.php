<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
   VERIFICAR ADMIN
   ========================== */

if ($_SESSION["id_rol"] != 1) {

    header("Location: ../docente/dashboard.php");
    exit();

}


/* ==========================
   VERIFICAR ID DEL DOCENTE
   ========================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: docentes.php?error=1");
    exit();

}

$id_docente = intval($_GET["id"]);

$error = "";


/* ==========================
   CONSULTAR DOCENTE
   ========================== */

$sql_docente = "
    SELECT
        id_usuario,
        nombres,
        apellidos,
        documento
    FROM usuarios
    WHERE id_usuario = ?
    AND id_rol = 2
";

$stmt_docente = mysqli_prepare(
    $conexion,
    $sql_docente
);

mysqli_stmt_bind_param(
    $stmt_docente,
    "i",
    $id_docente
);

mysqli_stmt_execute($stmt_docente);

$resultado_docente =
    mysqli_stmt_get_result(
        $stmt_docente
    );

$docente = mysqli_fetch_assoc(
    $resultado_docente
);


if (!$docente) {

    header("Location: docentes.php?error=1");
    exit();

}


/* ==========================
   ASIGNAR CURSO
   ========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_curso = intval($_POST["id_curso"]);


    if ($id_curso <= 0) {

        $error = "Debes seleccionar un curso.";

    } else {


        /* ==========================
           VERIFICAR CURSO
           ========================== */

        $sql_curso = "
            SELECT id_curso
            FROM cursos
            WHERE id_curso = ?
            AND estado = 'ACTIVO'
        ";

        $stmt_curso = mysqli_prepare(
            $conexion,
            $sql_curso
        );

        mysqli_stmt_bind_param(
            $stmt_curso,
            "i",
            $id_curso
        );

        mysqli_stmt_execute($stmt_curso);

        $resultado_curso =
            mysqli_stmt_get_result(
                $stmt_curso
            );


        if (
            mysqli_num_rows(
                $resultado_curso
            ) == 0
        ) {

            $error =
                "El curso seleccionado no existe o está inactivo.";

        } else {


            /* ==========================
               VERIFICAR ASIGNACIÓN
               ========================== */

            $sql_verificar = "
                SELECT id_docente_curso
                FROM docente_curso
                WHERE id_usuario = ?
                AND id_curso = ?
            ";

            $stmt_verificar = mysqli_prepare(
                $conexion,
                $sql_verificar
            );

            mysqli_stmt_bind_param(
                $stmt_verificar,
                "ii",
                $id_docente,
                $id_curso
            );

            mysqli_stmt_execute(
                $stmt_verificar
            );

            $resultado_verificar =
                mysqli_stmt_get_result(
                    $stmt_verificar
                );


            if (
                mysqli_num_rows(
                    $resultado_verificar
                ) > 0
            ) {

                $error =
                    "Este docente ya tiene asignado ese curso.";

            } else {


                /* ==========================
                   INSERTAR ASIGNACIÓN
                   ========================== */

                $sql_insertar = "
                    INSERT INTO docente_curso
                    (
                        id_usuario,
                        id_curso
                    )
                    VALUES
                    (
                        ?,
                        ?
                    )
                ";

                $stmt_insertar = mysqli_prepare(
                    $conexion,
                    $sql_insertar
                );

                mysqli_stmt_bind_param(
                    $stmt_insertar,
                    "ii",
                    $id_docente,
                    $id_curso
                );


                if (
                    mysqli_stmt_execute(
                        $stmt_insertar
                    )
                ) {

                    header(
                        "Location: docentes.php?mensaje=asignado"
                    );

                    exit();

                } else {

                    $error =
                        "No se pudo asignar el curso.";

                }

            }

        }

    }

}


/* ==========================
   CONSULTAR CURSOS
   ========================== */

$sql_cursos = "
    SELECT
        id_curso,
        nombre_curso
    FROM cursos
    WHERE estado = 'ACTIVO'
    ORDER BY CAST(nombre_curso AS UNSIGNED) ASC"
";

$resultado_cursos = mysqli_query(
    $conexion,
    $sql_cursos
);


if (!$resultado_cursos) {

    die(
        "Error al consultar cursos: "
        . mysqli_error($conexion)
    );

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

    <title>Asignar curso - Asistencia QR</title>

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


<!-- ==========================
     BARRA SUPERIOR
     ========================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid">

        <a
            href="dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-qr-code-scan"></i>

            Sistema de Asistencia QR

        </a>


        <span class="text-white">

            <i class="bi bi-person-circle"></i>

            <?php

            echo htmlspecialchars(
                $_SESSION["nombre"]
            );

            ?>

        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">


        <!-- ==========================
             MENÚ
             ========================== -->

        <aside class="col-md-2 bg-dark min-vh-100 p-3">

            <div class="text-white mb-4">

                <h5>

                    <i class="bi bi-speedometer2"></i>

                    Administración

                </h5>

            </div>


            <div class="nav flex-column nav-pills">


                <a
                    href="dashboard.php"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-house"></i>

                    Dashboard

                </a>


                <a
                    href="usuarios.php"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-people"></i>

                    Usuarios

                </a>


                <a
                    href="cursos.php"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-book"></i>

                    Cursos

                </a>


                <a
                    href="docentes.php"
                    class="nav-link active mb-2"
                >

                    <i class="bi bi-person-workspace"></i>

                    Docentes

                </a>


                <hr class="text-secondary">


                <a
                    href="asistencia.php"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-clipboard-check"></i>

                    Asistencia

                </a>


                <a
                    href="#"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-cup-hot"></i>

                    Restaurante

                </a>


                <a
                    href="#"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-bar-chart"></i>

                    Reportes

                </a>


                <a
                    href="#"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-journal-text"></i>

                    Auditoría

                </a>


                <hr class="text-secondary">


                <a
                    href="../auth/logout.php"
                    class="nav-link text-danger"
                >

                    <i class="bi bi-box-arrow-right"></i>

                    Cerrar sesión

                </a>

            </div>

        </aside>


        <!-- ==========================
             CONTENIDO
             ========================== -->

        <main class="col-md-10 p-4">


            <div class="mb-4">

                <h2>

                    <i class="bi bi-bookmark-plus"></i>

                    Asignar curso

                </h2>

                <p class="text-muted">

                    Asigna un curso al docente seleccionado.

                </p>

            </div>


            <!-- ==========================
                 DOCENTE
                 ========================== -->

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body">

                    <h5 class="card-title">

                        <i class="bi bi-person-workspace"></i>

                        Docente

                    </h5>

                    <p class="mb-1">

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $docente["nombres"]
                                . " "
                                . $docente["apellidos"]
                            );

                            ?>

                        </strong>

                    </p>

                    <p class="text-muted mb-0">

                        Documento:

                        <?php

                        echo htmlspecialchars(
                            $docente["documento"]
                        );

                        ?>

                    </p>

                </div>

            </div>


            <!-- ==========================
                 ERROR
                 ========================== -->

            <?php if ($error != ""): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-triangle"></i>

                    <?php

                    echo htmlspecialchars($error);

                    ?>

                </div>

            <?php endif; ?>


            <!-- ==========================
                 FORMULARIO
                 ========================== -->

            <div class="card shadow-sm border-0">

                <div class="card-body">


                    <form method="POST">


                        <div class="mb-4">

                            <label
                                class="form-label"
                            >

                                Seleccionar curso

                            </label>


                            <select
                                name="id_curso"
                                class="form-select"
                                required
                            >

                                <option value="">

                                    -- Selecciona un curso --

                                </option>


                                <?php while (
                                    $curso =
                                    mysqli_fetch_assoc(
                                        $resultado_cursos
                                    )
                                ): ?>

                                    <option
                                        value="<?php echo $curso["id_curso"]; ?>"
                                        <?php
                                        if (
                                            isset(
                                                $_POST["id_curso"]
                                            )
                                            &&
                                            $_POST["id_curso"]
                                            == $curso["id_curso"]
                                        ) {

                                            echo "selected";

                                        }
                                        ?>
                                    >

                                        <?php

                                        echo htmlspecialchars(
                                            $curso["nombre_curso"]
                                        );

                                        ?>

                                    </option>

                                <?php endwhile; ?>


                            </select>

                        </div>


                        <hr>


                        <div
                            class="d-flex justify-content-end gap-2"
                        >


                            <a
                                href="docentes.php"
                                class="btn btn-secondary"
                            >

                                <i class="bi bi-arrow-left"></i>

                                Cancelar

                            </a>


                            <button
                                type="submit"
                                class="btn btn-success"
                            >

                                <i class="bi bi-bookmark-plus"></i>

                                Asignar curso

                            </button>


                        </div>


                    </form>

                </div>

            </div>


        </main>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>