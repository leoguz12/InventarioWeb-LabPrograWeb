<?php
session_start();
require_once "php/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

// agregar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && $_POST["accion"] == "crear_prod") {
    $nombre = trim($_POST["nombre"]);
    $precio = floatval($_POST["precio"]);
    $stock = intval($_POST["stock"]);
    $id_categoria = intval($_POST["id_categoria"]);

    if (!empty($nombre) && $precio > 0 && $id_categoria > 0) {
        $stmt = $conn->prepare("INSERT INTO Productos (nombre, precio, stock, id_categoria) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdii", $nombre, $precio, $stock, $id_categoria);
        if ($stmt->execute()) {
            $mensaje = "Producto registrado correctamente.";
        }
        $stmt->close();
    }
}

// borrar producto
if (isset($_GET["borrar"])) {
    $id_prod = intval($_GET["borrar"]);
    $stmt = $conn->prepare("DELETE FROM Productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_prod);
    $stmt->execute();
    $stmt->close();
    header("Location: crud_productos.php");
    exit();
}

// traemos productos y categorias para los desplegables
$lista_cats = $conn->query("SELECT * FROM Categorias");
$productos = $conn->query("SELECT p.*, c.nombre AS categoria FROM Productos p INNER JOIN Categorias c ON p.id_categoria = c.id_categoria ORDER BY p.id_producto ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Inventario Web</title>
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
        <h2>Gestión de Productos</h2>
        <p>Alta, consulta y control de existencias del inventario general.</p>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- Formulario de alta -->
        <form action="crud_productos.php" method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <input type="hidden" name="accion" value="crear_prod">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 10px; align-items: flex-end;">
                <div class="grupo-form" style="margin-bottom:0;">
                    <label>Nombre del producto:</label>
                    <input type="text" name="nombre" required placeholder="Ej. Cable HDMI">
                </div>
                <div class="grupo-form" style="margin-bottom:0;">
                    <label>Precio ($):</label>
                    <input type="number" step="0.01" name="precio" required placeholder="0.00">
                </div>
                <div class="grupo-form" style="margin-bottom:0;">
                    <label>Stock inicial:</label>
                    <input type="number" name="stock" required placeholder="0">
                </div>
                <div class="grupo-form" style="margin-bottom:0;">
                    <label>Categoría:</label>
                    <select name="id_categoria" required>
                        <option value="">Selecciona...</option>
                        <?php while($c = $lista_cats->fetch_assoc()): ?>
                            <option value="<?php echo $c["id_categoria"]; ?>"><?php echo htmlspecialchars($c["nombre"]); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-verde">+ Agregar</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($productos && $productos->num_rows > 0): ?>
                    <?php while ($p = $productos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo str_pad($p["id_producto"], 2, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($p["nombre"]); ?></td>
                            <td><?php echo htmlspecialchars($p["categoria"]); ?></td>
                            <td>$<?php echo number_format($p["precio"], 2); ?></td>
                            <td><?php echo $p["stock"]; ?></td>
                            <td>
                                <a href="crud_productos.php?borrar=<?php echo $p["id_producto"]; ?>" class="btn btn-rojo" onclick="return confirmarEliminacion('producto');">Borrar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay productos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="js/validaciones.js"></script>
</body>
</html>