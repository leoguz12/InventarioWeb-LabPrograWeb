<?php
session_start();
require_once "php/conexion.php";

// verificamos que haya iniciado sesion (puede entrar Cliente o Admin)
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

// traemos el limite de stock critico configurado en la BD
$res_config = $conn->query("SELECT limite_stock_critico FROM Configuracion_Sistema WHERE id_config = 1");
$limite_stock = 10;
if ($fila_cfg = $res_config->fetch_assoc()) {
    $limite_stock = $fila_cfg["limite_stock_critico"];
}

// consultamos los productos junto con el nombre de su categoria
$sql = "SELECT p.id_producto, p.nombre, c.nombre AS categoria, p.precio, p.stock 
        FROM Productos p 
        INNER JOIN Categorias c ON p.id_categoria = c.id_categoria 
        ORDER BY p.id_producto ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Inventario - Inventario Web</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

  <header>
        <div class="logo">
            <?php echo ($_SESSION["id_rol"] == 1) ? "Panel Administrador" : "Panel Cliente"; ?>
        </div>
        <nav>
            <!-- ENLACE NUEVO AL INICIO -->
            <a href="index.html">Inicio</a>
            <a href="catalogo.php">Catálogo</a>
            <a href="contacto.html" target="_blank">Reportar / Soporte</a>

            <?php if ($_SESSION["id_rol"] == 1): ?>
                <a href="crud_categorias.php">Categorías</a>
                <a href="crud_productos.php">Productos</a>
                <a href="ajustes.php">Reportes y Ajustes</a>
            <?php endif; ?>
            <a href="php/logout.php">Cerrar Sesión (<?php echo htmlspecialchars($_SESSION["nombre"]); ?>)</a>
        </nav>
    </header>

    <div class="contenedor">
        <h2>Disponibilidad de Inventario</h2>
        <p>Consulta en tiempo real los productos registrados, sus precios y existencias.</p>

        <!-- Barra de busqueda (filtra al escribir y al dar clic en Buscar) -->
        <div class="grupo-form" style="display: flex; gap: 10px;">
            <input type="text" id="buscador" onkeyup="filtrarCatalogo()" placeholder="Buscar producto por nombre o categoría...">
            <button type="button" class="btn" onclick="filtrarCatalogo()">Buscar</button>
        </div>

        <table id="tabla-productos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock Disponible</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($prod = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo str_pad($prod["id_producto"], 2, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($prod["nombre"]); ?></td>
                            <td><?php echo htmlspecialchars($prod["categoria"]); ?></td>
                            <td>$<?php echo number_format($prod["precio"], 2); ?></td>
                            <td>
                                <?php if ($prod["stock"] <= $limite_stock): ?>
                                    <span class="stock-bajo"><?php echo $prod["stock"]; ?> (Stock bajo)</span>
                                <?php else: ?>
                                    <?php echo $prod["stock"]; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay productos registrados en el inventario.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="js/validaciones.js"></script>
</body>
</html>