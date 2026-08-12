<?php
/*
|--------------------------------------------------------------------------
| SISTEMA DE ASISTENCIA QR ESCOLAR
|--------------------------------------------------------------------------
| Archivo: conexion.php
| Descripción: Realiza la conexión con la base de datos MySQL.
|--------------------------------------------------------------------------
*/

/* Datos de conexión */
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "asistencia_qr_escolar";

/* Crear conexión */
$conexion = mysqli_connect($servidor, $usuario, $contrasena, $basedatos);

/* Verificar conexión */
if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

/* Configurar caracteres UTF-8 */
mysqli_set_charset($conexion, "utf8");

?>