<?php
// datos de conexion local para xampp
$servidor = "localhost";
$usuario = "root";
$password = "Guzle_120607"; // por defecto va vacia
$base_datos = "inventario_db";

// creamos conexion
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// checamos si hubo algun error al conectar
if ($conn->connect_error) {
    die("Error de conexion con la base de datos: " . $conn->connect_error);
}

// para que agarre bien los acentos y la ñ
$conn->set_charset("utf8mb4");
?>