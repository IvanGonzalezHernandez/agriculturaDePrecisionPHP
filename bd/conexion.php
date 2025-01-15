<?php
// Conectar con el servidor de base de datos
$conexion = mysqli_connect("localhost", "root", "");

// Seleccionar base de datos
mysqli_select_db($conexion, "agriculturaDePrecision")
        or die("No se puede seleccionar la base de datos");
?>
