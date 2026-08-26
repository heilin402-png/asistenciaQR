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


$error = "";


/* ==========================
   OBTENER CURSO
   ========================== */

$id_curso = 0;


/* Si viene por GET */

if (isset($_GET["id_curso"]) && is_numeric($_GET["id_curso"])) {

    $id_curso = intval($_GET["id_curso"]);

}


/* Si viene por POST */

if (
    $_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST["id_curso"])
    && is_numeric($_POST["id_curso"])
) {

    $id_curso = intval($_POST["id_curso"]);

}


/* Verificar que exista el curso */

if ($id_curso <= 0) {

    header("Location: cursos.php");
    exit();

}


$sql_curso = "SELECT
                id_curso,
                nombre_curso,
                estado
              FROM cursos
              WHERE id_curso = ?";

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

$resultado_curso = mysqli_stmt_get_result(
    $stmt_curso
);


if (mysqli_num_rows($resultado_curso) != 1) {

    header("Location: cursos.php?error=1");
    exit();

}


$curso = mysqli_fetch_assoc(
    $resultado_curso
);


/* ==========================
   GUARDAR ESTUDIANTE
   ========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);


    /* Validar campos */

    if (
        empty($nombres) ||
        empty($apellidos) ||
        empty($documento)
    ) {

        $error = "Todos los campos son obligatorios.";

    } else {


        /* ==========================
           VERIFICAR DOCUMENTO
           ========================== */

        $sql = "SELECT id_estudiante
                FROM estudiantes
                WHERE documento = ?";

        $stmt = mysqli_prepare(
            $conexion,
            $sql
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $documento
        );

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($resultado) > 0) {

            $error = "El documento ya está registrado.";

        } else {


            /* ==========================
               INSERTAR ESTUDIANTE
               ========================== */

            $sql = "INSERT INTO estudiantes
                    (
                        documento,
                        nombres,
                        apellidos,
                        id_curso,
                        estado
                    )
                    VALUES (?, ?, ?, ?, 'ACTIVO')";

            $stmt = mysqli_prepare(
                $conexion,
                $sql
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssi",
                $documento,
                $nombres,
                $apellidos,
                $id_curso
            );


            if (mysqli_stmt_execute($stmt)) {

                header(
                    "Location: curso_estudiantes.php?id="
                    . $id_curso
                    . "&mensaje=creado"
                );

                exit();

            } else {

                $error = "No se pudo registrar el estudiante.";

            }

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

    <title>
        Nuevo estudiante -
        <?php echo htmlspecialchars($curso["nombre_curso"]); ?>
    </title>

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


<!-- BARRA SUPERIOR -->

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


        <!-- MENÚ LATERAL -->

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
                    href="cursos.php"
                    class="nav-link active mb-2"
                >

                    <i class="bi bi-book"></i>

                    Cursos

                </a>


                <a
                    href="docente.php"
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


        <!-- CONTENIDO -->

        <main class="col-md-10 p-4">


            <div class="mb-4">

                <h2>

                    <i class="bi bi-person-plus"></i>

                    Nuevo estudiante

                </h2>

                <p class="text-muted">

                    Agregar estudiante al curso
                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $curso["nombre_curso"]
                        );
                        ?>
                    </strong>

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


                        <!-- ID DEL CURSO -->

                        <input
                            type="hidden"
                            name="id_curso"
                            value="<?php echo $id_curso; ?>"
                        >


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
                                    required
                                >

                                <small class="text-muted">

                                    Este documento será utilizado como
                                    identificación del código QR.

                                </small>

                            </div>


                            <!-- CURSO -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Curso

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $curso["nombre_curso"]
                                    );
                                    ?>"
                                    readonly
                                >

                            </div>

                        </div>


                        <hr>


                        <div class="d-flex gap-2">


                            <a
                                href="curso_estudiantes.php?id=<?php echo $id_curso; ?>"
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

                                Guardar estudiante

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