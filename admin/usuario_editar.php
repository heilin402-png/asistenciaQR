<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once("../config/conexion.php");


/* ==============================
   VERIFICAR SESIÓN
   ============================== */

if (!isset($_SESSION["id_usuario"])) {

    header("Location: /asistencia_qr/auth/login.php");
    exit();

}


/* ==============================
   VERIFICAR ADMINISTRADOR
   ============================== */

if ($_SESSION["id_rol"] != 1) {

    header("Location: /asistencia_qr/docente/dashboard.php");
    exit();

}


/* ==============================
   VERIFICAR ID
   ============================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: usuarios.php");
    exit();

}

$id_usuario = intval($_GET["id"]);


/* ==============================
   BUSCAR USUARIO
   ============================== */

$sql = "SELECT *
        FROM usuarios
        WHERE id_usuario = ?";

$stmt = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_usuario);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($resultado) != 1) {

    header("Location: usuarios.php");
    exit();

}


$usuario = mysqli_fetch_assoc($resultado);


/* ==============================
   PROCESAR FORMULARIO
   ============================== */

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);
    $correo = trim($_POST["correo"]);
    $id_rol = intval($_POST["id_rol"]);
    $password = trim($_POST["password"]);


    /* ==============================
       VALIDACIONES
       ============================== */

    if (
        empty($nombres) ||
        empty($apellidos) ||
        empty($documento) ||
        empty($correo) ||
        $id_rol <= 0
    ) {

        $error = "Todos los campos obligatorios deben estar completos.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "El correo electrónico no es válido.";

    } else {


        /* ==============================
           VERIFICAR DOCUMENTO
           ============================== */

        $sql_documento = "SELECT id_usuario
                          FROM usuarios
                          WHERE documento = ?
                          AND id_usuario != ?";

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

        mysqli_stmt_execute($stmt_documento);

        $resultado_documento = mysqli_stmt_get_result(
            $stmt_documento
        );


        if (mysqli_num_rows($resultado_documento) > 0) {

            $error = "El documento ya está registrado por otro usuario.";

        } else {


            /* ==============================
               VERIFICAR CORREO
               ============================== */

            $sql_correo = "SELECT id_usuario
                           FROM usuarios
                           WHERE correo = ?
                           AND id_usuario != ?";

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

            mysqli_stmt_execute($stmt_correo);

            $resultado_correo = mysqli_stmt_get_result(
                $stmt_correo
            );


            if (mysqli_num_rows($resultado_correo) > 0) {

                $error = "El correo ya está registrado por otro usuario.";

            } else {


                /* ==============================
                   ACTUALIZAR SIN CAMBIAR PASSWORD
                   ============================== */

                if (empty($password)) {

                    $sql_update = "UPDATE usuarios
                                   SET
                                       id_rol = ?,
                                       nombres = ?,
                                       apellidos = ?,
                                       documento = ?,
                                       correo = ?
                                   WHERE id_usuario = ?";

                    $stmt_update = mysqli_prepare(
                        $conexion,
                        $sql_update
                    );

                    mysqli_stmt_bind_param(
                        $stmt_update,
                        "issssi",
                        $id_rol,
                        $nombres,
                        $apellidos,
                        $documento,
                        $correo,
                        $id_usuario
                    );

                } else {


                    /* ==============================
                       ACTUALIZAR CON NUEVA PASSWORD
                       ============================== */

                    if (strlen($password) < 6) {

                        $error = "La contraseña debe tener mínimo 6 caracteres.";

                    } else {

                        $password_hash = password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );


                        $sql_update = "UPDATE usuarios
                                       SET
                                           id_rol = ?,
                                           nombres = ?,
                                           apellidos = ?,
                                           documento = ?,
                                           correo = ?,
                                           password = ?
                                       WHERE id_usuario = ?";

                        $stmt_update = mysqli_prepare(
                            $conexion,
                            $sql_update
                        );

                        mysqli_stmt_bind_param(
                            $stmt_update,
                            "isssssi",
                            $id_rol,
                            $nombres,
                            $apellidos,
                            $documento,
                            $correo,
                            $password_hash,
                            $id_usuario
                        );

                    }

                }


                /* ==============================
                   GUARDAR CAMBIOS
                   ============================== */

                if (empty($error)) {

                    if (mysqli_stmt_execute($stmt_update)) {

                        /*
                         * Si el usuario editado es el administrador
                         * que tiene la sesión actual, actualizamos
                         * también el nombre de la sesión.
                         */

                        if ($id_usuario == $_SESSION["id_usuario"]) {

                            $_SESSION["nombre"] = $nombres;

                            $_SESSION["id_rol"] = $id_rol;

                        }


                        header(
                            "Location: usuarios.php?mensaje=editado"
                        );

                        exit();

                    } else {

                        $error = "No se pudieron guardar los cambios.";

                    }

                }

            }

        }

    }


    /*
     * Si hubo un error, mostramos nuevamente
     * los datos introducidos.
     */

    $usuario["nombres"] = $nombres;
    $usuario["apellidos"] = $apellidos;
    $usuario["documento"] = $documento;
    $usuario["correo"] = $correo;
    $usuario["id_rol"] = $id_rol;

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

    <title>Editar usuario - Asistencia QR</title>


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


<!-- ==============================
     BARRA SUPERIOR
     ============================== -->

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


        <!-- ==============================
             MENÚ LATERAL
             ============================== -->

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
                    class="nav-link active mb-2"
                >

                    <i class="bi bi-people"></i>

                    Usuarios

                </a>


                <a
                    href="#"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-mortarboard"></i>

                    Estudiantes

                </a>


                <a
                    href="#"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-book"></i>

                    Cursos

                </a>


                <a
                    href="#"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-person-workspace"></i>

                    Docentes

                </a>


                <hr class="text-secondary">


                <a
                    href="#"
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


        <!-- ==============================
             CONTENIDO
             ============================== -->

        <main class="col-md-10 p-4">


            <div class="mb-4">

                <h2>

                    <i class="bi bi-pencil-square"></i>

                    Editar usuario

                </h2>

                <p class="text-muted">

                    Modifica la información del usuario.

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
                                    required
                                    value="<?php

                                    echo htmlspecialchars(
                                        $usuario["nombres"]
                                    );

                                    ?>"
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
                                    value="<?php

                                    echo htmlspecialchars(
                                        $usuario["apellidos"]
                                    );

                                    ?>"
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
                                    value="<?php

                                    echo htmlspecialchars(
                                        $usuario["documento"]
                                    );

                                    ?>"
                                >

                            </div>


                            <!-- CORREO -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Correo electrónico

                                </label>

                                <input
                                    type="email"
                                    name="correo"
                                    class="form-control"
                                    maxlength="150"
                                    required
                                    value="<?php

                                    echo htmlspecialchars(
                                        $usuario["correo"]
                                    );

                                    ?>"
                                >

                            </div>


                            <!-- PASSWORD -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Nueva contraseña

                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    minlength="6"
                                >

                                <div class="form-text">

                                    Déjalo vacío si no deseas cambiarla.

                                </div>

                            </div>


                            <!-- ROL -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Rol

                                </label>

                                <select
                                    name="id_rol"
                                    class="form-select"
                                    required
                                >

                                    <option value="">

                                        Seleccionar rol

                                    </option>


                                    <option
                                        value="2"
                                        <?php

                                        if (
                                            $usuario["id_rol"] == 2
                                        ) {
                                            echo "selected";
                                        }

                                        ?>
                                    >

                                        DOCENTE

                                    </option>


                                    <option
                                        value="1"
                                        <?php

                                        if (
                                            $usuario["id_rol"] == 1
                                        ) {
                                            echo "selected";
                                        }

                                        ?>
                                    >

                                        ADMINISTRADOR

                                    </option>

                                </select>

                            </div>


                        </div>


                        <hr>


                        <div class="d-flex gap-2">

                            <a
                                href="usuarios.php"
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