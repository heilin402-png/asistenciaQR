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

$error = "";

echo "ID RECIBIDO: " . $id_estudiante;


/* ==========================
   ACTUALIZAR ESTUDIANTE
   ========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);
    $id_curso = intval($_POST["id_curso"]);


    /* Validar campos */

    if (
        empty($nombres) ||
        empty($apellidos) ||
        empty($documento) ||
        empty($id_curso)
    ) {

        $error = "Todos los campos son obligatorios.";

    } else {


        /* ==========================
           VERIFICAR DOCUMENTO
           ========================== */

        $sql = "SELECT id_estudiante
                FROM estudiantes
                WHERE documento = ?
                AND id_estudiante != ?";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $documento,
            $id_estudiante
        );

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($resultado) > 0) {

            $error = "El documento ya pertenece a otro estudiante.";

        } else {


            /* ==========================
               VERIFICAR CURSO
               ========================== */

            $sql = "SELECT id_curso
                    FROM cursos
                    WHERE id_curso = ?
                    AND estado = 'ACTIVO'";

            $stmt = mysqli_prepare($conexion, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $id_curso
            );

            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);


            if (mysqli_num_rows($resultado) != 1) {

                $error = "El curso seleccionado no es válido.";

            } else {


                /* ==========================
                   ACTUALIZAR
                   ========================== */

                $sql = "UPDATE estudiantes
                        SET
                            documento = ?,
                            nombres = ?,
                            apellidos = ?,
                            id_curso = ?
                        WHERE id_estudiante = ?";

                $stmt = mysqli_prepare($conexion, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssii",
                    $documento,
                    $nombres,
                    $apellidos,
                    $id_curso,
                    $id_estudiante
                );


                if (mysqli_stmt_execute($stmt)) {

                    header(
                        "Location: estudiantes.php?mensaje=editado"
                    );

                    exit();

                } else {

                    $error = "No se pudo actualizar el estudiante.";

                }

            }

        }

    }

}


/* ==========================
   CONSULTAR ESTUDIANTE
   ========================== */

$sql = "SELECT
            id_estudiante,
            documento,
            nombres,
            apellidos,
            id_curso
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
   CONSULTAR CURSOS
   ========================== */

$sql = "SELECT
            id_curso,
            nombre_curso
        FROM cursos
        WHERE estado = 'ACTIVO'
        ORDER BY nombre_curso ASC";

$resultado_cursos = mysqli_query($conexion, $sql);

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

    <title>Editar estudiante - Asistencia QR</title>

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
             MENÚ LATERAL
             ========================== -->

        <aside class="col-md-2 bg-dark min-vh-100 p-3">

            <h5 class="text-white mb-4">

                <i class="bi bi-speedometer2"></i>

                Administración

            </h5>


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
                    href="estudiantes.php"
                    class="nav-link active mb-2"
                >

                    <i class="bi bi-mortarboard"></i>

                    Estudiantes

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
                    class="nav-link text-white mb-2"
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

                    <i class="bi bi-pencil-square"></i>

                    Editar estudiante

                </h2>

                <p class="text-muted">

                    Modificar la información del estudiante.

                </p>

            </div>


            <?php if (!empty($error)): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-triangle"></i>

                    <?php

                    echo htmlspecialchars($error);

                    ?>

                </div>

            <?php endif; ?>


            <div class="card shadow-sm border-0">

                <div class="card-body p-4">


                    <form method="POST">


                        <div class="row">


                            <!-- NOMBRES -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Nombres

                                </label>

                                <input
                                    type="text"
                                    name="nombres"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["nombres"]
                                    );
                                    ?>"
                                    required
                                >

                            </div>


                            <!-- APELLIDOS -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Apellidos

                                </label>

                                <input
                                    type="text"
                                    name="apellidos"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["apellidos"]
                                    );
                                    ?>"
                                    required
                                >

                            </div>


                            <!-- DOCUMENTO -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Documento

                                </label>

                                <input
                                    type="text"
                                    name="documento"
                                    class="form-control"
                                    maxlength="20"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $estudiante["documento"]
                                    );
                                    ?>"
                                    required
                                >

                            </div>


                            <!-- CURSO -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Curso

                                </label>

                                <select
                                    name="id_curso"
                                    class="form-select"
                                    required
                                >

                                    <option value="">

                                        Seleccionar curso

                                    </option>


                                    <?php while (
                                        $curso =
                                        mysqli_fetch_assoc(
                                            $resultado_cursos
                                        )
                                    ): ?>

                                        <option
                                            value="<?php
                                            echo $curso["id_curso"];
                                            ?>"
                                            <?php
                                            if (
                                                $curso["id_curso"]
                                                == $estudiante["id_curso"]
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

                        </div>


                        <hr>


                        <div class="d-flex gap-2">


                            <a
                                href="estudiantes.php"
                                class="btn btn-secondary"
                            >

                                <i class="bi bi-arrow-left"></i>

                                Cancelar

                            </a>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-save"></i>

                                Guardar cambios

                            </button>


                        </div>


                    </form>


                </div>

            </div>


        </main>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>