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
   CONSULTAR DOCENTES
   ========================== */

$sql = "SELECT
            u.id_usuario,
            u.nombres,
            u.apellidos,
            u.documento,
            u.correo,
            u.estado,
            GROUP_CONCAT(
                DISTINCT c.nombre_curso
                ORDER BY c.nombre_curso
                SEPARATOR ', '
            ) AS cursos
        FROM usuarios u

        LEFT JOIN docente_curso dc
            ON u.id_usuario = dc.id_usuario

        LEFT JOIN cursos c
            ON dc.id_curso = c.id_curso

        WHERE u.id_rol = 2

        GROUP BY
            u.id_usuario,
            u.nombres,
            u.apellidos,
            u.documento,
            u.correo,
            u.estado

        ORDER BY u.id_usuario DESC";


$resultado = mysqli_query($conexion, $sql);


if (!$resultado) {

    die(
        "Error al consultar docentes: "
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

    <title>Docentes - Asistencia QR</title>

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


            <div
                class="d-flex justify-content-between align-items-center mb-4"
            >

                <div>

                    <h2>

                        <i class="bi bi-person-workspace"></i>

                        Docentes

                    </h2>

                    <p class="text-muted mb-0">

                        Administración de docentes del sistema.

                    </p>

                </div>


                <a
                    href="docente_nuevo.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-plus-circle"></i>

                    Nuevo docente

                </a>

            </div>


            <!-- ==========================
                 MENSAJES
                 ========================== -->

            <?php if (isset($_GET["mensaje"])): ?>


                <?php if ($_GET["mensaje"] == "creado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Docente creado correctamente.

                    </div>


                <?php elseif ($_GET["mensaje"] == "editado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Docente actualizado correctamente.

                    </div>


                <?php elseif ($_GET["mensaje"] == "asignado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Curso asignado correctamente.

                    </div>


                <?php elseif ($_GET["mensaje"] == "desasignado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Curso desasignado correctamente.

                    </div>


                <?php elseif ($_GET["mensaje"] == "estado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Estado del docente actualizado.

                    </div>

                <?php endif; ?>


            <?php endif; ?>


            <?php if (isset($_GET["error"])): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-triangle"></i>

                    No se pudo realizar la operación.

                </div>

            <?php endif; ?>


            <!-- ==========================
                 TABLA
                 ========================== -->

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle"
                        >

                            <thead class="table-light">

                                <tr>

                                    <th>ID</th>

                                    <th>Docente</th>

                                    <th>Documento</th>

                                    <th>Correo</th>

                                    <th>Curso(s)</th>

                                    <th>Estado</th>

                                    <th>Acciones</th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php if (
                                mysqli_num_rows($resultado) > 0
                            ): ?>


                                <?php while (
                                    $docente = mysqli_fetch_assoc($resultado)
                                ): ?>


                                    <tr>


                                        <!-- ID -->

                                        <td>

                                            <?php

                                            echo $docente["id_usuario"];

                                            ?>

                                        </td>


                                        <!-- DOCENTE -->

                                        <td>

                                            <strong>

                                                <?php

                                                echo htmlspecialchars(
                                                    $docente["nombres"]
                                                    . " "
                                                    . $docente["apellidos"]
                                                );

                                                ?>

                                            </strong>

                                        </td>


                                        <!-- DOCUMENTO -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $docente["documento"]
                                            );

                                            ?>

                                        </td>


                                        <!-- CORREO -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $docente["correo"]
                                            );

                                            ?>

                                        </td>


                                        <!-- CURSOS -->

                                        <td>

                                            <?php if (
                                                !empty($docente["cursos"])
                                            ): ?>

                                                <span
                                                    class="badge text-bg-info"
                                                >

                                                    <i class="bi bi-book"></i>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $docente["cursos"]
                                                    );

                                                    ?>

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="text-muted"
                                                >

                                                    Sin asignar

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- ESTADO -->

                                        <td>

                                            <?php if (
                                                $docente["estado"]
                                                == "ACTIVO"
                                            ): ?>

                                                <span
                                                    class="badge text-bg-success"
                                                >

                                                    ACTIVO

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="badge text-bg-secondary"
                                                >

                                                    INACTIVO

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- ACCIONES -->

                                        <td>


                                            <!-- EDITAR -->

                                            <a
                                                href="docente_editar.php?id=<?php echo $docente["id_usuario"]; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Editar"
                                            >

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                            <!-- ASIGNAR CURSO -->

                                            <a
                                                href="docente_asignar.php?id=<?php echo $docente["id_usuario"]; ?>"
                                                class="btn btn-sm btn-outline-success"
                                                title="Asignar curso"
                                            >

                                                <i class="bi bi-bookmark-plus"></i>

                                            </a>


                                            <!-- DESASIGNAR CURSO -->

                                            <a
                                                href="docente_desasignar.php?id=<?php echo $docente["id_usuario"]; ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Desasignar curso"
                                            >

                                                <i class="bi bi-bookmark-dash"></i>

                                            </a>


                                            <!-- CAMBIAR ESTADO -->

                                            <a
                                                href="docente_estado.php?id=<?php echo $docente["id_usuario"]; ?>"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Cambiar estado"
                                            >

                                                <i class="bi bi-arrow-repeat"></i>

                                            </a>


                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            <?php else: ?>


                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-4"
                                    >

                                        <i
                                            class="bi bi-person-workspace fs-3"
                                        ></i>

                                        <br>

                                        No hay docentes registrados.

                                    </td>

                                </tr>


                            <?php endif; ?>


                            </tbody>

                        </table>

                    </div>

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