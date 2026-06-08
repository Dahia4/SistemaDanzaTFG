<?php
session_start();
include 'conexion.php';

// Validar que el usuario esté logueado y que el mensaje no esté vacío
if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_emisor = $_SESSION['id'];
$id_receptor = intval($_POST['id_receptor']);
$mensaje = trim($_POST['mensaje']);

if (!empty($mensaje) && $id_receptor > 0) {
    $stmt = $conn->prepare("INSERT INTO chat_mensajes (id_emisor, id_receptor, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_emisor, $id_receptor, $mensaje);
    $stmt->execute();
    $stmt->close();
}

// Redirección de regreso para mantener la conversación abierta
if ($_SESSION['rol'] === 'profesor') {
    // Si eres profesor, vuelves manteniendo abierto el chat con ese alumno
    header("Location: chat.php?con_alumno=" . $id_receptor);
} else {
    // Si eres alumno, vuelves manteniendo abierto el chat con ese profesor
    header("Location: chat.php?con_profe=" . $id_receptor);
}
exit;
?>