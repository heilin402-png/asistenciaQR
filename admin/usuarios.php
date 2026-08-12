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
   CONSULTAR USUARIOS
   ========================== */

$sql = "SELECT 
            u.id_usuario,
            u.nombres,
            u.apellidos,
            u.documento,
            u.correo,
            r.nombre_rol,
            u.estado,
            u.fecha_creacion
        FROM usuarios u
        INNER JOIN roles r 
            ON u.id_rol = r.id_rol
        ORDER BY u.id_usuario DESC";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error al consultar usuarios: " . mysqli_error($conexion));
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Usuarios - Asistencia QR</title>

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

        <a
            href="dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-qr-code-scan"></i>

            Sistema de Asistencia QR

        </a>


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


            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2>

                        Usuarios

                    </h2>

                    <p class="text-muted mb-0">

                        Administración de usuarios del sistema

                    </p>

                </div>


                <a
                    href="usuario_nuevo.php"
                    class="btn btn-primary"
                >

                    <i class="bi bi-person-plus"></i>

                    Nuevo usuario

                </a>

            </div>



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

                                    <th>Nombre</th>

                                    <th>Documento</th>

                                    <th>Correo</th>

                                    <th>Rol</th>

                                    <th>Estado</th>

                                    <th>Fecha creación</th>

                                    <th>Acciones</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php if (mysqli_num_rows($resultado) > 0): ?>

                                <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>

                                    <tr>

                                        <td>

                                            <?php echo $usuario["id_usuario"]; ?>

                                        </td>


                                        <td>

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    $usuario["nombres"] . " " . $usuario["apellidos"]
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <td>

                                            <?php
                                            echo htmlspecialchars($usuario["documento"]);
                                            ?>

                                        </td>


                                        <td>

                                            <?php
                                            echo htmlspecialchars($usuario["correo"]);
                                            ?>

                                        </td>


                                        <td>

                                            <?php
                                            echo htmlspecialchars($usuario["nombre_rol"]);
                                            ?>

                                        </td>


                                        <td>

                                            <?php if ($usuario["estado"] == "ACTIVO"): ?>

                                                <span class="badge text-bg-success">

                                                    ACTIVO

                                                </span>

                                            <?php else: ?>

                                                <span class="badge text-bg-secondary">

                                                    INACTIVO

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?php
                                            echo date(
                                                "d/m/Y H:i",
                                                strtotime($usuario["fecha_creacion"])
                                            );
                                            ?>

                                        </td>


                                        <td>

                                            <a
                                                href="usuario_editar.php?id=<?php echo $usuario["id_usuario"]; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Editar"
                                            >

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                            <?php if ($usuario["id_usuario"] != $_SESSION["id_usuario"]): ?>

                                                <a
                                                    href="usuario_estado.php?id=<?php echo $usuario["id_usuario"]; ?>"
                                                    class="btn btn-sm btn-outline-warning"
                                                    title="Cambiar estado"
                                                >

                                                    <i class="bi bi-arrow-repeat"></i>

                                                </a>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="8"
                                        class="text-center text-muted py-4"
                                    >

                                        No hay usuarios registrados.

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