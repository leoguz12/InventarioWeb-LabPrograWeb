<?php
session_start();
require_once "php/conexion.php";

// solo el administrador (rol 1) puede entrar aqui
if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

// procesamos si quiere agregar o borrar categoria
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["accion"]) && $_POST["accion"] == "crear") {
        $nombre_cat = trim($_POST["nombre_categoria"]);
        if (!empty($nombre_cat)) {
            $stmt = $conn->prepare("INSERT INTO Categorias (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre_cat);
            if ($stmt->execute()) {
                $mensaje = "Categoría agregada con éxito.";
            }
            $stmt->close();
        }
    }
}

// procesar borrado por get (?borrar=id)
if (isset($_GET["borrar"])) {
    $id_cat = intval($_GET["borrar"]);
    // el borrado se ejecuta (si tiene productos asociados, la BD restringira o borrara segun FK)
    $stmt = $conn->prepare("DELETE FROM Categorias WHERE id_categoria = ?");
    $stmt->bind_param("i", $id_cat);
    $stmt->execute();
    $stmt->close();
    header("Location: crud_categorias.php");
    exit();
}

// consultamos todas las categorias
$resultado = $conn->query("SELECT * FROM Categorias ORDER BY id_categoria ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Inventario Web</title>
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
            <a href="php/logout.php">Salir (<?php echo htmlspecialchars($_SESSION["nombre"]); ?>)</a>
        </nav>
    </header>

    <div class="contenedor">
        <h2>Gestión de Categorías</h2>
        <p>Administra las clasificaciones de los productos del inventario.</p>

        <?php if ($mensaje != ""): ?>
            <div class="alerta alerta-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- Formulario para agregar categoria -->
        <form action="crud_categorias.php" method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <input type="hidden" name="accion" value="crear">
            <div class="grupo-form" style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 0;">
                <div style="flex: 1;">
                    <label for="nombre_categoria">Nombre de la nueva categoría:</label>
                    <input type="text" id="nombre_categoria" name="nombre_categoria" required placeholder="Ej. Herramientas">
                </div>
                <button type="submit" class="btn btn-verde">+ Agregar Nueva Categoría</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($cat = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo str_pad($cat["id_categoria"], 2, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($cat["nombre"]); ?></td>
                            <td>
                                <a href="crud_categorias.php?borrar=<?php echo $cat["id_categoria"]; ?>" class="btn btn-rojo" onclick="return confirmarEliminacion('categoría');">Borrar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No hay categorías registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="js/validaciones.js"></script>
</body>
</html>