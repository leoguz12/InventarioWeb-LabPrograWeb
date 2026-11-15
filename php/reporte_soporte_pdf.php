<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1 || !isset($_GET["id"])) {
    exit("Acceso no autorizado");
}

$id_mensaje = intval($_GET["id"]);
$stmt = $conn->prepare("SELECT * FROM Mensajes_Soporte WHERE id_mensaje = ?");
$stmt->bind_param("i", $id_mensaje);
$stmt->execute();
$resultado = $stmt->get_result();
$soporte = $resultado->fetch_assoc();
$stmt->close();
$conn->close();

if (!$soporte) {
    exit("Reporte no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Soporte #<?php echo str_pad($soporte["id_mensaje"], 3, "0", STR_PAD_LEFT); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        h1, h2 { text-align: center; color: #2c3e50; }
        .caja-reporte { border: 1px solid #bdc3c7; background: #f8f9fa; padding: 25px; border-radius: 6px; margin-top: 20px; }
        .campo { margin-bottom: 15px; }
        .campo label { font-weight: bold; display: block; color: #2c3e50; margin-bottom: 5px; }
        .campo p { background: #fff; padding: 10px; border: 1px solid #dcdde1; border-radius: 4px; margin: 0; }
    </style>
</head>
<body>
    <h1>Sistema de Control de Inventario</h1>
    <h2>Detalle de Reporte de Soporte Técnico</h2>
    <hr>

    <div class="caja-reporte">
        <div class="campo">
            <label>ID de Reporte:</label>
            <p>#<?php echo str_pad($soporte["id_mensaje"], 3, "0", STR_PAD_LEFT); ?></p>
        </div>
        <div class="campo">
            <label>Fecha y Hora de Recepción:</label>
            <p><?php echo $soporte["fecha"]; ?></p>
        </div>
        <div class="campo">
            <label>Nombre del Cliente:</label>
            <p><?php echo htmlspecialchars($soporte["nombre"]); ?></p>
        </div>
        <div class="campo">
            <label>Correo Electrónico:</label>
            <p><?php echo htmlspecialchars($soporte["correo"]); ?></p>
        </div>
        <div class="campo">
            <label>Mensaje o Descripción del Problema:</label>
            <p><?php echo nl2br(htmlspecialchars($soporte["mensaje"])); ?></p>
        </div>
    </div>

    <script>
        // abre la ventana de impresion para guardar como PDF de inmediato
        window.print();
    </script>
</body>
</html>