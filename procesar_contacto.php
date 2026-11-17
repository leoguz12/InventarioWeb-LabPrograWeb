<?php
require_once "conexion.php";

// checamos que los datos vengan por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $mensaje = trim($_POST["mensaje"]);

    if (!empty($nombre) && !empty($correo) && !empty($mensaje)) {
        // la fecha se guarda sola porque en la BD tiene DEFAULT CURRENT_TIMESTAMP
        $stmt = $conn->prepare("INSERT INTO Mensajes_Soporte (nombre, correo, mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $correo, $mensaje);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
// regresamos a contacto.html avisando que ya se mando
header("Location: ../contacto.html?enviado=1");
exit();
?>