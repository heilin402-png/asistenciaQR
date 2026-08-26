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


$error = "";


/* ==========================
   GUARDAR DOCENTE
   ========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];


    /* ==========================
       VALIDAR CAMPOS
       ========================== */

    if (
        $nombres == "" ||
        $apellidos == "" ||
        $documento == "" ||
        $correo == "" ||
        $password == ""
    ) {

        $error = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "El correo electrónico no es válido.";

    } else {


        /* ==========================
           VERIFICAR DOCUMENTO
           ========================== */

        $sql_verificar = "
            SELECT id_usuario
            FROM usuarios
            WHERE documento = ?
        ";

        $stmt_verificar = mysqli_prepare(
            $conexion,
            $sql_verificar
        );

        mysqli_stmt_bind_param(
            $stmt_verificar,
            "s",
            $documento
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
                "Ya existe un usuario con ese documento.";

        } else {


            /* ==========================
               VERIFICAR CORREO
               ========================== */

            $sql_correo = "
                SELECT id_usuario
                FROM usuarios
                WHERE correo = ?
            ";

            $stmt_correo = mysqli_prepare(
                $conexion,
                $sql_correo
            );

            mysqli_stmt_bind_param(
                $stmt_correo,
                "s",
                $correo
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
                    "Ya existe un usuario con ese correo.";

            } else {


                /* ==========================
                   ENCRIPTAR CONTRASEÑA
                   ========================== */

                $password_hash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                /* ==========================
                   CREAR DOCENTE
                   ========================== */

                $sql = "
                    INSERT INTO usuarios
                    (
                        id_rol,
                        nombres,
                        apellidos,
                        documento,
                        correo,
                        password,
                        estado
                    )
                    VALUES
                    (
                        2,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        'ACTIVO'
                    )
                ";

                $stmt = mysqli_prepare(
                    $conexion,
                    $sql
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssss",
                    $nombres,
                    $apellidos,
                    $documento,
                    $correo,
                    $password_hash
                );


                if (
                    mysqli_stmt_execute($stmt)
                ) {

                    header(
                        "Location: docentes.php?mensaje=creado"
                    );

                    exit();

                } else {

                    $error =
                        "No se pudo crear el docente.";

                }

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

    <title>Nuevo docente - Asistencia QR</title>

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

                    <i class="bi bi-person-plus"></i>

                    Nuevo docente

                </h2>

                <p class="text-muted">

                    Registra un nuevo docente en el sistema.

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
                                    value="<?php echo isset($_POST["nombres"]) ? htmlspecialchars($_POST["nombres"]) : ""; ?>"
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
                                    value="<?php echo isset($_POST["apellidos"]) ? htmlspecialchars($_POST["apellidos"]) : ""; ?>"
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
                                    value="<?php echo isset($_POST["documento"]) ? htmlspecialchars($_POST["documento"]) : ""; ?>"
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
                                    value="<?php echo isset($_POST["correo"]) ? htmlspecialchars($_POST["correo"]) : ""; ?>"
                                >

                            </div>


                            <!-- CONTRASEÑA -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label"
                                >

                                    Contraseña

                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    required
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

                                    El rol se asigna automáticamente.

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

                                Guardar docente

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