<?php
// Datos de conexión
$host = "localhost";       // localhost con XAMPP
$usuario = "root";         // usuario por defecto en XAMPP
$contraseña = "";          // contraseña por defecto en XAMPP es vacía
$basededatos = "biblioteca_danza";  // el nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contraseña, $basededatos);

// Revisar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

?>
