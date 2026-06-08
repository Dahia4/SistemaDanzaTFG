<?php
session_start();

// 1. Eliminamos los datos del usuario actual para cerrar su sesión de forma segura
unset($_SESSION['id']);
unset($_SESSION['nombre']);
unset($_SESSION['rol']);
unset($_SESSION['cedula']);

// 2. Guardamos el mensaje que leerá el archivo index.php (Login)
$_SESSION['logout_exito'] = "Cerraste sesión correctamente. ¡Hasta luego!";

// 3. Redirigimos al login
header("Location: index.php");
exit;
?>
