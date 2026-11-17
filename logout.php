<?php
session_start();
// limpiamos todas las variables de sesion y la destruimos
session_unset();
session_destroy();

// regresamos al login
header("Location: ../login.php");
exit();
?>