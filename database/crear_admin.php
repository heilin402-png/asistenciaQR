<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Inicio</h2>";

if(file_exists("../config/conexion.php")){

    echo "Encontró conexion.php <br>";

}else{

    echo "NO encontró conexion.php <br>";

}

require_once("../config/conexion.php");

if(isset($conexion)){

    echo "La variable \$conexion existe.";

}else{

    echo "La variable \$conexion NO existe.";

}
