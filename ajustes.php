<?php
session_start();
require_once "php/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

// actualizar configuracion de stock critico si se envio el form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nuevo_limite"])) {
    $nuevo_limite = intval($_POST["nuevo_limite"]);
    $stmt = $conn->prepare("UPDATE Configuracion_Sistema SET limite_stock_critico = ? WHERE id_config = 1");
    $stmt->bind_param("i", $nuevo_limite);
    if ($stmt->execute()) {
        $mensaje = "Umbral de stock crítico actualizado correctamente.";
    }
    $stmt->close();
}

// procesar borrado de un mensaje de soporte (?borrar_soporte=id)
if (isset($_GET["borrar_soporte"])) {
    $id_soporte = intval($_GET["borrar_soporte"]);
    $stmt = $conn->prepare("DELETE FROM Mensajes_Soporte WHERE id_mensaje = ?");
    $stmt->bind_param("i", $id_soporte);
    $stmt->execute();
    $stmt->close();
    header("Location: ajustes.php");
    exit();
}

// metricas para las tarjetas del dashboard
$res_tot = $conn->query("SELECT COUNT(*) AS total, SUM(precio * stock) AS valor FROM Productos");
$datos_tot = $res_tot->fetch_assoc();
$total_productos = $datos_tot["total"] ?? 0;
$valor_inventario = $datos_tot["valor"] ?? 0;

// limite actual y productos con stock bajo
$cfg = $conn->query("SELECT limite_stock_critico FROM Configuracion_Sistema WHERE id_config = 1")->fetch_assoc();
$limite_critico = $cfg["limite_stock_critico"] ?? 10;

$alertas = $conn->query("SELECT COUNT(*) AS pocas FROM Productos WHERE stock <= $limite_critico")->fetch_assoc()["pocas"] ?? 0;

// mensajes de soporte recibidos
$mensajes_soporte = $conn->query("SELECT * FROM Mensajes_Soporte ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Ajustes - Inventario Web</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <div class="logo">Panel Administrador</div>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="catalogo.php">Catálogo</a>
            <a href="crud_categorias.php">Categorías</a>
            <a href="crud_productos.php">Productos</a>
            <a href="ajustes.php">Reportes y Ajustes</a>
            <a href="php/logout.php">Salir</a>
        </nav>
    </header>

    <div class="contenedor">
        <h2>Reportes y Ajustes del Sistema</h2>
        <p>Panel integral de métricas, bandeja de soporte y configuración global.</p>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- Tarjetas de metricas (Pantalla 8) -->
        <div class="tarjetas-resumen">
            <div class="tarjeta">
                <h3>Total Productos</h3>
                <div class="numero"><?php echo $total_productos; ?></div>
            </div>
            <div class="tarjeta">
                <h3>Valor del Inventario</h3>
                <div class="numero">$<?php echo number_format($valor_inventario, 2); ?></div>
            </div>
            <div class="tarjeta">
                <h3>Alertas de Stock</h3>
                <div class="numero stock-bajo"><?php echo $alertas; ?></div>
            </div>
        </div>

        <div style="text-align: center; margin: 25px 0;">
            <a href="php/reporte_pdf.php" target="_blank" class="btn" style="font-size: 16px; padding: 12px 24px;">Exportar Reporte General a PDF</a>
        </div>

        <hr><br>

        <h3>Bandeja de Mensajes de Soporte</h3>
        <p>Consultas y comentarios enviados por los usuarios desde la pantalla de contacto.</p>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Mensaje</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($mensajes_soporte && $mensajes_soporte->num_rows > 0): ?>
                    <?php while($m = $mensajes_soporte->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $m["fecha"]; ?></td>
                            <td><?php echo htmlspecialchars($m["nombre"]); ?></td>
                            <td><?php echo htmlspecialchars($m["correo"]); ?></td>
                            <td><?php echo htmlspecialchars($m["mensaje"]); ?></td>
                            <td>
                                <a href="php/reporte_soporte_pdf.php?id=<?php echo $m["id_mensaje"]; ?>" target="_blank" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #2980b9;">PDF</a>
                                <a href="ajustes.php?borrar_soporte=<?php echo $m["id_mensaje"]; ?>" class="btn btn-rojo" onclick="return confirmarEliminacion('reporte de soporte');">Borrar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay mensajes de soporte registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br><hr><br>

        <h3>Ajustes del Sistema</h3>
        <form action="ajustes.php" method="POST" style="max-width: 400px; background: #f8f9fa; padding: 15px; border-radius: 4px;">
            <div class="grupo-form">
                <label for="nuevo_limite">Límite para Alertas de Stock Crítico:</label>
                <input type="number" id="nuevo_limite" name="nuevo_limite" value="<?php echo $limite_critico; ?>" required min="1">
            </div>
            <button type="submit" class="btn">Actualizar Umbral</button>
        </form>
    </div>

    <script src="js/validaciones.js"></script>
</body>
</html>