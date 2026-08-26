<?php

session_start();

require_once("../config/conexion.php");

/* Verificar sesión */
if (!isset($_SESSION["id_usuario"])) {

    header("Location: /asistenciaQR/auth/login.php");
    exit();

}

/* Solo administrador */
if ($_SESSION["id_rol"] != 1) {

    header("Location: /asistenciaQR/docente/dashboard.php");
    exit();

}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $documento = trim($_POST["documento"]);
    $correo = trim($_POST["correo"]);
    $password = trim($_POST["password"]);
    $id_rol = intval($_POST["id_rol"]);

    /* Validar campos */

    if (
        empty($nombres) ||
        empty($apellidos) ||
        empty($documento) ||
        empty($correo) ||
        empty($password) ||
        empty($id_rol)
    ) {

        $error = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "El correo electrónico no es válido.";

    } elseif (strlen($password) < 6) {

        $error = "La contraseña debe tener mínimo 6 caracteres.";

    } else {

        /* Verificar documento */

        $sql = "SELECT id_usuario
                FROM usuarios
                WHERE documento = ?";

        $stmt = mysqli_prepare($conexion, $sql);

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

            /* Verificar correo */

            $sql = "SELECT id_usuario
                    FROM usuarios
                    WHERE correo = ?";

            $stmt = mysqli_prepare($conexion, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $correo
            );

            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($resultado) > 0) {

                $error = "El correo ya está registrado.";

            } else {

                /* Crear hash */

                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                /* Insertar */

                $sql = "INSERT INTO usuarios
                        (
                            id_rol,
                            nombres,
                            apellidos,
                            documento,
                            correo,
                            password,
                            estado
                        )
                        VALUES (?, ?, ?, ?, ?, ?, 'ACTIVO')";

                $stmt = mysqli_prepare(
                    $conexion,
                    $sql
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "isssss",
                    $id_rol,
                    $nombres,
                    $apellidos,
                    $documento,
                    $correo,
                    $password_hash
                );

                if (mysqli_stmt_execute($stmt)) {

                    header(
                        "Location: /asistenciaQR/admin/usuarios.php?mensaje=creado"
                    );

                    exit();

                } else {

                    $error = "No se pudo crear el usuario.";

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

    <title>Nuevo usuario - Asistencia QR</title>

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

<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid">

        <a
            href="/asistenciaQR/admin/dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-qr-code-scan"></i>

            Sistema de Asistencia QR

        </a>

        <span class="text-white">

            <i class="bi bi-person-circle"></i>

            <?php
            echo htmlspecialchars($_SESSION["nombre"]);
            ?>

        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">

        <!-- MENÚ -->

        <aside class="col-md-2 bg-dark min-vh-100 p-3">

            <h5 class="text-white mb-4">

                <i class="bi bi-speedometer2"></i>

                Administración

            </h5>

            <div class="nav flex-column nav-pills">

                <a
                    href="/asistenciaQR/admin/dashboard.php"
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-house"></i>

                    Dashboard

                </a>

                <a
                    href="/asistenciaQR/admin/usuarios.php"
                    class="nav-link active mb-2"
                >

                    <i class="bi bi-people"></i>

                    Usuarios

                </a>

                <a
                    href="#"
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
                    href="/asistenciaQR/auth/logout.php"
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

                    Nuevo usuario

                </h2>

                <p class="text-muted">

                    Registrar un nuevo usuario en el sistema.

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

                            </div>


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
                                >

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Contraseña
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                                <small class="text-muted">

                                    Mínimo 6 caracteres.

                                </small>

                            </div>


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

                                    <option value="2">
                                        DOCENTE
                                    </option>

                                    <option value="1">
                                        ADMINISTRADOR
                                    </option>

                                </select>

                            </div>

                        </div>


                        <hr>


                        <div class="d-flex gap-2">

                            <a
                                href="/asistenciaQR/admin/usuarios.php"
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

                                Guardar usuario

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

</body>

</html>