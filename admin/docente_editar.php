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
   VERIFICAR ID
   ========================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: docentes.php?error=1");
    exit();

}

$id_usuario = intval($_GET["id"]);

$error = "";


/* ==========================
   CONSULTAR DOCENTE
   ========================== */

$sql = "
    SELECT
        id_usuario,
        nombres,
        apellidos,
        documento,
        correo,
        estado
    FROM usuarios
    WHERE id_usuario = ?
    AND id_rol = 2
";

$stmt = mysqli_prepare(
    $conexion,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_usuario
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

$docente = mysqli_fetch_assoc($resultado);


if (!$docente) {

    header("Location: docentes.php?error=1");
    exit();

}


/* ==========================
   ACTUALIZAR DOCENTE
   ========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);
    $correo = trim($_POST["correo"]);
    $estado = $_POST["estado"];


    /* ==========================
       VALIDAR CAMPOS
       ========================== */

    if (
        $nombres == "" ||
        $apellidos == "" ||
        $documento == "" ||
        $correo == ""
    ) {

        $error = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "El correo electrónico no es válido.";

    } elseif (
        $estado != "ACTIVO" &&
        $estado != "INACTIVO"
    ) {

        $error = "El estado seleccionado no es válido.";

    } else {


        /* ==========================
           VERIFICAR DOCUMENTO
           ========================== */

        $sql_documento = "
            SELECT id_usuario
            FROM usuarios
            WHERE documento = ?
            AND id_usuario != ?
        ";

        $stmt_documento = mysqli_prepare(
            $conexion,
            $sql_documento
        );

        mysqli_stmt_bind_param(
            $stmt_documento,
            "si",
            $documento,
            $id_usuario
        );

        mysqli_stmt_execute(
            $stmt_documento
        );

        $resultado_documento =
            mysqli_stmt_get_result(
                $stmt_documento
            );


        if (
            mysqli_num_rows(
                $resultado_documento
            ) > 0
        ) {

            $error =
                "Ya existe otro usuario con ese documento.";

        } else {


            /* ==========================
               VERIFICAR CORREO
               ========================== */

            $sql_correo = "
                SELECT id_usuario
                FROM usuarios
                WHERE correo = ?
                AND id_usuario != ?
            ";

            $stmt_correo = mysqli_prepare(
                $conexion,
                $sql_correo
            );

            mysqli_stmt_bind_param(
                $stmt_correo,
                "si",
                $correo,
                $id_usuario
            );

            mysqli_stmt_execute(
                $stmt_correo
            );

            $resultado_correo =
                mysqli_stmt_get_result(
                    $stmt_correo
                );


            if (
                mysqli_num_rows(
                    $resultado_correo
                ) > 0
            ) {

                $error =
                    "Ya existe otro usuario con ese correo.";

            } else {


                /* ==========================
                   ACTUALIZAR
                   ========================== */

                $sql_update = "
                    UPDATE usuarios
                    SET
                        nombres = ?,
                        apellidos = ?,
                        documento = ?,
                        correo = ?,
                        estado = ?
                    WHERE id_usuario = ?
                    AND id_rol = 2
                ";

                $stmt_update = mysqli_prepare(
                    $conexion,
                    $sql_update
                );

                mysqli_stmt_bind_param(
                    $stmt_update,
                    "sssssi",
                    $nombres,
                    $apellidos,
                    $documento,
                    $correo,
                    $estado,
                    $id_usuario
                );


                if (
                    mysqli_stmt_execute(
                        $stmt_update
                    )
                ) {

                    header(
                        "Location: docentes.php?mensaje=editado"
                    );

                    exit();

                } else {

                    $error =
                        "No se pudo actualizar el docente.";

                }

            }

        }

    }


    /* ==========================
       MANTENER DATOS DEL FORMULARIO
       ========================== */

    $docente["nombres"] = $nombres;
    $docente["apellidos"] = $apellidos;
    $docente["documento"] = $documento;
    $docente["correo"] = $correo;
    $docente["estado"] = $estado;

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

    <title>Editar docente - Asistencia QR</title>

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

                    <i class="bi bi-pencil-square"></i>

                    Editar docente

                </h2>

                <p class="text-muted">

                    Actualiza la información del docente.

                </p>

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


                    <form
                        method="POST"
                        autocomplete="off"
                    >


                        <div class="row">


                            <!-- NOMBRES -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                >

                                    Nombres

                                </label>

                                <input
                                    type="text"
                                    name="nombres"
                                    class="form-control"
                                    required
                                    value="<?php echo htmlspecialchars($docente["nombres"]); ?>"
                                >

                            </div>


                            <!-- APELLIDOS -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                >

                                    Apellidos

                                </label>

                                <input
                                    type="text"
                                    name="apellidos"
                                    class="form-control"
                                    required
                                    value="<?php echo htmlspecialchars($docente["apellidos"]); ?>"
                                >

                            </div>


                            <!-- DOCUMENTO -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                >

                                    Documento

                                </label>

                                <input
                                    type="text"
                                    name="documento"
                                    class="form-control"
                                    required
                                    value="<?php echo htmlspecialchars($docente["documento"]); ?>"
                                >

                            </div>


                            <!-- CORREO -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                >

                                    Correo electrónico

                                </label>

                                <input
                                    type="email"
                                    name="correo"
                                    class="form-control"
                                    required
                                    value="<?php echo htmlspecialchars($docente["correo"]); ?>"
                                >

                            </div>

                            <!-- ROL -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                >

                                    Rol

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="DOCENTE"
                                    disabled
                                >

                                <small class="text-muted">

                                    El rol no puede modificarse desde aquí.

                                </small>

                            </div>


                        </div>


                        <hr>


                        <div class="d-flex justify-content-end gap-2">


                            <a
                                href="docentes.php"
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


        </main>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>