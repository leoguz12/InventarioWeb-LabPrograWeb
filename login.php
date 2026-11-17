<?php
session_start();
require_once "php/conexion.php";

// si ya tiene sesion iniciada, lo mandamos a su pantalla correspondiente
if (isset($_SESSION["id_usuario"])) {
    if ($_SESSION["id_rol"] == 1) {
        header("Location: crud_categorias.php");
    } else {
        header("Location: catalogo.php");
    }
    exit();
}

$mensaje_error = "";
$mensaje_exito = "";

// procesamos el formulario dependiendo de si le dio a Entrar o a Registrarse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"];

    // --- CASO 1: INICIAR SESION ---
    if ($accion == "login") {
        $correo = trim($_POST["correo"]);
        $password = trim($_POST["password"]);

        $stmt = $conn->prepare("SELECT id_usuario, nombre, password, id_rol FROM Usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($usuario = $resultado->fetch_assoc()) {
            // checamos contraseña (soporta texto plano del seed.sql o hash de registros nuevos)
            if ($password === $usuario["password"] || password_verify($password, $usuario["password"])) {
                $_SESSION["id_usuario"] = $usuario["id_usuario"];
                $_SESSION["nombre"] = $usuario["nombre"];
                $_SESSION["id_rol"] = $usuario["id_rol"];

                // redireccion segun el rol 
                if ($usuario["id_rol"] == 1) {
                    header("Location: crud_categorias.php");
                } else {
                    header("Location: catalogo.php");
                }
                exit();
            } else {
                $mensaje_error = "Contraseña incorrecta. Intenta de nuevo.";
            }
        } else {
            $mensaje_error = "No existe ninguna cuenta con ese correo.";
        }
        $stmt->close();
    }

    // --- CASO 2: REGISTRO DE NUEVO CLIENTE ---
    if ($accion == "registro") {
        $nombre = trim($_POST["nombre_reg"]);
        $correo = trim($_POST["correo_reg"]);
        $password = trim($_POST["password_reg"]);
        $rol_cliente = 2; // por seguridad, los registros publicos siempre son Cliente

        if (!empty($nombre) && !empty($correo) && !empty($password)) {
            // checamos que el correo no este repetido
            $check = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE correo = ?");
            $check->bind_param("s", $correo);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $mensaje_error = "Ese correo ya está registrado. Intenta iniciar sesión.";
            } else {
                $pass_segura = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO Usuarios (nombre, correo, password, id_rol) VALUES (?, ?, ?, ?)");
                $insert->bind_param("sssi", $nombre, $correo, $pass_segura, $rol_cliente);

                if ($insert->execute()) {
                    $mensaje_exito = "Cuenta creada correctamente. Ya puedes iniciar sesión arriba.";
                } else {
                    $mensaje_error = "Hubo un error al crear la cuenta.";
                }
                $insert->close();
            }
            $check->close();
        } else {
            $mensaje_error = "Llena todos los campos para registrarte.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Inventario Web</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <div class="logo">Inventario Web</div>
        <nav>
            <a href="index.html">Volver al Inicio</a>
            <a href="politicas.html">Políticas</a>
            <a href="contacto.html">Contacto</a>
        </nav>
    </header>

    <div class="contenedor" style="max-width: 480px;">
        <h2>Iniciar Sesión</h2>
        <p>Ingresa tus credenciales para acceder al sistema.</p>

        <?php if ($mensaje_error != ""): ?>
            <div class="alerta alerta-error"><?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <?php if ($mensaje_exito != ""): ?>
            <div class="alerta alerta-exito"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" onsubmit="return validarLogin();">
            <input type="hidden" name="accion" value="login">

            <div class="grupo-form">
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" placeholder="ejemplo@inventario.com">
            </div>

            <div class="grupo-form">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="••••••••">
            </div>

            <button type="submit" class="btn" style="width: 100%;">Entrar</button>
        </form>

        <br><hr><br>

        <h3>¿No tienes cuenta? Regístrate como Cliente</h3>
        <form action="login.php" method="POST">
            <input type="hidden" name="accion" value="registro">

            <div class="grupo-form">
                <label for="nombre_reg">Nombre completo:</label>
                <input type="text" id="nombre_reg" name="nombre_reg" required placeholder="Tu nombre">
            </div>

            <div class="grupo-form">
                <label for="correo_reg">Correo electrónico:</label>
                <input type="email" id="correo_reg" name="correo_reg" required placeholder="tu@correo.com">
            </div>

            <div class="grupo-form">
                <label for="password_reg">Crea una contraseña:</label>
                <input type="password" id="password_reg" name="password_reg" required placeholder="Mínimo 6 caracteres">
            </div>

            <button type="submit" class="btn btn-verde" style="width: 100%;">Crear Cuenta</button>
        </form>
    </div>

    <script src="js/validaciones.js"></script>
</body>
</html>