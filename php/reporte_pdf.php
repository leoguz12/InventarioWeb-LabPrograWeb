<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    exit("Acceso no autorizado");
}

$productos = $conn->query("SELECT p.*, c.nombre AS categoria FROM Productos p INNER JOIN Categorias c ON p.id_categoria = c.id_categoria");
$total_val = $conn->query("SELECT SUM(precio * stock) AS valor FROM Productos")->fetch_assoc()["valor"] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario - PDF</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        h1, h2 { text-align: center; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #95a5a6; padding: 8px 12px; font-size: 13px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .totales { margin-top: 20px; font-size: 16px; font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <h1>Sistema de Control de Inventario</h1>
    <h2>Reporte General de Existencias y Valoración</h2>
    <p><strong>Fecha de emisión:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>
    <p><strong>Generado por:</strong> <?php echo htmlspecialchars($_SESSION["nombre"]); ?></p>
    <hr>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Producto</th>
                <th>Categoría</th>
                <th>Precio Unitario</th>
                <th>Stock</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $productos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo str_pad($p["id_producto"], 2, "0", STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($p["nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($p["categoria"]); ?></td>
                    <td>$<?php echo number_format($p["precio"], 2); ?></td>
                    <td><?php echo $p["stock"]; ?></td>
                    <td>$<?php echo number_format($p["precio"] * $p["stock"], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totales">
        Valor Total del Inventario: $<?php echo number_format($total_val, 2); ?>
    </div>

    <script>
        // abre automaticamente la ventana de impresion para guardar como PDF
        window.print();
    </script>
</body>
</html>