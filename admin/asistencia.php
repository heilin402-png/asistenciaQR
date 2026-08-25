<?php

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
   FECHA ACTUAL
   ========================== */

$fecha = date("Y-m-d");

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Asistencia - Asistencia QR</title>

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
                    href="asistencia.php"
                    class="nav-link active mb-2"
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

                    <i class="bi bi-clipboard-check"></i>

                    Tomar asistencia

                </h2>

                <p class="text-muted">

                    Registro de asistencia mediante código QR.

                </p>

            </div>


            <div class="row justify-content-center">

                <div class="col-md-7">


                    <div class="card shadow-sm border-0">

                        <div class="card-body p-5 text-center">


                            <i
                                class="bi bi-qr-code-scan display-1 text-primary">
                            </i>


                            <h3 class="mt-3">

                                Escanear código QR

                            </h3>


                            <p class="text-muted">

                                Coloca el código QR del estudiante
                                frente a la cámara.

                            </p>


                            <div
                                class="alert alert-info"
                            >

                                <i class="bi bi-calendar3"></i>

                                Fecha:

                                <strong>

                                    <?php

                                    echo date(
                                        "d/m/Y"
                                    );

                                    ?>

                                </strong>

                            </div>


                            <!-- ÁREA DEL ESCÁNER -->

                            <div
                                id="lector"
                                class="border rounded p-5 mb-3"
                            >

                                <i
                                    class="bi bi-camera display-4">
                                </i>

                                <p class="mt-2 mb-0">

                                    El lector QR aparecerá aquí.

                                </p>

                            </div>


                            <button
                                type="button"
                                class="btn btn-primary btn-lg"
                                id="iniciar"
                            >

                                <i class="bi bi-camera"></i>

                                Iniciar escáner

                            </button>


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
<script src="https://unpkg.com/html5-qrcode"></script>

<script>

const boton = document.getElementById("iniciar");

let lectorQR = null;

boton.addEventListener("click", function () {

    if (lectorQR !== null) {
        return;
    }

    lectorQR = new Html5Qrcode("lector");

    lectorQR.start(
        { facingMode: "environment" },

        {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        },

        function (textoQR) {

            console.log("QR leído:", textoQR);

            alert("QR leído correctamente:\n" + textoQR);

            lectorQR.stop().then(function () {

                lectorQR.clear();

                lectorQR = null;

            });

        },

        function (error) {

            // No hacemos nada mientras busca el QR

        }
    )
    .catch(function (error) {

        alert(
            "No se pudo iniciar la cámara.\n\n" +
            "Verifica que el navegador tenga permiso para usarla."
        );

        lectorQR = null;

    });

});

</script>


</body>

</html>