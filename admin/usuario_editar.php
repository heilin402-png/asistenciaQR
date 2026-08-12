<?php

session_start();

require_once("../config/conexion.php");

/* Verificar sesión */
if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/login.php");
    exit();

}

/* Verificar administrador */
if ($_SESSION["id_rol"] != 1) {

    header("Location: ../docente/dashboard.php");
    exit();

}


/* Verificar ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: usuarios.php");
    exit();

}

$id_usuario = intval($_GET["id"]);

$error = "";


/* ==========================
   PROCESAR FORMULARIO
   ========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);
    $correo = trim($_POST["correo"]);
    $id_rol = intval($_POST["id_rol"]);
    $password = trim($_POST["password"]);


    /* Validaciones */

    if (
        empty($nombres) ||
        empty($apellidos) ||
        empty($documento) ||
        empty($correo) ||
        empty($id_rol)
    ) {

        $error = "Los campos obligatorios deben estar completos.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "El correo electrónico no es válido.";

    } else {


        /* Verificar documento */

        $sql = "SELECT id_usuario
                FROM usuarios
                WHERE documento = ?
                AND id_usuario != ?";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $documento,
            $id_usuario
        );

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($resultado) > 0) {

            $error = "El documento ya pertenece a otro usuario.";

        } else {


            /* Verificar correo */

            $sql = "SELECT id_usuario
                    FROM usuarios
                    WHERE correo = ?
                    AND id_usuario != ?";

            $stmt = mysqli_prepare($conexion, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $correo,
                $id_usuario
            );

            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);


            if (mysqli_num_rows($resultado) > 0) {

                $error = "El correo ya pertenece a otro usuario.";

            } else {


                /* ==========================
                   ACTUALIZAR DATOS
                   ========================== */

                if (!empty($password)) {

                    $password_hash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $sql = "UPDATE usuarios
                            SET nombres = ?,
                                apellidos = ?,
                                documento = ?,
                                correo = ?,
                                id_rol = ?,
                                password = ?
                            WHERE id_usuario = ?";

                    $stmt = mysqli_prepare($conexion, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssssisi",
                        $nombres,
                        $apellidos,
                        $documento,
                        $correo,
                        $id_rol,
                        $password_hash,
                        $id_usuario
                    );

                } else {

                    $sql = "UPDATE usuarios
                            SET nombres = ?,
                                apellidos = ?,
                                documento = ?,
                                correo = ?,
                                id_rol = ?
                            WHERE id_usuario = ?";

                    $stmt = mysqli_prepare($conexion, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssssii",
                        $nombres,
                        $apellidos,
                        $documento,
                        $correo,
                        $id_rol,
                        $id_usuario
                    );

                }


                if (mysqli_stmt_execute($stmt)) {

                    header("Location: usuarios.php?mensaje=editado");

                    exit();

                } else {

                    $error = "No se pudo actualizar el usuario.";

                }

            }

        }

    }

}


/* ==========================
   CONSULTAR USUARIO
   ========================== */

$sql = "SELECT *
        FROM usuarios
        WHERE id_usuario = ?";

$stmt = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_usuario
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($resultado) != 1) {

    header("Location: usuarios.php?error=noexiste");
    exit();

}


$usuario = mysqli_fetch_assoc($resultado);

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


        <!-- ==========================
             CONTENIDO
             ========================== -->

        <main class="col-md-10 p-4">


            <div class="mb-4">

                <h2>

                    <i class="bi bi-pencil-square"></i>

                    Editar usuario

                </h2>

                <p class="text-muted">

                    Modificar información del usuario.

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
                                    value="<?php echo htmlspecialchars($usuario["nombres"]); ?>"
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
                                    value="<?php echo htmlspecialchars($usuario["apellidos"]); ?>"
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
                                    value="<?php echo htmlspecialchars($usuario["documento"]); ?>"
                                    required
                                >

                            </div>


                            <!-- CORREO -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Correo

                                </label>

                                <input
                                    type="email"
                                    name="correo"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($usuario["correo"]); ?>"
                                    required
                                >

                            </div>


                            <!-- CONTRASEÑA -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Nueva contraseña

                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                >

                                <small class="text-muted">

                                    Déjala vacía si no quieres cambiarla.

                                </small>

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

                                    <option
                                        value="1"
                                        <?php
                                        if ($usuario["id_rol"] == 1) {
                                            echo "selected";
                                        }
                                        ?>
                                    >

                                        ADMINISTRADOR

                                    </option>


                                    <option
                                        value="2"
                                        <?php
                                        if ($usuario["id_rol"] == 2) {
                                            echo "selected";
                                        }
                                        ?>
                                    >

                                        DOCENTE

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