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
   VERIFICAR ID DEL CURSO
   ========================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: cursos.php");
    exit();

}

$id_curso = intval($_GET["id"]);


/* ==========================
   CONSULTAR CURSO
   ========================== */

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
   CONSULTAR ESTUDIANTES
   ========================== */

$sql_estudiantes = "SELECT
                        id_estudiante,
                        documento,
                        nombres,
                        apellidos,
                        estado
                    FROM estudiantes
                    WHERE id_curso = ?
                    ORDER BY apellidos ASC, nombres ASC";

$stmt_estudiantes = mysqli_prepare(
    $conexion,
    $sql_estudiantes
);

mysqli_stmt_bind_param(
    $stmt_estudiantes,
    "i",
    $id_curso
);

mysqli_stmt_execute(
    $stmt_estudiantes
);

$resultado_estudiantes = mysqli_stmt_get_result(
    $stmt_estudiantes
);

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
        Estudiantes - <?php
        echo htmlspecialchars(
            $curso["nombre_curso"]
        );
        ?>
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
                    class="nav-link text-white mb-2"
                >

                    <i class="bi bi-mortarboard"></i>

                    Estudiantes

                </a>


                <a
                    href="cursos.php"
                    class="nav-link active mb-2"
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


            <!-- ENCABEZADO -->

            <div
                class="d-flex justify-content-between align-items-center mb-4"
            >

                <div>

                    <h2>

                        <i class="bi bi-book"></i>

                        Curso:

                        <?php

                        echo htmlspecialchars(
                            $curso["nombre_curso"]
                        );

                        ?>

                    </h2>

                    <p class="text-muted mb-0">

                        Estudiantes registrados en este curso.

                    </p>

                </div>


                <a
                    href="estudiante_nuevo.php?id_curso=<?php echo $id_curso; ?>"
                    class="btn btn-primary"
                >

                    <i class="bi bi-person-plus"></i>

                    Agregar estudiante

                </a>

            </div>


            <!-- BOTÓN VOLVER -->

            <div class="mb-3">

                <a
                    href="cursos.php"
                    class="btn btn-outline-secondary"
                >

                    <i class="bi bi-arrow-left"></i>

                    Volver a cursos

                </a>

            </div>


            <!-- INFORMACIÓN DEL CURSO -->

            <div class="row mb-4">


                <div class="col-md-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="text-muted">

                                Curso

                            </div>

                            <h3 class="mb-0">

                                <?php

                                echo htmlspecialchars(
                                    $curso["nombre_curso"]
                                );

                                ?>

                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="text-muted">

                                Estudiantes

                            </div>

                            <h3 class="mb-0">

                                <?php

                                echo mysqli_num_rows(
                                    $resultado_estudiantes
                                );

                                ?>

                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="text-muted">

                                Estado

                            </div>

                            <h3 class="mb-0">

                                <?php

                                if (
                                    $curso["estado"]
                                    == "ACTIVO"
                                ) {

                                    echo '<span class="badge text-bg-success">
                                            ACTIVO
                                          </span>';

                                } else {

                                    echo '<span class="badge text-bg-secondary">
                                            INACTIVO
                                          </span>';

                                }

                                ?>

                            </h3>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TABLA DE ESTUDIANTES -->

            <div class="card shadow-sm border-0">

                <div class="card-body">


                    <h5 class="mb-3">

                        <i class="bi bi-people"></i>

                        Estudiantes del curso

                    </h5>


                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle"
                        >

                            <thead class="table-light">

                                <tr>

                                    <th>Documento</th>

                                    <th>Nombres</th>

                                    <th>Apellidos</th>

                                    <th>Estado</th>

                                    <th>Acciones</th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php if (
                                mysqli_num_rows(
                                    $resultado_estudiantes
                                ) > 0
                            ): ?>


                                <?php while (
                                    $estudiante =
                                    mysqli_fetch_assoc(
                                        $resultado_estudiantes
                                    )
                                ): ?>

                                    <tr>


                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $estudiante[
                                                    "documento"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $estudiante[
                                                    "nombres"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $estudiante[
                                                    "apellidos"
                                                ]
                                            );

                                            ?>

                                        </td>


                                        <td>

                                            <?php if (
                                                $estudiante[
                                                    "estado"
                                                ] == "ACTIVO"
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


                                        <td>


                                            <!-- EDITAR -->

                                            <a
                                                href="estudiante_editar.php?id=<?php echo $estudiante["id_estudiante"]; ?>&id_curso=<?php echo $id_curso; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Editar"
                                            >

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                            <!-- ESTADO -->

                                            <a
                                                href="estudiante_estado.php?id=<?php echo $estudiante["id_estudiante"]; ?>&id_curso=<?php echo $id_curso; ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Cambiar estado"
                                            >

                                                <i class="bi bi-arrow-repeat"></i>

                                            </a>


                                            <!-- QR -->

                                            <a
                                                href="estudiante_qr.php?id=<?php echo $estudiante["id_estudiante"]; ?>"
                                                class="btn btn-sm btn-outline-success"
                                                title="Ver QR"
                                            >

                                                <i class="bi bi-qr-code"></i>

                                            </a>


                                        </td>

                                    </tr>

                                <?php endwhile; ?>


                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center text-muted py-5"
                                    >

                                        <i
                                            class="bi bi-people fs-1"
                                        ></i>

                                        <p class="mt-3 mb-2">

                                            Este curso todavía no tiene estudiantes.

                                        </p>

                                        <a
                                            href="estudiante_nuevo.php?id_curso=<?php echo $id_curso; ?>"
                                            class="btn btn-primary"
                                        >

                                            <i class="bi bi-person-plus"></i>

                                            Agregar primer estudiante

                                        </a>

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