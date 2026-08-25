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

/* Verificar que sea administrador */
if ($_SESSION["id_rol"] != 1) {
    header("Location: ../docente/dashboard.php");
    exit();
}


/* ==============================
   ESTADÍSTICAS DEL SISTEMA
   ============================== */

/* Usuarios activos */
$sql_usuarios = "SELECT COUNT(*) AS total 
                 FROM usuarios 
                 WHERE estado = 'ACTIVO'";

$resultado_usuarios = mysqli_query($conexion, $sql_usuarios);
$usuarios = mysqli_fetch_assoc($resultado_usuarios);
$total_usuarios = $usuarios["total"];


/* Estudiantes activos */
$sql_estudiantes = "SELECT COUNT(*) AS total 
                    FROM estudiantes 
                    WHERE estado = 'ACTIVO'";

$resultado_estudiantes = mysqli_query($conexion, $sql_estudiantes);
$estudiantes = mysqli_fetch_assoc($resultado_estudiantes);
$total_estudiantes = $estudiantes["total"];


/* Cursos activos */
$sql_cursos = "SELECT COUNT(*) AS total 
               FROM cursos 
               WHERE estado = 'ACTIVO'";

$resultado_cursos = mysqli_query($conexion, $sql_cursos);
$cursos = mysqli_fetch_assoc($resultado_cursos);
$total_cursos = $cursos["total"];


/* Asistencias de hoy */
$sql_asistencias = "SELECT COUNT(*) AS total 
                    FROM asistencia_clase ac
                    WHERE DATE(ac.hora_registro) = CURDATE()";

$resultado_asistencias = mysqli_query($conexion, $sql_asistencias);
$asistencias = mysqli_fetch_assoc($resultado_asistencias);
$total_asistencias = $asistencias["total"];


/* Almuerzos de hoy */
$sql_almuerzos = "SELECT COUNT(*) AS total 
                  FROM asistencia_restaurante
                  WHERE fecha = CURDATE()";

$resultado_almuerzos = mysqli_query($conexion, $sql_almuerzos);
$almuerzos = mysqli_fetch_assoc($resultado_almuerzos);
$total_almuerzos = $almuerzos["total"];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard - Asistencia QR</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/estilos.css">

</head>


<body class="bg-light">


<!-- ==========================
     BARRA SUPERIOR
     ========================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid">

        <span class="navbar-brand mb-0 h1">

            <i class="bi bi-qr-code-scan"></i>

            Sistema de Asistencia QR

        </span>


        <span class="text-white">

            <i class="bi bi-person-circle"></i>

            <?php echo htmlspecialchars($_SESSION["nombre"]); ?>

        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">


        <!-- ==========================
             MENÚ LATERAL
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
                    class="nav-link active mb-2"
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

                    Dashboard

                </h2>

                <p class="text-muted">

                    Resumen general del sistema

                </p>

            </div>



            <!-- TARJETAS -->

            <div class="row g-4">


                <!-- USUARIOS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Usuarios activos

                                    </h6>

                                    <h2>

                                        <?php echo $total_usuarios; ?>

                                    </h2>

                                </div>

                                <i class="bi bi-people fs-1 text-primary"></i>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ESTUDIANTES -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Estudiantes activos

                                    </h6>

                                    <h2>

                                        <?php echo $total_estudiantes; ?>

                                    </h2>

                                </div>

                                <i class="bi bi-mortarboard fs-1 text-success"></i>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- CURSOS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Cursos activos

                                    </h6>

                                    <h2>

                                        <?php echo $total_cursos; ?>

                                    </h2>

                                </div>

                                <i class="bi bi-book fs-1 text-warning"></i>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ASISTENCIAS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Asistencias hoy

                                    </h6>

                                    <h2>

                                        <?php echo $total_asistencias; ?>

                                    </h2>

                                </div>

                                <i class="bi bi-clipboard-check fs-1 text-info"></i>

                            </div>

                        </div>

                    </div>

                </div>


            </div>



            <!-- ALMUERZOS -->

            <div class="row mt-4">

                <div class="col-md-6">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Almuerzos registrados hoy

                                    </h6>

                                    <h2>

                                        <?php echo $total_almuerzos; ?>

                                    </h2>

                                </div>

                                <i class="bi bi-cup-hot fs-1 text-danger"></i>

                            </div>

                        </div>

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