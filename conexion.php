<?php
$host = "localhost";
$usuario = "root";
$password = "";
$bd = "stockcontrol_db";

$conexion = new mysqli($host, $usuario, $password, $bd);

if ($conexion->connect_error) {
    die("Error de conexión a MySQL: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>