<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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


/* ==========================
   CONSULTAR ESTUDIANTES
   ========================== */

$sql = "SELECT
            e.id_estudiante,
            e.documento,
            e.nombres,
            e.apellidos,
            e.id_curso,
            c.nombre_curso,
            e.estado,
            e.fecha_creacion
        FROM estudiantes e
        INNER JOIN cursos c
            ON e.id_curso = c.id_curso
        ORDER BY e.id_estudiante DESC";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {

    die(
        "Error al consultar estudiantes: "
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

    <title>Estudiantes - Asistencia QR</title>

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


            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2>

                        <i class="bi bi-mortarboard"></i>

                        Estudiantes

                    </h2>

                    <p class="text-muted mb-0">

                        Administración de estudiantes del sistema.

                    </p>

                </div>


                <a
                    href="estudiante_nuevo.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-person-plus"></i>

                    Nuevo estudiante

                </a>

            </div>


            <!-- ==========================
                 MENSAJES
                 ========================== -->

            <?php if (isset($_GET["mensaje"])): ?>

                <?php if ($_GET["mensaje"] == "creado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Estudiante registrado correctamente.

                    </div>

                <?php elseif ($_GET["mensaje"] == "editado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Estudiante actualizado correctamente.

                    </div>

                <?php elseif ($_GET["mensaje"] == "estado"): ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Estado del estudiante actualizado.

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

                        <table class="table table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th>ID</th>

                                    <th>Estudiante</th>

                                    <th>Documento</th>

                                    <th>Curso</th>

                                    <th>Estado</th>

                                    <th>Fecha creación</th>

                                    <th>Acciones</th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php if (mysqli_num_rows($resultado) > 0): ?>


                                <?php while (
                                    $estudiante =
                                    mysqli_fetch_assoc($resultado)
                                ): ?>


                                    <tr>


                                        <!-- ID -->

                                        <td>

                                            <?php

                                            echo $estudiante[
                                                "id_estudiante"
                                            ];

                                            ?>

                                        </td>


                                        <!-- NOMBRE -->

                                        <td>

                                            <strong>

                                                <?php

                                                echo htmlspecialchars(
                                                    $estudiante["nombres"]
                                                    . " "
                                                    . $estudiante["apellidos"]
                                                );

                                                ?>

                                            </strong>

                                        </td>


                                        <!-- DOCUMENTO -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $estudiante["documento"]
                                            );

                                            ?>

                                        </td>


                                        <!-- CURSO -->

                                        <td>

                                            <span class="badge text-bg-primary">

                                                <?php

                                                echo htmlspecialchars(
                                                    $estudiante["nombre_curso"]
                                                );

                                                ?>

                                            </span>

                                        </td>


                                        <!-- ESTADO -->

                                        <td>

                                            <?php if (
                                                $estudiante["estado"]
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


                                        <!-- FECHA -->

                                        <td>

                                            <?php

                                            echo date(
                                                "d/m/Y H:i",
                                                strtotime(
                                                    $estudiante[
                                                        "fecha_creacion"
                                                    ]
                                                )
                                            );

                                            ?>

                                        </td>


                                        <!-- ACCIONES -->

 <td>

    <!-- EDITAR -->

    <a
        href="estudiante_editar.php?id=<?php echo $estudiante["id_estudiante"]; ?>"
        class="btn btn-sm btn-outline-primary"
        title="Editar"
    >

        <i class="bi bi-pencil"></i>

    </a>


    <!-- CAMBIAR ESTADO -->

    <a
        href="estudiante_estado.php?id=<?php echo $estudiante["id_estudiante"]; ?>"
        class="btn btn-sm btn-outline-warning"
        title="Cambiar estado"
    >

        <i class="bi bi-arrow-repeat"></i>

    </a>


    <!-- VER QR -->

    <a
        href="estudiante_qr.php?id=<?php echo $estudiante["id_estudiante"]; ?>"
        class="btn btn-sm btn-outline-success"
        title="Ver código QR"
    >

        <i class="bi bi-qr-code"></i>

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
                                            class="bi bi-person-x fs-3"
                                        ></i>

                                        <br>

                                        No hay estudiantes registrados.

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
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>